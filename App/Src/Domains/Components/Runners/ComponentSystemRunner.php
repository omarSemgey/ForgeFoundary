<?php

namespace App\Src\Domains\Components\Runners;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Components\Generators\ComponentGenerator;
use App\Src\Domains\Components\Resolvers\ComponentResolver;

// ======================================================
// Class: ComponentSystemRunner
// Purpose: Coordinates the full lifecycle of a component:
//          validation, context resolution, and generation.
//          Ensures the component context is published to the global ContextBus.
// Functions:
//   - __construct(): injects the resolver and generator dependencies
//   - run(): runs the entire component system sequence
//   - validate(): placeholder for component validation
//   - resolve(): resolves component context and publishes it
//   - generate(): generates the component using the generator
//   - publishDTO(): publishes a DTO to the global ContextBus
// ======================================================
class ComponentSystemRunner
{
    public function __construct(
        private ComponentResolver $componentResolver, 
        private ComponentGenerator $componentGenerator
    ) {}
        
    // ======================================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the full component system lifecycle
    // Logic Walkthrough:
    //   1. Logs start of component system runner
    //   2. Calls validate() to perform pre-generation checks (TODO)
    //   3. Calls resolve() to resolve component context and publish it
    //   4. Calls generate() to generate the component code
    //   5. Logs end of component system runner
    // External Functions/Helpers:
    //   - Debugger()->header()
    //   - validate(), resolve(), generate()
    // Side Effects:
    //   - Logs messages to debugger
    //   - Publishes component context to ContextBus
    // ======================================================
    public function run(): void
    {
        Debugger()->header('Component System Runner Started.', 'big');
        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Component System Runner Finished.', 'big');
    }

    // ======================================================
    // Function: generate
    // Inputs: none
    // Outputs: void
    // Purpose: Calls ComponentGenerator to generate the component
    // Logic Walkthrough:
    //   1. Logs start of generator
    //   2. Calls generateComponent() on ComponentGenerator
    //   3. Logs end of generator
    // External Functions/Helpers:
    //   - Debugger()->header()
    //   - ComponentGenerator->generateComponent()
    // Side Effects: logs generation progress
    // ======================================================
    private function generate(): void{
        Debugger()->header('Component Generator Started.', 'medium');
        $this->componentGenerator->generateComponent();
        Debugger()->header('Component Generating Finished.', 'medium');
    }

    // ======================================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for implementing component validation
    // Logic: Currently not implemented
    // Side Effects: none
    // ======================================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ======================================================
    // Function: resolve
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves the component context and publishes it
    // Logic Walkthrough:
    //   1. Logs start of resolver
    //   2. Calls resolveComponentContext() on ComponentResolver
    //   3. Publishes the resulting DTO using publishDTO()
    //   4. Logs end of resolver
    // External Functions/Helpers:
    //   - Debugger()->header()
    //   - ComponentResolver->resolveComponentContext()
    //   - publishDTO()
    // Side Effects:
    //   - Publishes component context to ContextBus
    // ======================================================
    private function resolve(): void
    {
        Debugger()->header('Component Resolver Started.', 'medium');
        $componentContextDTO = $this->componentResolver->resolveComponentContext();
        $this->publishDTO(ComponentContextDTO::class, $componentContextDTO);
        Debugger()->header('Component Resolving Finished.', 'medium');
    }
    
    // ======================================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: class name or key of the DTO to publish
    //   - object $dto: the DTO object to publish
    // Outputs: void
    // Purpose: Publishes a DTO to the global ContextBus
    // Logic Walkthrough:
    //   1. Logs start of publishing
    //   2. Publishes DTO to ContextBus
    //   3. Logs info about published context
    //   4. Logs end of publishing
    // External Functions/Helpers:
    //   - Debugger()->header()
    //   - Debugger()->info()
    //   - ContextBus()->publish()
    // Side Effects:
    //   - Modifies global context bus state
    // ======================================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Component Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Component Context Publishing Finished.', 'medium');
    }
}
