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
    public function __construct(
        // Main config
        public string $mainConfigName,
        public string $mainConfigPath,
        public array $mainConfigValue,

        // Mode Config
        public string $modeName,
        public string $modesPath,
        public array $modeValue,
        public string $modeAbsolutePath,
    ) {}
}
