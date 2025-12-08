<?php

namespace App\Src\Domains\Configs\Helpers;

// ===============================================
// Class: ConfigManager
// Purpose: Provides helper functions to manage configuration values
// Functions:
//   - loadConfig(string $configName, array $configValue): Loads a configuration into the global config store
// ===============================================
class ConfigManager
{
    // ===============================================
    // Function: loadConfig
    // Inputs:
    //   - string $configName: The key/name of the configuration to set
    //   - array $configValue: The configuration data to store
    // Outputs: void
    // Purpose: Loads a configuration array into the global Config singleton
    // Logic Walkthrough:
    //   1. Calls the global Config() helper to retrieve the Config singleton instance
    //   2. Sets the configuration with the provided name and value
    // External Functions/Helpers Used:
    //   - Config() helper function
    //   - Config->set() method
    // Side Effects:
    //   - Updates global configuration state accessible throughout the application
    // ===============================================
    public static function loadConfig(string $configName, array $configValue): void{
        Config()->set($configName, $configValue);
    }
}
