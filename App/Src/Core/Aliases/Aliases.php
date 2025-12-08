<?php

use App\Src\Core\NamingConventions\Executers\NamingConventionsExecuter;
use App\Src\Core\Debuggers\Debugger;
use App\Src\Core\Contexts\ContextBus;
use App\Src\Core\Helpers\Config;
use App\Src\Core\Reporters\Reporter;

if (!function_exists('Debugger')) {
    function Debugger(): Debugger {
        return Debugger::getInstance();
    }
}

if (!function_exists('ContextBus')) {
    function ContextBus(): ContextBus {
        return ContextBus::getInstance();
    }
}

if (!function_exists('Config')) {
    function Config(): Config {
        return Config::getInstance();
    }
}

if (!function_exists('NamingConventionsExecuter')) {
    function NamingConventions(): NamingConventionsExecuter {
        return NamingConventionsExecuter::getInstance();
    }
}

if (!function_exists('Reporter')) {
    function Reporter(): Reporter
    {
        return Reporter::getInstance();
    }
}
