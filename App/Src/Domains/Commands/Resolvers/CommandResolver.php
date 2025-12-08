<?php

namespace App\Src\Domains\Commands\Resolvers;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;

// ===============================================
// Class: CommandResolver
// Purpose: Resolves pre- and post-scaffolding commands
//          from the current mode configuration and returns them
//          encapsulated in a CommandsContextDTO.
// Functions:
//   - resolveCommandsContext(): returns a CommandsContextDTO with before/after commands
//   - resolveBeforeCommands(): retrieves and logs pre-scaffolding commands
//   - resolveAfterCommands(): retrieves and logs post-scaffolding commands
// ===============================================
class CommandResolver
{
    private array $beforeCommands; // Stores commands to run before scaffolding
    private array $afterCommands;  // Stores commands to run after scaffolding

    private const COMMANDS_CONFIG_KEYS = [
        "commands" => "commands",
        "before" => "before", 
        "after" => "after",
    ]; // Configuration keys for locating commands in mode_config

    // ===============================================
    // Function: resolveCommandsContext
    // Inputs: none
    // Outputs: CommandsContextDTO containing beforeCommands and afterCommands
    // Purpose: Public entry point for retrieving all configured commands
    // Logic Walkthrough:
    //   1. Resolves "before" commands
    //   2. Resolves "after" commands
    //   3. Returns a DTO containing both arrays
    // External Functions/Helpers Used:
    //   - $this->resolveBeforeCommands()
    //   - $this->resolveAfterCommands()
    // Side Effects:
    //   - Logs the resolved before/after commands using Debugger
    // ===============================================
    public function resolveCommandsContext(): CommandsContextDTO
    {
        $this->resolveBeforeCommands();
        $this->resolveAfterCommands();
        return new CommandsContextDTO(
            $this->beforeCommands,
            $this->afterCommands,
        );
    }

    // ===============================================
    // Function: resolveBeforeCommands
    // Inputs: none
    // Outputs: void
    // Purpose: Loads pre-scaffolding commands from configuration and logs them
    // Logic Walkthrough:
    //   1. Retrieves the "before" commands from mode_config via Config()
    //   2. Sets $this->beforeCommands to the retrieved array or empty if none
    //   3. Logs the commands count and their names using Debugger()->info()
    // External Functions/Helpers Used:
    //   - Config()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Updates $this->beforeCommands
    //   - Logs information to the Debugger
    // ===============================================
    private function resolveBeforeCommands(): void
    {
        $this->beforeCommands = Config()->get(
            "mode_config." . self::COMMANDS_CONFIG_KEYS['commands'] . "." . self::COMMANDS_CONFIG_KEYS["before"], 
            []
        );

        $logCommands = count($this->beforeCommands) 
            ? "Pre-scaffolding commands provided: '[" . implode(', ', $this->beforeCommands) . "]'" 
            : 'No pre-scaffolding commands were provided';
        
        Debugger()->info($logCommands);
    }
    
    // ===============================================
    // Function: resolveAfterCommands
    // Inputs: none
    // Outputs: void
    // Purpose: Loads post-scaffolding commands from configuration and logs them
    // Logic Walkthrough:
    //   1. Retrieves the "after" commands from mode_config via Config()
    //   2. Sets $this->afterCommands to the retrieved array or empty if none
    //   3. Logs the commands count and their names using Debugger()->info()
    // External Functions/Helpers Used:
    //   - Config()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Updates $this->afterCommands
    //   - Logs information to the Debugger
    // ===============================================
    private function resolveAfterCommands(): void
    {
        $this->afterCommands = Config()->get(
            "mode_config." . self::COMMANDS_CONFIG_KEYS['commands'] . "." . self::COMMANDS_CONFIG_KEYS["after"], 
            []
        );

        $logCommands = count($this->afterCommands) 
            ? "Post-scaffolding commands provided: '[" . implode(', ', $this->afterCommands) . "]'" 
            : 'No post-scaffolding commands were provided';
        
        Debugger()->info($logCommands);
    }
}
