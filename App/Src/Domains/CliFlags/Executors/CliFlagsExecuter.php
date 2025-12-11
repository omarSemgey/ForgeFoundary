<?php

namespace App\Src\Domains\CliFlags\Executors;

use Arr;
use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\CliFlags\Helpers\ConfigLoader;
use Symfony\Component\Yaml\Yaml;

// ===============================================
// Class: CliFlagsExecuter
// Purpose: Applies CLI flag overrides to the mode configuration.
//          It reads the CLI flags provided by the user, normalizes their values,
//          and updates the corresponding configuration keys recursively.
// ===============================================
class CliFlagsExecuter
{
    // Holds the current mode configuration loaded from the context bus
    private ConfigContextDTO $configContextDTO;

    // Holds the CLI flags context, including provided flags and which config keys they can mutate
    private CliFlagsContextDTO $cliFlagsContextDTO;

    // Config loader helper used to reload configuration after mutations
    public function __construct(protected ConfigLoader $configLoader){}

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: none
    // Purpose: Loads required contexts from the ContextBus singleton
    // Logic:
    //   1. Retrieve ConfigContextDTO from the context bus
    //   2. Retrieve CliFlagsContextDTO from the context bus
    //   3. Logs debug info about loaded contexts
    // External Functions/Helpers:
    //   - ContextBus()
    //   - Debugger()->info()
    // Side Effects:
    //   - Initializes $this->configContextDTO and $this->cliFlagsContextDTO
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
    // Outputs: none
    // Purpose: Main entry point to apply CLI flags to the mode configuration
    // Logic:
    //   1. Load contexts
    //   2. Iterate over provided CLI flags
    //   3. Normalize the value (boolean conversion for 'true'/'false' or keep as string)
    //   4. Apply the flag recursively to all matching configuration keys
    //   5. Update the ContextBus and reload the configuration via ConfigLoader
    // External Functions/Helpers:
    //   - loadContexts()
    //   - applyCliFlagRecursive()
    //   - ContextBus()->mutateModeValue()
    //   - $this->configLoader->loadConfig()
    //   - Debugger()->info()
    // Side Effects:
    //   - Mutates the mode configuration stored in the context bus and in the config loader
    // ===============================================
    public function executeCliFlags(): void {
        $this->loadContexts();
        $configValue = $this->configContextDTO->modeValue;
        $mutatableConfigKeys = $this->cliFlagsContextDTO->mutatableConfigKeys; 

        foreach ($this->cliFlagsContextDTO->providedCliFlags as $flag) {
            [$cliFlag, $providedValue] = explode('=', $flag, 2);

            if (!isset($mutatableConfigKeys[$cliFlag])) {
                Debugger()->warning("Unknown CLI flag '{$cliFlag}', skipping.");
                continue;
            }

            // Normalize boolean strings into actual boolean values
            if ($providedValue === 'true' || $providedValue === '1') {
                $normalizedValue = true;
                Debugger()->info("'{$cliFlag}' CLI flag detected as bool TRUE");
            } elseif ($providedValue === 'false' || $providedValue === '0') {
                $normalizedValue = false;
                Debugger()->info("'{$cliFlag}' CLI flag detected as bool FALSE");
            } else {
                $normalizedValue = $providedValue;
            }

            // Apply normalized value recursively to all config keys associated with this CLI flag
            foreach ($mutatableConfigKeys[$cliFlag] as $configKey) {
                $this->applyCliFlagRecursive($configValue, $configKey, $normalizedValue, $cliFlag);
            }
        }

        // Update the context bus and reload the configuration with applied CLI flags
        ContextBus()->mutateModeValue($configValue);
        $this->configLoader->loadConfig('mode_config', $configValue);
    }

    // ===============================================
    // Function: applyCliFlagRecursive
    // Inputs:
    //   - array &$config: reference to the current configuration array
    //   - string $configKey: the key in the configuration to update
    //   - mixed $value: the value to apply to the key
    //   - string $cliFlag: the CLI flag being applied (for logging)
    // Outputs: none
    // Purpose: Recursively applies a CLI flag value to all matching keys in a nested configuration array
    // Logic:
    //   1. Iterate through all keys in the config array
    //   2. If a value is an array, recurse into it
    //   3. If the key matches the target config key, overwrite it with the new value
    //   4. Log the change
    // External Functions/Helpers:
    //   - Debugger()->info()
    // Side Effects:
    //   - Mutates the $config array in-place
    // ===============================================
    private function applyCliFlagRecursive(array &$config, string $configKey, mixed $value, string $cliFlag): void {
        foreach ($config as $key => &$val) {
            if (is_array($val)) {
                $this->applyCliFlagRecursive($val, $configKey, $value, $cliFlag);
                continue;
            }

            if ($key === $configKey) {
                Debugger()->info("CLI flag '{$cliFlag}' overwrote '{$key}' value '{$val}' with '{$value}'");
                $val = $value;
            }
        }
    }
}
