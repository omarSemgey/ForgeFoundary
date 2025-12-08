<?php

namespace App\Src\Domains\CliFlags\Helpers;

class ConfigLoader
{
    public static function loadConfig(string $configName, array $configValue): void{
        Config()->set($configName, $configValue);
    }
}