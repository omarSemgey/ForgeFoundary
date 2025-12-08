<?php

namespace App\Src\Domains\Configs\DTOs;

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
    ) {}
}