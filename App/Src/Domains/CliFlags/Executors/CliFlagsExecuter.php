<?php

namespace App\Src\Domains\CliFlags\Executors;

use Arr;
use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\CliFlags\Helpers\ConfigLoader;
use Symfony\Component\Yaml\Yaml;

// ===============================================
// Class: CliFlagsExecuter
// Purpose: Applies CLI flag overrides to the configuration values
//          and updates the mode configuration context accordingly.
// Functions:
//   - __construct(): injects the ConfigLoader dependency
//   - loadContexts(): loads ConfigContextDTO and CliFlagsContextDTO from the ContextBus
//   - executeCliFlags(): executes the process of overriding config values with CLI flags
// ===============================================
class CliFlagsExecuter
{
    // Holds configuration context retrieved from ContextBus
    private ConfigContextDTO $configContextDTO;

    // Holds CLI flags context retrieved from ContextBus
    private CliFlagsContextDTO $cliFlagsContextDTO;

    public function __construct(protected ConfigLoader $configLoader){}

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads necessary contexts from ContextBus for execution
    // Logic Walkthrough:
    //   1. Retrieves ConfigContextDTO from ContextBus
    //   2. Logs info about loading config context
    //   3. Retrieves CliFlagsContextDTO from ContextBus
    //   4. Logs info about loading CLI flags context
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Initializes $configContextDTO and $cliFlagsContextDTO properties
    // ===============================================
    private function loadContexts(): void {
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
        $this->cliFlagsContextDTO = ContextBus()->get(CliFlagsContextDTO::class);
        Debugger()->info("Loaded context: 'CliFlagsContextDTO' from the context bus");
    }

    // ===============================================
    // Function: executeCliFlags
    // Inputs: none
    // Outputs: void
    // Purpose: Overrides default mode configuration values using CLI flags
    //          and updates both ContextBus and the system config loader
    // Logic Walkthrough:
    //   1. Loads contexts via loadContexts()
    //   2. Converts current mode configuration to YAML string
    //   3. Iterates over all provided CLI flags
    //      - Splits each flag into key and value
    //      - Retrieves default value for the key
    //      - Logs info about overriding the default with provided value
    //      - Replaces the default value with the provided value in the YAML string
    //   4. Parses YAML back to PHP array
    //   5. Mutates ContextBus mode value with updated configuration
    //   6. Loads updated config into system via ConfigLoader
    // External Functions/Helpers Used:
    //   - $this->loadContexts()
    //   - Yaml::dump()
    //   - Yaml::parse()
    //   - ContextBus()->mutateModeValue()
    //   - Debugger()->info()
    //   - $this->configLoader->loadConfig()
    // Side Effects:
    //   - Updates configuration values in ContextBus and system
    // ===============================================
    public function executeCliFlags(): void {
        $this->loadContexts();
        $configValue = Yaml::dump($this->configContextDTO->modeValue);
        
        foreach($this->cliFlagsContextDTO->providedCliFlags as $flag){
            [$key, $providedValue] = explode('=', $flag, 2);
            $defaultValue = $this->cliFlagsContextDTO->definedCliFlags[$key];
            Debugger()->info("'{$key}' cli flag overrided the default value: '{$defaultValue}' to the provided value: '{$providedValue}'");
            $configValue = str_replace($defaultValue, $providedValue, $configValue);
        }
        $configValue = Yaml::parse($configValue);
        ContextBus()->mutateModeValue($configValue);
        $this->configLoader->loadConfig('mode_config', $configValue);
    }
}
