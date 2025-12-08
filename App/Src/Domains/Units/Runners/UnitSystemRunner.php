<?php

namespace App\Src\Domains\Units\Runners;

use App\Src\Domains\Units\Resolvers\UnitResolver;
use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Generators\UnitsGenerator;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: UnitSystemRunner
// Purpose: Orchestrates the entire "Units" system within ForgeFoundary.
//          It ensures the system is enabled, validates, resolves unit contexts,
//          generates units, and publishes them to the global context bus.
// Dependencies:
//   - UnitResolver: resolves the context of units
//   - UnitsGenerator: generates unit code/files based on resolved context
//   - SystemStateManager: checks if systems like 'units' or 'directories' are enabled
// ===============================================
class UnitSystemRunner
{
    public function __construct(
        private UnitResolver $unitResolver, 
        private UnitsGenerator $unitsGenerator,
        private SystemStateManager $systemStateManager,
    ) {}

    // ===========================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the full lifecycle of the units system
    // Logic Walkthrough:
    //   1. Prints a header for start
    //   2. Checks if 'units' system is enabled via SystemStateManager
    //   3. Checks if 'directories' system is enabled (required for units)
    //   4. Runs validate(), resolve(), generate() in order
    //   5. Prints a header for finish
    // Side Effects: Logs headers and warnings to Debugger
    // Uses: validate(), resolve(), generate(), Debugger(), SystemStateManager
    // ===========================================
    public function run(): void
    {
        Debugger()->header('Unit System Runner Started', 'big');

        if(!$this->systemStateManager->assertEnabled('units', 'Units')){
            return;
        }

        if(!$this->systemStateManager->getSystemState('directories')){
            Debugger()->warning("Directories system is disabled therefore units system cant be ran");
            Debugger()->header('Unit System Runner Finished.', 'big');
            return;
        }

        $this->validate();
        $this->resolve();
        $this->generate();

        Debugger()->header('Unit System Runner Finished', 'big');
    }

    // ===========================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for future validation logic for the units system
    // Logic Walkthrough: currently not implemented
    // Side Effects: none
    // ===========================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ===========================================
    // Function: generate
    // Inputs: none
    // Outputs: void
    // Purpose: Triggers the units generator to create all unit code/files
    // Logic Walkthrough:
    //   1. Prints header for generator start
    //   2. Calls UnitsGenerator->generateUnits()
    //   3. Prints header for generator finish
    // Side Effects: Logs headers to Debugger, generates files on disk
    // Uses: UnitsGenerator->generateUnits(), Debugger()
    // ===========================================
    private function generate(): void
    {
        Debugger()->header('Unit Generator Started', 'medium');
        $this->unitsGenerator->generateUnits();
        Debugger()->header('Unit Generator Finished', 'medium');
    }

    // ===========================================
    // Function: resolve
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves the current context of units and publishes it to the global context bus
    // Logic Walkthrough:
    //   1. Prints header for resolver start
    //   2. Calls UnitResolver->resolveUnitsContext() to get context
    //   3. Publishes the context DTO via publishDTO()
    //   4. Prints header for resolver finish
    // Side Effects: Logs headers to Debugger, publishes context to ContextBus
    // Uses: UnitResolver->resolveUnitsContext(), publishDTO(), Debugger(), ContextBus()
    // ===========================================
    private function resolve(): void
    {
        Debugger()->header('Unit Resolver Started', 'medium');
        $unitContextDTO = $this->unitResolver->resolveUnitsContext();
        $this->publishDTO(UnitContextDTO::class, $unitContextDTO);
        Debugger()->header('Unit Resolver Finished', 'medium');
    }

    // ===========================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: the key for the DTO in the context bus
    //   - object $dto: the DTO object to publish
    // Outputs: void
    // Purpose: Publishes a DTO to the global ContextBus and logs the process
    // Logic Walkthrough:
    //   1. Prints header for publisher start
    //   2. Calls ContextBus()->publish() with the DTO
    //   3. Logs published DTO key to Debugger
    //   4. Prints header for publisher finish
    // Side Effects: Logs headers and info to Debugger, modifies ContextBus
    // Uses: ContextBus()->publish(), Debugger()
    // ===========================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Unit Context Publisher Started', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Unit Context Publisher Finished', 'medium');
    }
}
