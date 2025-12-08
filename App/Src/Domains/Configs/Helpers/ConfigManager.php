<?php

namespace App\Src\Domains\Configs\Helpers;

class ConfigManager
{
    public static function loadConfig(string $configName, array $configValue): void{
        Config()->set($configName, $configValue);
    }
}