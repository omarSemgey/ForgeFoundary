<?php

namespace App\Src\Commands;

use App\Src\Commands\Traits\HandlesUserErrors;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use Symfony\Component\Yaml\Yaml;

// ===============================================
// Class: DumbModeCommand
// Purpose: CLI command for dumping a ForgeFoundary mode's configuration values.
// Functions:
//   - __construct(): injects Bootstrapper and PathManager dependencies
//   - handle(): entry point for command execution
//   - loadContext(): loads required contexts from ContextBus
//   - dumbMode(): prints a mode's configuration values (tree or raw YAML)
//   - printTree(): recursively prints nested arrays in tree format
//   - printRawYaml(): prints nested arrays in YAML format
// ===============================================
class DumbModeCommand extends Command
{
    use HandlesUserErrors;
    // Signature defines all options available for this command
    protected $signature = 'dumb-mode {--mode=} {--modes-path=} {--raw-yaml} {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log}';
    protected $description = 'Dump the configuration values for the ForgeFoundary command selected mode';
    
    private ConfigContextDTO $configContextDTO;
    private CliInputContextDTO $cliInputContextDTO;

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
    // Purpose: Loads the configuration and CLI input contexts from the global ContextBus
    // Logic:
    //   - Fetch ConfigContextDTO and CliInputContextDTO from ContextBus
    //   - Log info messages about loaded contexts
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects: populates $configContextDTO and $cliInputContextDTO
    // ===============================================
    private function loadContext(): void {
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    // ===============================================
    // Function: handle
    // Inputs: none (entry point for command)
    // Outputs: void
    // Purpose: Main execution function for dumb-mode command
    // Logic Walkthrough:
    //   1. Define TOOL_BASE_PATH using PathManager
    //   2. Boot all core systems via Bootstrapper
    //   3. Load required contexts
    //   4. Print header and dump the mode's configuration via dumbMode()
    //   5. Print headers indicating completion
    // External Functions/Helpers Used:
    //   - define()
    //   - PathManager->resolveToolPath()
    //   - Bootstrapper->boot()
    //   - loadContext()
    //   - Debugger()->header()
    //   - dumbMode()
    // Side Effects: boots core systems, outputs configuration to console
    // ===============================================
    public function handle(): int
    {
        return $this->runWithUserFriendlyErrors(function() {
            define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
            $this->bootstrapper->boot($this);
            $this->loadContext();
    
            Debugger()->header('Forge Foundary Dumb Mode Command Started.', 'huge');
            $this->dumbMode($this->configContextDTO->modeValue, $this->configContextDTO->modeName);
         
            Debugger()->header('Forge Foundary Dumb Mode Command Finished.', 'huge');
            
            Debugger()->header('Debugger Finished.', 'huge');
        });
    }

    // ===============================================
    // Function: dumbMode
    // Inputs:
    //   - array $modeValue: mode configuration array
    //   - string $modeName: name of the mode
    // Outputs: void
    // Purpose: Print the mode configuration in either tree or raw YAML format
    // Logic:
    //   - Print mode name header
    //   - If raw-yaml option is set, call printRawYaml()
    //   - Otherwise, call printTree() to display nested structure
    // External Functions/Helpers Used:
    //   - $this->line() (prints to console)
    //   - printTree()
    //   - printRawYaml()
    // Side Effects: outputs mode values to console
    // ===============================================
    private function dumbMode(array $modeValue, string $modeName): void
    {
        $this->line("===== {$modeName} Value =====");
    
        if ($this->cliInputContextDTO->getOption('raw-yaml')) {
            $this->printRawYaml($modeValue);
            $this->line("==========");
            return;
        }

        $this->printTree($modeValue);
        $this->line("==========");
    }

    // ===============================================
    // Function: printTree
    // Inputs:
    //   - array $modeValue: nested array to print
    //   - string $prefix: prefix for tree formatting
    // Outputs: void
    // Purpose: Recursively prints a nested array as a tree structure
    // Logic:
    //   - Determine number of elements and iterate
    //   - For each element, choose connector based on position
    //   - If element is an array:
    //       - Check if associative or indexed
    //       - Recursively print tree
    //   - If element is a scalar, print key → value
    // External Functions/Helpers Used:
    //   - $this->line()
    // Side Effects: prints formatted tree to console
    // ===============================================
    private function printTree(array $modeValue, string $prefix = '   '): void
    {
        $count = count($modeValue);
        $i = 0;

        foreach ($modeValue as $key => $value) {
            $i++;
            $connector = $i === $count ? '└─ ' : '├─ ';

            if (is_array($value)) {
                $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                $this->line($prefix . $connector . $key);
                $newPrefix = $prefix . ($i === $count ? '   ' : '│  ');

                if ($isAssoc) {
                    $this->printTree($value, $newPrefix);
                } else {
                    foreach ($value as $idx => $item) {
                        $itemConnector = $idx === count($value) - 1 ? '└─ ' : '├─ ';
                        if (is_array($item)) {
                            $this->line($newPrefix . $itemConnector . '[list]');
                            $this->printTree($item, $newPrefix . ($idx === count($value) - 1 ? '   ' : '│  '));
                        } else {
                            $this->line($newPrefix . $itemConnector . $item);
                        }
                    }
                }
            } else {
                $this->line($prefix . $connector . "{$key} → {$value}");
            }
        }
    }

    // ===============================================
    // Function: printRawYaml
    // Inputs:
    //   - array $modeValue: nested array to print
    // Outputs: void
    // Purpose: Dump the array as YAML with indentation for readability
    // Logic:
    //   - Uses Symfony Yaml::dump() to serialize array
    //   - Prints YAML to console via $this->line()
    // External Functions/Helpers Used:
    //   - Yaml::dump()
    //   - $this->line()
    // Side Effects: prints YAML-formatted output to console
    // ===============================================
    private function printRawYaml(array $modeValue): void
    {
        $this->line(Yaml::dump($modeValue, 4, 2));
    }
}
