<?php

namespace App\Src\Core\NamingConventions\Runners;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\Resolvers\NamingConventionsResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: NamingConventionsSystemRunner
// Purpose: Runs the naming conventions subsystem. 
//          Responsible for validating and resolving naming conventions across directories
//          and publishing the results to the global ContextBus.
// Functions:
//   - __construct(): injects resolver and system state manager
//   - run(): main entry point for executing the naming conventions system
//   - validate(): validates naming conventions (currently a placeholder)
//   - resolve(): resolves naming conventions and publishes the context
//   - publishDTO(): publishes a DTO to the ContextBus
// ===============================================
class NamingConventionsSystemRunner
{
    // ===============================================
    // Constructor: __construct
    // Inputs:
    //   - NamingConventionsResolver $namingConventionsResolver: Resolver for naming conventions
    //   - SystemStateManager $systemStateManager: Checks if subsystems are enabled
    // Outputs: none
    // Purpose: Stores injected dependencies for use in run/validate/resolve
    // Side Effects: None
    // ===============================================
    public function __construct(
        private NamingConventionsResolver $namingConventionsResolver, 
        private SystemStateManager $systemStateManager,
    ) {}
    
    // ===============================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the naming conventions system end-to-end
    // Logic Walkthrough:
    //   1. Prints header that system runner started
    //   2. Checks if naming_conventions system is enabled via SystemStateManager
    //      - Returns early if not enabled
    //   3. Calls validate() to run validation layer
    //   4. Calls resolve() to run resolver and publish context
    //   5. Prints header that system runner finished
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - SystemStateManager->assertEnabled()
    // Side Effects:
    //   - Prints to debugger
    //   - May publish resolved context via resolve()
    // ===============================================
    public function run(): void
    {
        Debugger()->header('Naming Conventions System Runner Started.', 'big');

        if (!$this->systemStateManager->assertEnabled('naming_conventions', 'Naming Conventions')) {
            return;
        }

        $this->validate();
        $this->resolve();

        Debugger()->header('Naming Conventions System Runner Finished.', 'big');
    }

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for future validation of naming conventions
    // Logic Walkthrough: none yet (TODO)
    // Side Effects: none
    // ===============================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ===============================================
    // Function: resolve
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves naming conventions using the resolver and publishes the resulting DTO
    // Logic Walkthrough:
    //   1. Prints header that resolver started
    //   2. Calls NamingConventionsResolver->resolveNamingConventionsContext() to get DTO
    //   3. Publishes DTO using publishDTO()
    //   4. Prints header that resolver finished
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - NamingConventionsResolver->resolveNamingConventionsContext()
    //   - publishDTO()
    // Side Effects:
    //   - Publishes NamingConventionsContextDTO to ContextBus
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Naming Conventions Resolver Started.', 'medium');

        $namingConventionContextDTO = $this->namingConventionsResolver->resolveNamingConventionsContext();
        $this->publishDTO(NamingConventionsContextDTO::class, $namingConventionContextDTO);

        Debugger()->header('Naming Conventions Resolver Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: the class name or identifier for the DTO
    //   - object $dto: the actual DTO instance to publish
    // Outputs: void
    // Purpose: Publishes a DTO to the global ContextBus
    // Logic Walkthrough:
    //   1. Prints header that context publishing started
    //   2. Calls ContextBus()->publish() with the given key and DTO
    //   3. Prints info message confirming published context
    //   4. Prints header that context publishing finished
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - Debugger()->info()
    //   - ContextBus()->publish()
    // Side Effects:
    //   - Modifies global context state in ContextBus
    //   - Prints to debugger
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Naming Conventions Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Naming Conventions Context Publisher Finished.', 'medium');
    }
}
