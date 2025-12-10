<?php

namespace App\Src\Commands;

use App\Src\Commands\Traits\HandlesUserErrors;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\Helpers\PathManager;
use Symfony\Component\Yaml\Yaml;

// ===============================================
// Class: DumbModesCommand
// Purpose: CLI command to dump all modes available in ForgeFoundary. 
//          Retrieves modes from configured directories and prints their metadata.
// Functions:
//   - __construct(): injects Bootstrapper and PathManager
//   - handle(): main command execution sequence
//   - loadContext(): loads ConfigContextDTO from ContextBus
//   - resolveModes(): resolves all modes and their metadata
//   - resolveModesNames(): finds all YAML mode files in the given modesPath
//   - resolveModeMetadata(): parses YAML file and extracts metadata
//   - printModes(): prints resolved modes in a formatted tree-like structure
// ===============================================
class DumbModesCommand extends Command
{
    use HandlesUserErrors;
    // Command signature and description
    protected $signature = 'dumb-modes {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log}';
    protected $description = 'Dump all the modes available for the ForgeFoundry command';

    // Config context loaded from ContextBus
    private ConfigContextDTO $configContextDTO;

    // YAML key to extract mode metadata
    private const DUMB_MODES_COMMAND_CONFIG_KEYS = [
        "mode_metadata" => "mode_metadata"
    ];

    // Stores modes (name => file path)
    private array $modes; 
    private array $resolvedModes;

    public function __construct(
        private Bootstrapper $bootstrapper,
        private PathManager $pathManager,
    )
    {
        parent::__construct();
    }

    // ===============================================
    // Function: loadContext
    // Inputs: none
    // Outputs: void
    // Purpose: Loads ConfigContextDTO from ContextBus
    // Logic Walkthrough:
    //   1. Retrieves ConfigContextDTO instance from ContextBus singleton
    //   2. Logs info using Debugger
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Sets $configContextDTO
    // ===============================================
    private function loadContext(): void {
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
    }

    // ===============================================
    // Function: handle
    // Inputs: none (executed by command runner)
    // Outputs: void
    // Purpose: Main execution of dumb-modes command
    // Logic Walkthrough:
    //   1. Defines TOOL_BASE_PATH using PathManager
    //   2. Boots all core systems via Bootstrapper
    //   3. Loads ConfigContextDTO
    //   4. Prints a header in Debugger
    //   5. Resolves all modes and metadata
    //   6. Prints resolved modes
    //   7. Prints completion headers
    // External Functions/Helpers Used:
    //   - PathManager->resolveToolPath()
    //   - Bootstrapper->boot()
    //   - loadContext()
    //   - resolveModes()
    //   - printModes()
    //   - Debugger()->header()
    // Side Effects:
    //   - Boots all core systems
    //   - Sets $resolvedModes
    // ===============================================
    public function handle(): int
    {
        return $this->runWithUserFriendlyErrors(function() {
            define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
            $this->bootstrapper->boot($this);
    
            $this->loadContext();
    
            Debugger()->header('Forge Foundary Dumb Modes Command Started.', 'huge');
            
            $this->resolveModes();
    
            $this->printModes();
         
            Debugger()->header('Forge Foundary Dumb Modes Command Finished.', 'huge');
            
            Debugger()->header('Debugger Finished.', 'huge');
        });
    }

    // ===============================================
    // Function: printModes
    // Inputs: none
    // Outputs: void (prints to CLI)
    // Purpose: Displays all resolved modes in a structured format
    // Logic Walkthrough:
    //   1. Iterates over $resolvedModes
    //   2. Prints mode name
    //   3. If metadata is empty, prints placeholder
    //   4. Otherwise prints key-value pairs, handling multi-line strings
    // Side Effects: outputs to console
    // External Functions: $this->line()
    // ===============================================
    private function printModes(): void
    {
        foreach ($this->resolvedModes as $modeName => $metadata) {

            $this->line($modeName);

            if (empty($metadata)) {
                $this->line("└─  <no metadata>");
                $this->newLine();
                continue;
            }

            $keys = array_keys($metadata);
            $lastKey = end($keys);

            foreach ($metadata as $key => $value) {
                $prefix = ($key === $lastKey) ? "└─" : "├─";

                if (is_string($value) && str_contains($value, "\n")) {
                    $lines = explode("\n", trim($value));

                    $this->line("$prefix $key: " . array_shift($lines));

                    foreach ($lines as $line) {
                        $this->line("   │  " . $line);
                    }
                } else {
                    $this->line("$prefix $key: $value");
                }
            }

            $this->newLine();
        }
    }

    // ===============================================
    // Function: resolveModes
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves all modes and their metadata
    // Logic Walkthrough:
    //   1. Calls resolveModesNames() to populate $this->modes
    //   2. Iterates over $this->modes to parse YAML metadata using resolveModeMetadata()
    //   3. Sets $this->resolvedModes
    // External Functions:
    //   - resolveModesNames()
    //   - resolveModeMetadata()
    // Side Effects: sets $resolvedModes
    // ===============================================
    private function resolveModes(): void{
        $resolvedModes = [];
        $this->resolveModesNames($this->configContextDTO->modesPath);
        
        foreach($this->modes as $modeName => $modePath){
            $resolvedModes[$modeName] = $this->resolveModeMetadata($modePath);
        }

        $this->resolvedModes = $resolvedModes;
    }

    // ===============================================
    // Function: resolveModesNames
    // Inputs:
    //   - string $modesPath: path to modes directory
    // Outputs: void
    // Purpose: Populates $this->modes with mode name => file path
    // Logic Walkthrough:
    //   1. Scans all subdirectories in $modesPath
    //   2. Ignores "." and ".."
    //   3. Normalizes paths using PathManager
    //   4. Checks for YAML files and adds to $modes
    // External Functions:
    //   - PathManager->normalizeSlashes()
    // Side Effects: sets $this->modes
    // ===============================================
    private function resolveModesNames(string $modesPath): void
    {
        $modes = [];

        foreach (scandir($modesPath) as $subDir) {
            if ($subDir === '.' || $subDir === '..') continue;

            $fullSubDirPath = $this->pathManager->normalizeSlashes($modesPath . '/' . $subDir);

            if (!is_dir($fullSubDirPath)) continue;

            foreach (scandir($fullSubDirPath) as $file) {
                if ($file === '.' || $file === '..') continue;

                if (preg_match('/\.ya?ml$/i', $file)) {
                    $modeName = pathinfo($file, PATHINFO_FILENAME);
                    $modePath = $this->pathManager->normalizeSlashes("$fullSubDirPath/$file");
                    $modes[$modeName] = $modePath;
                }
            }
        }

        $this->modes = $modes;
    }

    // ===============================================
    // Function: resolveModeMetadata
    // Inputs:
    //   - string $modePath: path to a YAML file
    // Outputs:
    //   - array: extracted metadata for the mode
    // Purpose: Parses the YAML file and retrieves mode metadata
    // Logic Walkthrough:
    //   1. Uses Symfony Yaml parser to read file
    //   2. Extracts mode_metadata key (if exists) or returns empty array
    // External Functions:
    //   - Yaml::parseFile()
    // Side Effects: none
    // ===============================================
    private function resolveModeMetadata(string $modePath): array {
        return Yaml::parseFile($modePath)[self::DUMB_MODES_COMMAND_CONFIG_KEYS["mode_metadata"]] ?? [];
    }
}
