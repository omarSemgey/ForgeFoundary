<?php

namespace App\Src\Domains\CliFlags\Runners;

use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Domains\CliFlags\Executors\CliFlagsExecuter;
use App\Src\Domains\CliFlags\Resolvers\CliFlagsResolver;
use App\Src\Core\Helpers\SystemStateManager;

class CliFlagsSystemRunner
{
    // private CliFlagsResolver $CliFlagsResolver;

    public function __construct(
        private CliFlagsResolver $CliFlagsResolver,
        private CliFlagsExecuter $cliFlagsExecuter,
        private SystemStateManager $systemStateManager,
    ){}
        
    public function run(): void
    {
        Debugger()->header('Cli Flags System Runner Started.', 'big');

        if(!$this->systemStateManager->assertEnabled('cli_flags', 'Cli Flags')){
            return;
        };


        $this->validate();
        $this->resolve();
        $this->execute();
        Debugger()->header('Cli Flags System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function resolve(): void
    {
        Debugger()->header('Cli Flags Resolver Started.', 'medium');
        $cliFlagsContextDTO = $this->CliFlagsResolver->resolveCliFlagsContext();
        $this->publishDTO(CliFlagsContextDTO::class, $cliFlagsContextDTO);
        Debugger()->header('Cli Flags Resolver Finished.', 'medium');
    }

    private function execute():void{
        Debugger()->header('Cli Flags Executer Started.', 'medium');
        $this->cliFlagsExecuter->executeCliFlags();
        Debugger()->header('Cli Flags Executer Finished.', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Cli Flags Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Cli Flags Context Publisher Finished.', 'medium');
    }
}
