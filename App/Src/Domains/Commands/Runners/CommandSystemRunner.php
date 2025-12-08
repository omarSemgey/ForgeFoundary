<?php

namespace App\Src\Domains\Commands\Runners;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;
use App\Src\Domains\Commands\Executors\CommandsExecuter;
use App\Src\Domains\Commands\Resolvers\CommandResolver;
use App\Src\Core\Helpers\SystemStateManager;

class CommandSystemRunner
{

    public function __construct(
        private CommandResolver $commandResolver,
        private CommandsExecuter $commandsExecuter,
        private SystemStateManager $systemStateManager,
    ){}
        
    public function run(bool $before): void
    {
        Debugger()->header('Commands System Runner Started.', 'big');

        if(!$this->systemStateManager->assertEnabled('commands', 'Commands')){
            return;
        };


        $this->validate();
        $this->resolve();
        $this->execute($before);
        Debugger()->header('Commands System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function resolve(): void
    {
        Debugger()->header('Commands Resolver Started.', 'medium');
        $commandsContextDTO = $this->commandResolver->resolveCommandsContext();
        $this->publishDTO(CommandsContextDTO::class, $commandsContextDTO);
        Debugger()->header('Commands Resolver Finished.', 'medium');
    }

    public function execute(bool $before):void{
        Debugger()->header('Commands Executer Started.', 'medium');
        $this->commandsExecuter->executeCommands($before);
        Debugger()->header('Commands Executer Finished.', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Commands Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Commands Context Publisher Finished.', 'medium');
    }
}
