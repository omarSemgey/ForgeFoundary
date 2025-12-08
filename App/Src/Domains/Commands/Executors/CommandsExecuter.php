<?php

namespace App\Src\Domains\Commands\Executors;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;
use Log;

class CommandsExecuter
{
    private CommandsContextDTO $commandsContextDTO;
    private function loadContexts():void{
        $this->commandsContextDTO = ContextBus()->get(CommandsContextDTO::class);
        Debugger()->info("Loaded context: 'CommandsContextDTO' from the context bus");
    }

    public function executeCommands(bool $before): void{
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