<?php
// ===============================================
// File: Aliases.php
// Purpose: Provides global helper functions (aliases) for commonly used classes 
//          like Debugger, ContextBus, Config, NamingConventionsExecuter, and Reporter.
//          Each alias returns a singleton instance of the respective class for convenience.
// Functions:
//   - Debugger(): returns the singleton Debugger instance
//   - ContextBus(): returns the singleton ContextBus instance
//   - Config(): returns the singleton Config instance
//   - NamingConventions(): returns the singleton NamingConventionsExecuter instance
//   - Reporter(): returns the singleton Reporter instance
// ===============================================

use App\Src\Core\NamingConventions\Executers\NamingConventionsExecuter;
use App\Src\Core\Debuggers\Debugger;
use App\Src\Core\Contexts\ContextBus;
use App\Src\Core\Helpers\Config;
use App\Src\Core\Reporters\Reporter;

// ===============================================
// Function: Debugger
// Inputs: none
// Outputs: Debugger instance
// Purpose: Returns a singleton instance of the Debugger class
// Logic: Checks if the function already exists; if not, defines it to return
//        Debugger::getInstance()
// Side Effects: None
// Uses: Debugger::getInstance()
// ===============================================
if (!function_exists('Debugger')) {
    function Debugger(): Debugger {
        return Debugger::getInstance();
    }
}

// ===============================================
// Function: ContextBus
// Inputs: none
// Outputs: ContextBus instance
// Purpose: Returns a singleton instance of the ContextBus class
// Logic: Checks if the function exists; defines it to return ContextBus::getInstance()
// Side Effects: None
// Uses: ContextBus::getInstance()
// ===============================================
if (!function_exists('ContextBus')) {
    function ContextBus(): ContextBus {
        return ContextBus::getInstance();
    }
}

// ===============================================
// Function: Config
// Inputs: none
// Outputs: Config instance
// Purpose: Returns a singleton instance of the Config class
// Logic: Checks for existence, defines alias to return Config::getInstance()
// Side Effects: None
// Uses: Config::getInstance()
// ===============================================
if (!function_exists('Config')) {
    function Config(): Config {
        return Config::getInstance();
    }
}

// ===============================================
// Function: NamingConventions
// Inputs: none
// Outputs: NamingConventionsExecuter instance
// Purpose: Returns a singleton instance of the NamingConventionsExecuter class
// Logic: Checks for existence, defines alias to return NamingConventionsExecuter::getInstance()
// Side Effects: None
// Uses: NamingConventionsExecuter::getInstance()
// ===============================================
if (!function_exists('NamingConventionsExecuter')) {
    function NamingConventions(): NamingConventionsExecuter {
        return NamingConventionsExecuter::getInstance();
    }
}

// ===============================================
// Function: Reporter
// Inputs: none
// Outputs: Reporter instance
// Purpose: Returns a singleton instance of the Reporter class
// Logic: Checks for existence, defines alias to return Reporter::getInstance()
// Side Effects: None
// Uses: Reporter::getInstance()
// ===============================================
if (!function_exists('Reporter')) {
    function Reporter(): Reporter
    {
        return Reporter::getInstance();
    }
}
