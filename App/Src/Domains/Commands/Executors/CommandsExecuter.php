<?php

namespace App\Src\Domains\Commands\Executors;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;
use Log;

// ===============================================
// Class: CommandsExecuter
// Purpose: Handles execution of pre- and post-scaffolding commands defined in the CommandsContextDTO.
//          Supports running shell commands and logging their execution status via Debugger.
// Functions:
//   - loadContexts(): Loads the CommandsContextDTO from the ContextBus
//   - executeCommands(bool $before): Executes the commands either before or after scaffolding
// ===============================================
class CommandsExecuter
{
    // ===============================================
    // Property: $commandsContextDTO
    // Type: CommandsContextDTO
    // Purpose: Stores commands context fetched from the ContextBus
    // ===============================================
    private CommandsContextDTO $commandsContextDTO;

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads the CommandsContextDTO instance from the global ContextBus singleton
    // Logic Walkthrough:
    //   1. Calls ContextBus()->get() to retrieve the CommandsContextDTO
    //   2. Stores it in $commandsContextDTO
    //   3. Logs an info message indicating context was loaded
    // External Functions/Helpers Used:
    //   - ContextBus(): retrieves the global context bus
    //   - Debugger(): logs messages
    // Side Effects:
    //   - Updates $commandsContextDTO property
    // ===============================================
    private function loadContexts(): void
    {
        $this->commandsContextDTO = ContextBus()->get(CommandsContextDTO::class);
        Debugger()->info("Loaded context: 'CommandsContextDTO' from the context bus");
    }

    // ===============================================
    // Function: executeCommands
    // Inputs:
    //   - bool $before: Determines whether to execute pre-scaffolding (true) or post-scaffolding (false) commands
    // Outputs: void
    // Purpose: Executes all commands in the CommandsContextDTO according to the $before flag
    // Logic Walkthrough:
    //   1. Calls loadContexts() to ensure commands are loaded
    //   2. Determines commands type (pre- or post-scaffolding) and selects appropriate command list
    //   3. Logs info that command execution is starting
    //   4. Loops through each command:
    //       a. Logs the command being executed
    //       b. Runs the command using passthru()
    //       c. Checks exit code; logs error if non-zero
    //   5. Logs completion of all commands
    // External Functions/Helpers Used:
    //   - Debugger(): logs messages and errors
    //   - passthru(): executes shell commands
    // Side Effects:
    //   - Executes shell commands
    //   - Logs messages to Debugger
    // ===============================================
    public function executeCommands(bool $before): void
    {
        $this->loadContexts();
        
        $commandsType = $before ? "pre-scaffolding" : "post-scaffolding";
        $commands = $before ? $this->commandsContextDTO->beforeCommands : $this->commandsContextDTO->afterCommands;

        Debugger()->info("Executing {$commandsType} commands...");

        foreach ($commands as $cmd) {
            Debugger()->info("Executing: '{$cmd}'");
            $exitCode = null;
            passthru($cmd, $exitCode);
        
            if ($exitCode !== 0) {
                Debugger()->error("Command failed: '{$cmd}' (exit code {$exitCode})");
            }
        }
            
        Debugger()->info("All {$commandsType} commands executed");
    }
}
