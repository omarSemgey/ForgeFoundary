<?php

namespace App\Src\Core\Reporters;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;

// ===============================================
// Class: Reporter
// Purpose: Singleton service responsible for tracking and reporting
//          the creation, skipping, and errors of scaffolded components.
//          Provides structured output to CLI via the report() method.
// Functions:
//   - getInstance(): returns the singleton instance
//   - logCreated(): logs a created directory/unit/file
//   - logSkipped(): logs skipped items with optional reason
//   - logError(): logs errors with messages
//   - report(): outputs a full summary to the CLI
//   - reset(): clears all tracking arrays after reporting
// ===============================================
class Reporter{
    private static ?self $instance = null;

    // Tracks what was successfully created
    private array $created = [
        'Directories' => [],
        'Units'       => [],
        'Files'       => [],
    ];

    // Stores the component context (name, path, etc.)
    private ComponentContextDTO $componentContextDTO;

    // Tracks skipped items and errors
    private array $skipped = [];
    private array $errors   = [];

    private function __construct() {}

    // ===============================================
    // Function: getInstance
    // Inputs: none
    // Outputs: self (singleton instance of Reporter)
    // Purpose: Provides access to the single Reporter instance
    // Logic:
    //   - If instance doesn't exist, create it
    //   - Return the instance
    // Side Effects: none
    // External functions/helpers: none
    // ===============================================
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ===============================================
    // Logging Functions
    // -----------------------------------------------
    // logCreated
    // Inputs: string $type (Directories|Units|Files), string $name
    // Outputs: void
    // Purpose: Adds the created item to the internal tracker
    // Side Effects: modifies $created array
    // ===============================================
    public function logCreated(string $type, string $name): void
    {
        if (!isset($this->created[$type])) {
            $this->created[$type] = [];
        }
        $this->created[$type][] = $name;
    }

    // -----------------------------------------------
    // logSkipped
    // Inputs: string $type, string $name, ?string $reason
    // Outputs: void
    // Purpose: Logs a skipped item with optional reason
    // Side Effects: modifies $skipped array
    // ===============================================
    public function logSkipped(string $type, string $name, ?string $reason = null): void
    {
        $this->skipped[] = compact('type', 'name', 'reason');
    }

    // -----------------------------------------------
    // logError
    // Inputs: string $type, string $name, string $message
    // Outputs: void
    // Purpose: Logs an error with a descriptive message
    // Side Effects: modifies $errors array
    // ===============================================
    public function logError(string $type, string $name, string $message): void
    {
        $this->errors[] = compact('type', 'name', 'message');
    }

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads ComponentContextDTO from ContextBus singleton
    // Side Effects: sets $componentContextDTO
    // External Functions/Helpers Used:
    //   - ContextBus()->get(ComponentContextDTO::class)
    //   - Debugger()->info()
    // ===============================================
    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    // ===============================================
    // Reporting
    // -----------------------------------------------
    // report
    // Inputs: $components (CLI output helper object)
    // Outputs: void
    // Purpose: Displays the full report of scaffolded items
    // Logic Walkthrough:
    //   1. Load the current component context
    //   2. Output main scaffold success message
    //   3. Display component path
    //   4. (Optional) Display table of created Directories/Units/Files
    //   5. Display skipped items with reasons
    //   6. Display errors with messages
    //   7. Reset internal state for next report
    // Side Effects:
    //   - Outputs to CLI via $components
    //   - Clears internal tracking arrays after reporting
    // External Functions/Helpers Used:
    //   - $this->loadContexts()
    // ===============================================
    public function report($components): void
    {
        $this->loadContexts();

        $components->info("'{$this->componentContextDTO->componentName}' Scaffolded Successfully!");
        $components->twoColumnDetail('📂 Path', $this->componentContextDTO->componentPath);

        // Skipped / errors (optional)
        if (!empty($this->skipped)) {
            $components->line("\n⚠️ Skipped:");
            foreach ($this->skipped as $s) {
                $reason = $s['reason'] ?? 'No reason';
                $components->line("  - [{$s['type']}] {$s['name']} ({$reason})");
            }
        }

        if (!empty($this->errors)) {
            $components->line("\n❌ Errors:");
            foreach ($this->errors as $e) {
                $components->line("  - [{$e['type']}] {$e['name']} ({$e['message']})");
            }
        }

        $this->reset();
    }

    // ===============================================
    // Function: reset
    // Inputs: none
    // Outputs: void
    // Purpose: Clears all internal tracking arrays for next reporting
    // Side Effects: resets $created, $skipped, $errors arrays
    // ===============================================
    private function reset(): void
    {
        $this->created = [
            'Directories' => [],
            'Units'       => [],
            'Files'       => [],
        ];
        $this->skipped = [];
        $this->errors  = [];
    }
}
