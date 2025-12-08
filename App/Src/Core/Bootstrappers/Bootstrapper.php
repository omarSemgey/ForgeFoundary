<?php

namespace App\Src\Core\Bootstrappers;

use App\Src\Domains\Commands\Runners\CommandSystemRunner;
use App\Src\Core\Debuggers\Debugger;
use App\Src\Core\Contexts\ContextBus;
use App\Src\Domains\Configs\Runners\ConfigSystemRunner;
use App\Src\Domains\CliFlags\Runners\CliFlagsSystemRunner;
use App\Src\Core\NamingConventions\Runners\NamingConventionsSystemRunner;
use Illuminate\Console\Command;
use App\Src\Core\Helpers\CliInputResolver;

// ===============================================
// Class: Bootstrapper
// Purpose: Central initializer for ForgeFoundary. 
//          Coordinates bootstrapping of core systems including configs, CLI flags, 
//          naming conventions, commands, and debugging. 
//          Ensures proper sequence of initialization for all systems.
// Functions:
//   - __construct(): injects all system runners and the CLI input resolver
//   - boot(Command $command): performs full bootstrapping sequence for a CLI command
// ===============================================
class Bootstrapper
{
        public function __construct(
        private ConfigSystemRunner $configSystemRunner, 
        private CliFlagsSystemRunner $cliFlagsSystemRunner, 
        private NamingConventionsSystemRunner $namingConventionsSystemRunner,
        private CommandSystemRunner $commandSystemRunner,
        private CliInputResolver $cliInputResolver,
    ){}

    // ===============================================
    // Function: boot
    // Inputs:
    //   - Command $command: The current CLI command being executed
    // Outputs: void
    // Purpose: Bootstraps all systems of ForgeFoundary in the correct sequence
    // Logic Walkthrough:
    //   1. Retrieves the singleton ContextBus instance
    //   2. Resolves CLI input using CliInputResolver
    //   3. Initializes Debugger (must happen after CLI input is resolved)
    //   4. Boots Debugger with the current command context
    //   5. Runs ConfigSystemRunner to initialize configurations
    //   6. Runs CliFlagsSystemRunner to initialize CLI flags
    //   7. Runs CommandSystemRunner (with `true` argument to indicate full execution)
    //   8. Runs NamingConventionsSystemRunner to enforce naming rules
    //   9. Disables context mutation in ContextBus to prevent runtime changes
    // Side Effects:
    //   - Initializes global systems
    // ===============================================

    public function boot(Command $command): void
    {
        $contextBus = ContextBus::getInstance();
        
        $this->cliInputResolver->resolve($command);
     
        // Note: debugger MUST be initialized after CLI input is resolved
        $debugger = Debugger::getInstance();
        
        $debugger->boot($command);

        $this->configSystemRunner->run();
        
        $this->cliFlagsSystemRunner->run();
        
        $this->commandSystemRunner->run(true);

        $this->namingConventionsSystemRunner->run();

        $contextBus->disableMutation();
    }
}
