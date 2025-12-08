<?php

namespace App\Src\Domains\Commands\Resolvers;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;

class CommandResolver
{
    private array $beforeCommands;
    private array $afterCommands;
    private const COMMANDS_CONFIG_KEYS = [
        "commands" => "commands",
        "before" => "before", 
        "after" => "after",
    ];

    public function resolveCommandsContext(): CommandsContextDTO
    {
        $this->resolveBeforeCommands();
        $this->resolveAfterCommands();
        return new CommandsContextDTO(
            $this->beforeCommands,
            $this->afterCommands,
        );
    }

    private function resolveBeforeCommands(): void{
        $this->beforeCommands = Config()->get("mode_config." . self::COMMANDS_CONFIG_KEYS['commands'] . "." . self::COMMANDS_CONFIG_KEYS["before"], []);
        $logCommands = count($this->beforeCommands) ?  "Pre-scaffolding commands provided: '[" . implode(', ', $this->beforeCommands) . "]'" : 'No pre-scaffolding commands were provided';
        Debugger()->info($logCommands);
    }
    
    private function resolveAfterCommands(): void{
        $this->afterCommands = Config()->get("mode_config." . self::COMMANDS_CONFIG_KEYS['commands'] . "." . self::COMMANDS_CONFIG_KEYS["after"], []);
        $logCommands = count($this->afterCommands) ?  "Post-scaffolding commands provided: '[" . implode(', ', $this->afterCommands) . "]'" : 'No post-scaffolding commands were provided';
        Debugger()->info($logCommands);
    }
}
