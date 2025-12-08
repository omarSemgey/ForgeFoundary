<?php

namespace App\Src\Core\Helpers;

// ===============================================
// Class: SystemStateManager
// Purpose: Central manager to check if a core system 
//          (directories, units, templates, CLI flags, naming conventions, commands) 
//          is enabled or disabled based on the mode configuration.
// Functions:
//   - getSystemState(string $systemName):bool — checks if a system is enabled
//   - assertEnabled(string $systemName, string $runnerName):bool — checks and logs warnings if a system is disabled
// ===============================================
class SystemStateManager
{
    // ===============================================
    // Constant: SYSTEMS
    // Purpose: Maps internal system names to their corresponding config keys.
    // Keys: directory, units, templates, cli_flags, naming_conventions, commands
    // Values: config keys in mode_config
    // ===============================================
    private const SYSTEMS = [
        "directories" => "directories_enabled",
        "units" => "units_enabled",
        "templates" => "templates_enabled",
        "cli_flags" => "cli_flags_enabled",
        "naming_conventions" => "naming_conventions_enabled",
        "commands" => "commands_enabled",
    ];

    // ===============================================
    // Function: getSystemState
    // Inputs:
    //   - string $systemName: The internal name of the system to check
    // Outputs:
    //   - bool: true if the system is enabled, false otherwise
    // Purpose: Returns the enabled/disabled state of a system based on mode_config
    // Logic Walkthrough:
    //   1. Check if $systemName exists in SYSTEMS map
    //   2. If unknown, log error via Debugger and return false
    //   3. Otherwise, fetch the config key corresponding to the system
    //   4. Return the value from Config()->get(...), defaulting to true
    // External Functions/Helpers Used:
    //   - Debugger()->error() for unknown systems
    //   - Config()->get() to retrieve system state from mode configuration
    // Side Effects:
    //   - Logs error if systemName is unknown
    // ===============================================
    public function getSystemState(string $systemName) :bool
    {
        if (!isset(self::SYSTEMS[$systemName])) {
            Debugger()->error("Unknown system name '{$systemName}'. System won't be ran");
            return false;
        }
        
        $systemStateKey = self::SYSTEMS[$systemName];
        return Config()->get("mode_config.{$systemStateKey}") ?? true;
    }

    // ===============================================
    // Function: assertEnabled
    // Inputs:
    //   - string $systemName: Internal name of the system
    //   - string $runnerName: Name of the system runner being executed
    // Outputs:
    //   - bool: true if the system is enabled, false otherwise
    // Purpose: Ensures a system is enabled before running; logs warnings if disabled
    // Logic Walkthrough:
    //   1. Call getSystemState($systemName)
    //   2. If system is disabled:
    //       - Log a warning with $runnerName
    //       - Log a header indicating runner finished
    //       - Return false
    //   3. If system is enabled, return true
    // External Functions/Helpers Used:
    //   - getSystemState()
    //   - Debugger()->warning()
    //   - Debugger()->header()
    // Side Effects:
    //   - Logs warnings and headers if system is disabled
    // ===============================================
    public function assertEnabled(string $systemName, string $runnerName): bool
    {
        if (!$this->getSystemState($systemName)) {
            Debugger()->warning("$runnerName system is disabled");
            Debugger()->header("$runnerName System Runner Finished.", 'big');
            return false;
        }
        return true;
    }
}
