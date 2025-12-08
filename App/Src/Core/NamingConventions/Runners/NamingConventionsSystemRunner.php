<?php

namespace App\Src\Core\NamingConventions\Runners;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\Resolvers\NamingConventionsResolver;
use App\Src\Core\Helpers\SystemStateManager;

class NamingConventionsSystemRunner
{
    public function __construct(
        private NamingConventionsResolver $namingConventionsResolver, 
        private SystemStateManager $systemStateManager,
        ) {}
        
    public function run(): void
    {
        Debugger()->header('Naming Conventions System Runner Started.', 'big');
        if(!$this->systemStateManager->assertEnabled('directories', 'Directories')){
            return;
        };
        $this->validate();
        $this->resolve();
        Debugger()->header('Naming Conventions System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function resolve(): void
    {
        Debugger()->header('Naming Conventions Resolver Started.', 'medium');
        $namingConventionContextDTO = $this->namingConventionsResolver->resolveNamingConventionsContext();
        $this->publishDTO(NamingConventionsContextDTO::class, $namingConventionContextDTO);
        Debugger()->header('Naming Conventions Resolver Finished.', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Naming Conventions Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Naming Conventions Context Publisher Finished.', 'medium');
    }
}