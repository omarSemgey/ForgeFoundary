<?php

namespace App\Src\Domains\Components\Runners;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Components\Generators\ComponentGenerator;
use App\Src\Domains\Components\Resolvers\ComponentResolver;

class ComponentSystemRunner
{

    public function __construct(private ComponentResolver $componentResolver, private ComponentGenerator $componentGenerator) {}
        
    public function run(): void
    {
        Debugger()->header('Component System Runner Started.', 'big');
        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Component System Runner Finished.', 'big');
    }

    private function generate(): void{
        Debugger()->header('Component Generator Started.', 'medium');
        $this->componentGenerator->generateComponent();
        Debugger()->header('Component Generating Finished.', 'medium');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function resolve(): void
    {
        Debugger()->header('Component Resolver Started.', 'medium');
        $componentContextDTO = $this->componentResolver->resolveComponentContext();
        $this->publishDTO(ComponentContextDTO::class, $componentContextDTO);
        Debugger()->header('Component Resolving Finished.', 'medium');
    }
    
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Component Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Component Context Publishing Finished.', 'medium');
    }
}
