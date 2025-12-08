<?php

namespace App\Src\Domains\CliFlags\Runners;

use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Domains\CliFlags\Executors\CliFlagsExecuter;
use App\Src\Domains\CliFlags\Resolvers\CliFlagsResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: CliFlagsSystemRunner
// Purpose: Orchestrates the entire CLI Flags system.
//          Validates, resolves, and executes CLI flags.
//          Publishes the CLI Flags context to the global ContextBus.
// Functions:
//   - __construct(): injects resolver, executer, and system state manager
//   - run(): main runner method for the CLI flags system
//   - validate(): placeholder for future validation logic
//   - resolve(): resolves CLI flags and publishes the context
//   - execute(): executes the resolved CLI flags
//   - publishDTO(): publishes a DTO to the global ContextBus
// ===============================================
class CliFlagsSystemRunner
{
    public function __construct(
        private CliFlagsResolver $CliFlagsResolver,
        private CliFlagsExecuter $cliFlagsExecuter,
        private SystemStateManager $systemStateManager,
    ){}
    
    // ===============================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Main entry point to run the CLI Flags system
    // Logic Walkthrough:
    //   1. Prints header: "Cli Flags System Runner Started"
    //   2. Checks via SystemStateManager if CLI flags system is enabled
    //      - If not enabled, returns early
    //   3. Calls validate() (currently a TODO placeholder)
    //   4. Calls resolve() to resolve CLI flags and publish the DTO
    //   5. Calls execute() to run the CLI flags logic
    //   6. Prints header: "Cli Flags System Runner Finished"
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - SystemStateManager->assertEnabled()
    //   - validate(), resolve(), execute()
    // Side Effects:
    //   - May publish a CLI Flags DTO to ContextBus
    // ===============================================
    public function run(): void
    {
        Debugger()->header('Cli Flags System Runner Started.', 'big');

        if(!$this->systemStateManager->assertEnabled('cli_flags', 'Cli Flags')){
            return;
        }

        $this->validate();
        $this->resolve();
        $this->execute();
        Debugger()->header('Cli Flags System Runner Finished.', 'big');
    }

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for CLI flags validation logic
    // Logic Walkthrough: Not implemented yet
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
    // Purpose: Resolves CLI flags into a context DTO and publishes it globally
    // Logic Walkthrough:
    //   1. Prints header: "Cli Flags Resolver Started"
    //   2. Calls CliFlagsResolver->resolveCliFlagsContext() to get context DTO
    //   3. Calls publishDTO() to publish the DTO to ContextBus
    //   4. Prints header: "Cli Flags Resolver Finished"
    // External Functions/Helpers Used:
    //   - CliFlagsResolver->resolveCliFlagsContext()
    //   - publishDTO()
    //   - Debugger()->header()
    // Side Effects:
    //   - Publishes a CLI Flags context DTO to the global ContextBus
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Cli Flags Resolver Started.', 'medium');
        $cliFlagsContextDTO = $this->CliFlagsResolver->resolveCliFlagsContext();
        $this->publishDTO(CliFlagsContextDTO::class, $cliFlagsContextDTO);
        Debugger()->header('Cli Flags Resolver Finished.', 'medium');
    }

    // ===============================================
    // Function: execute
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the CLI flags logic
    // Logic Walkthrough:
    //   1. Prints header: "Cli Flags Executer Started"
    //   2. Calls CliFlagsExecuter->executeCliFlags()
    //   3. Prints header: "Cli Flags Executer Finished"
    // External Functions/Helpers Used:
    //   - CliFlagsExecuter->executeCliFlags()
    // Side Effects: Executes CLI flags logic, may affect internal state
    // ===============================================
    private function execute(): void
    {
        Debugger()->header('Cli Flags Executer Started.', 'medium');
        $this->cliFlagsExecuter->executeCliFlags();
        Debugger()->header('Cli Flags Executer Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: Key name to register DTO under in ContextBus
    //   - object $dto: DTO object to publish
    // Outputs: void
    // Purpose: Publishes a DTO to the global ContextBus and logs via Debugger
    // Logic Walkthrough:
    //   1. Prints header: "Cli Flags Context Publisher Started"
    //   2. Publishes the DTO to ContextBus using ContextBus()->publish()
    //   3. Logs info via Debugger about the published context
    //   4. Prints header: "Cli Flags Context Publisher Finished"
    // External Functions/Helpers Used:
    //   - ContextBus()->publish()
    //   - Debugger()->header(), Debugger()->info()
    // Side Effects:
    //   - Updates global ContextBus with a new DTO
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Cli Flags Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Cli Flags Context Publisher Finished.', 'medium');
    }
}
