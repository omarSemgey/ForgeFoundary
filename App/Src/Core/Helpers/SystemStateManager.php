<?php

namespace App\Src\Core\Helpers;

use Log;

class SystemStateManager
{
    private const SYSTEMS = [
        "directories" => "directories_enabled",
        "units" => "units_enabled",
        "templates" => "templates_enabled",
        "cli_flags" => "cli_flags_enabled",
        "naming_conventions" => "naming_conventions_enabled",
        "commands" => "commands_enabled",
    ];

    public function getSystemState(string $systemName) :bool{
        if (!isset(self::SYSTEMS[$systemName])) {
            Debugger()->error("Unknown system name '{$systemName}'. System wont be ran");
            return false;
        }
        
        $systemStateKey = self::SYSTEMS[$systemName];
        return Config()->get("mode_config.{$systemStateKey}") ?? true;
    }

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