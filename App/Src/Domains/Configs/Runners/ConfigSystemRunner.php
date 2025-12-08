<?php

namespace App\Src\Domains\Configs\Runners;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\Configs\Resolvers\ConfigResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: ConfigSystemRunner
// Purpose: Handles initialization of the "Configs" system.
//          Validates, resolves, and publishes configuration context to the global ContextBus.
// Functions:
//   - __construct(): injects the resolver and system state manager
//   - run(): orchestrates the full lifecycle of config system boot
//   - validate(): placeholder for validation logic of configs
//   - resolve(): resolves the configs into a context DTO and publishes it
//   - publishDTO(): publishes a DTO object to the ContextBus
// ===============================================
class ConfigSystemRunner
{
    public function __construct(
        private ConfigResolver $configResolver,
        private SystemStateManager $systemStateManager,
    ) {}
        
    // ===============================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the full config system lifecycle
    // Logic Walkthrough:
    //   1. Logs "Configs System Runner Started"
    //   2. Calls validate() to perform configuration validation (currently empty)
    //   3. Calls resolve() to resolve and publish configs to ContextBus
    //   4. Logs "Configs System Runner Finished"
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - validate()
    //   - resolve()
    // Side Effects:
    //   - Publishes ConfigContextDTO to ContextBus
    //   - Logs messages to Debugger
    // ===============================================
    public function run(): void
    {
        Debugger()->header('Configs System Runner Started.', 'big');
        $this->validate();
        $this->resolve();
        Debugger()->header('Configs System Runner Finished.', 'big');
    }

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for config validation logic
    // Logic Walkthrough: TODO: Implement validation layer
    // Side Effects: None
    // ===============================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ===============================================
    // Function: resolve
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves configuration context into a DTO and publishes it globally
    // Logic Walkthrough:
    //   1. Logs "Configs Resolver Finished" (medium header)
    //   2. Resolves config context DTO via configResolver
    //   3. Calls publishDTO() to publish the DTO to ContextBus
    //   4. Logs "Configs Resolver Finished" again
    // External Functions/Helpers Used:
    //   - configResolver->resolveConfigsContext()
    //   - publishDTO()
    //   - Debugger()->header()
    // Side Effects:
    //   - Publishes ConfigContextDTO to ContextBus
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Configs Resolver Finished.', 'medium');
        $configContextDTO = $this->configResolver->resolveConfigsContext();
        $this->publishDTO(ConfigContextDTO::class, $configContextDTO);
        Debugger()->header('Configs Resolver Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: Key under which the DTO will be published in ContextBus
    //   - object $dto: DTO object to be published
    // Outputs: void
    // Purpose: Publishes a DTO object to the global ContextBus
    // Logic Walkthrough:
    //   1. Logs "Configs Context Publisher Finished" (medium header)
    //   2. Publishes the DTO to ContextBus using the given key
    //   3. Logs info about the published context
    //   4. Logs "Configs Context Publisher Finished" again
    // External Functions/Helpers Used:
    //   - ContextBus()->publish()
    //   - Debugger()->header()
    //   - Debugger()->info()
    // Side Effects:
    //   - Modifies the global ContextBus by adding the DTO
    //   - Logs messages to Debugger
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Configs Context Publisher Finished.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Configs Context Publisher Finished.', 'medium');
    }
}
