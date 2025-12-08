<?php

namespace App\Src\Domains\Commands\Runners;

use App\Src\Domains\Commands\DTOs\CommandsContextDTO;
use App\Src\Domains\Commands\Executors\CommandsExecuter;
use App\Src\Domains\Commands\Resolvers\CommandResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: CommandSystemRunner
// Purpose: Orchestrates the lifecycle of the Commands system.
//          It validates, resolves, and executes commands, and publishes
//          the CommandsContextDTO to the global ContextBus.
// Functions:
//   - __construct(): injects dependencies
//   - run(bool $before): main entry point for running commands system
//   - validate(): placeholder for validation layer
//   - resolve(): resolves commands and publishes context
//   - execute(bool $before): executes resolved commands
//   - publishDTO(string $dtoKey, object $dto): publishes context DTO to ContextBus
// ===============================================
class CommandSystemRunner
{
    // ===============================================
    // Constructor: __construct
    // Inputs:
    //   - CommandResolver $commandResolver: Resolves commands into a usable context
    //   - CommandsExecuter $commandsExecuter: Executes commands according to context
    //   - SystemStateManager $systemStateManager: Ensures the commands system is enabled before running
    // Outputs: none
    // Purpose: Stores dependencies as private properties
    // Logic: Assigns injected dependencies
    // Side Effects: None
    // ===============================================
    public function __construct(
        private CommandResolver $commandResolver,
        private CommandsExecuter $commandsExecuter,
        private SystemStateManager $systemStateManager,
    ){}
        
    // ===============================================
    // Function: run
    // Inputs:
    //   - bool $before: flag indicating if commands should be executed before main process
    // Outputs: void
    // Purpose: Main orchestrator for the Commands system
    // Logic Walkthrough:
    //   1. Logs header that the Commands system has started
    //   2. Checks via SystemStateManager if the 'commands' system is enabled; exits if disabled
    //   3. Calls validate() (currently a placeholder)
    //   4. Calls resolve() to resolve commands and publish their context
    //   5. Calls execute($before) to run the commands
    //   6. Logs header that the Commands system has finished
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - $this->validate()
    //   - $this->resolve()
    //   - $this->execute()
    // Side Effects:
    //   - May publish CommandsContextDTO to ContextBus
    // ===============================================
    public function run(bool $before): void
    {
        Debugger()->header('Commands System Runner Started.', 'big');

        if(!$this->systemStateManager->assertEnabled('commands', 'Commands')){
            return;
        }

        $this->validate();
        $this->resolve();
        $this->execute($before);
        Debugger()->header('Commands System Runner Finished.', 'big');
    }

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: Placeholder for validation layer for commands
    // Logic: TODO: implement validation
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
    // Purpose: Resolves commands into a CommandsContextDTO and publishes it
    // Logic Walkthrough:
    //   1. Logs header that Commands resolver started
    //   2. Uses CommandResolver to create CommandsContextDTO
    //   3. Calls publishDTO() to publish context globally
    //   4. Logs header that Commands resolver finished
    // External Functions/Helpers Used:
    //   - $this->commandResolver->resolveCommandsContext()
    //   - $this->publishDTO()
    // Side Effects:
    //   - Publishes CommandsContextDTO to ContextBus
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Commands Resolver Started.', 'medium');
        $commandsContextDTO = $this->commandResolver->resolveCommandsContext();
        $this->publishDTO(CommandsContextDTO::class, $commandsContextDTO);
        Debugger()->header('Commands Resolver Finished.', 'medium');
    }

    // ===============================================
    // Function: execute
    // Inputs:
    //   - bool $before: indicates if commands should run before main process
    // Outputs: void
    // Purpose: Executes the resolved commands using CommandsExecuter
    // Logic Walkthrough:
    //   1. Logs header that command execution started
    //   2. Calls CommandsExecuter->executeCommands($before)
    //   3. Logs header that command execution finished
    // External Functions/Helpers Used:
    //   - $this->commandsExecuter->executeCommands()
    // Side Effects:
    //   - Executes commands which may have external side effects
    // ===============================================
    public function execute(bool $before):void{
        Debugger()->header('Commands Executer Started.', 'medium');
        $this->commandsExecuter->executeCommands($before);
        Debugger()->header('Commands Executer Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: the key name for publishing in ContextBus
    //   - object $dto: the DTO object to publish
    // Outputs: void
    // Purpose: Publishes the given DTO object to the global ContextBus
    // Logic Walkthrough:
    //   1. Logs header that publishing started
    //   2. Calls ContextBus()->publish($dtoKey, $dto)
    //   3. Logs info about published context
    //   4. Logs header that publishing finished
    // External Functions/Helpers Used:
    //   - ContextBus()->publish()
    //   - Debugger()->header() / Debugger()->info()
    // Side Effects:
    //   - Adds DTO to global ContextBus
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Commands Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Commands Context Publisher Finished.', 'medium');
    }
}
