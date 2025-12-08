<?php

namespace App\Src\Domains\Units\Runners;

use App\Src\Domains\Units\Resolvers\UnitResolver;
use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Generators\UnitsGenerator;
use App\Src\Core\Helpers\SystemStateManager;

class UnitSystemRunner
{
    public function __construct(
        private UnitResolver $unitResolver, 
        private UnitsGenerator $unitsGenerator,
        private SystemStateManager $systemStateManager,
        ) {}
        
    public function run(): void
    {
        Debugger()->header('Unit System Runner Started', 'big');
        if(!$this->systemStateManager->assertEnabled('units', 'Units')){
            return;
        };
        
        if(!$this->systemStateManager->getSystemState('directories')){
            Debugger()->warning("Directories system is disabled therefore units system cant be ran");
            Debugger()->header('Unit System Runner Finished.', 'big');
            return;
        };
        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Unit System Runner Finished', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function generate(): void{
        Debugger()->header('Unit Generator Started', 'medium');
        $this->unitsGenerator->generateUnits();
        Debugger()->header('Unit Generator Finished', 'medium');
    }

    private function resolve(): void
    {
        Debugger()->header('Unit Resolver Started', 'medium');
        $unitContextDTO = $this->unitResolver->resolveUnitsContext();
        $this->publishDTO(UnitContextDTO::class, $unitContextDTO);
        Debugger()->header('Unit Resolver Finished', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Unit Context Publisher Started', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Unit Context Publisher Finished', 'medium');
    }
}
