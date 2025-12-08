<?php

namespace App\Src\Core\Reporters;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;

class Reporter{
    private static ?self $instance = null;

    private array $created = [
        'Directories' => [],
        'Units'       => [],
        'Files'       => [],
    ];

    private ComponentContextDTO $componentContextDTO;

    private array $skipped = [];
    private array $errors   = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ---------------------------------------------------------
    // Logging
    // ---------------------------------------------------------
    
    public function logCreated(string $type, string $name): void
    {
        if (!isset($this->created[$type])) {
            $this->created[$type] = [];
        }
        $this->created[$type][] = $name;
    }

    public function logSkipped(string $type, string $name, ?string $reason = null): void
    {
        $this->skipped[] = compact('type', 'name', 'reason');
    }

    public function logError(string $type, string $name, string $message): void
    {
        $this->errors[] = compact('type', 'name', 'message');
    }

    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    // ---------------------------------------------------------
    // Reporting
    // ---------------------------------------------------------
    
    public function report($components): void
    {
        $this->loadContexts();

        $components->info("'{$this->componentContextDTO->componentName}' Scaffolded Successfully!");
        $components->twoColumnDetail('📂 Path', $this->componentContextDTO->componentPath);

        // foreach ($this->created as $type => $names) {
        //     if (!empty($names)) {
        //         $rows = array_map(fn($n) => [$n], $names);
        //         $components->table(
        //             [match($type) {
        //                 'Directories' => '📁 Directories',
        //                 'Units'       => '📦 Units',
        //                 'Files'       => '🧩 Files',
        //                 default       => $type
        //             }],
        //             $rows
        //         );
        //     }
        // }

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