<?php

namespace App\Src\Domains\Configs\DTOs;

// ===============================================
// Class: ConfigContextDTO
// Purpose: Data Transfer Object for passing configuration context
//          between systems in ForgeFoundary. Encapsulates both 
//          main application configuration and mode-specific configuration.
// Functions:
//   - __construct(): initializes all config context properties
// ===============================================
final class ConfigContextDTO
{
    // ===============================================
    // Constructor: __construct
    // Inputs:
    //   - string $mainConfigName: Name of the main configuration
    //   - string $mainConfigPath: File path of the main configuration
    //   - array $mainConfigValue: Parsed values of the main configuration
    //   - string $modeName: Name of the currently active mode
    //   - string $modesPath: Base path where all modes are stored
    //   - array $modeValue: Parsed values of the active mode configuration
    // Outputs: none
    // Purpose: Encapsulates all relevant configuration information in a single DTO
    // Logic Walkthrough:
    //   - Stores main config name, path, and values
    //   - Stores current mode name, path, and values
    // Side Effects: None
    // External Functions/Helpers Used: None
    // ===============================================
    public function __construct(
        // Main config
        public string $mainConfigName,
        public string $mainConfigPath,
        public array $mainConfigValue,

        // Mode Config
        public string $modeName,
        public string $modesPath,
        public array $modeValue,
    ) {}
}
