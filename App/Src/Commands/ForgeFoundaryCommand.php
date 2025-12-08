<?php

namespace App\Src\Commands;

use App\Src\Domains\Commands\Runners\CommandSystemRunner;
use App\Src\Domains\Components\Runners\ComponentSystemRunner;
use App\Src\Domains\Directories\Runners\DirectorySystemRunner;
use App\Src\Domains\Templates\Runners\TemplateSystemRunner;
use App\Src\Domains\Units\Runners\UnitSystemRunner;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use App\Src\Core\Helpers\TreeManager;

// ===============================================
// Class: ForgeFoundaryCommand
// Purpose: Entry point for executing ForgeFoundary from the CLI.
//          Initializes the tool, runs all system runners in order, 
//          manages CLI contexts, and outputs reports/logs.
// Functions:
//   - __construct(): injects all system runners and helpers
//   - loadContexts(): loads CLI input context from ContextBus
//   - handle(): main command handler, executes all system runners and debugging/reporting
// ===============================================
class ForgeFoundaryCommand extends Command
{
    // CLI command signature and options
    protected $signature = 'scaffold {--mode=} {--modes-path=} {--tree-view} {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
    protected $description = 'Run ForgeFoundary command';

    public function __construct(
        private Bootstrapper $bootstrapper,
        private TreeManager $treeManager,
        private ComponentSystemRunner $componentSystemRunner,   
        private DirectorySystemRunner $directorySystemRunner,
        private UnitSystemRunner $unitSystemRunner, 
        private TemplateSystemRunner $templateSystemRunner,
        private CommandSystemRunner $commandSystemRunner,
        private PathManager $pathManager,
    )
    {
        parent::__construct();
    }

    // Holds CLI input context
    private CliInputContextDTO $cliInputContextDTO;

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads the CLI input context from the ContextBus
    // Logic Walkthrough:
    //   1. Retrieves CliInputContextDTO instance from ContextBus
    //   2. Logs an info message via Debugger that context was loaded
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects: none
    // ===============================================
    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    // ===============================================
    // Function: handle
    // Inputs: none (uses CLI arguments/options internally)
    // Outputs: void
    // Purpose: Main command execution method for ForgeFoundary
    // Logic Walkthrough:
    //   1. Defines TOOL_BASE_PATH based on the resolved tool directory
    //   2. Boots all systems via Bootstrapper
    //   3. Logs command start using Debugger
    //   4. Loads CLI input context
    //   5. Runs all system runners in order:
    //       - Components
    //       - Directories
    //       - Units
    //       - Templates
    //       - Commands
    //   6. Logs command completion via Debugger
    //   7. Outputs reports via Reporter
    //   8. Renders tree view if the tree-view option is set
    // External Functions/Helpers Used:
    //   - $this->pathManager->resolveToolPath()
    //   - $this->bootstrapper->boot()
    //   - Debugger()->header()
    //   - $this->componentSystemRunner->run()
    //   - $this->directorySystemRunner->run()
    //   - $this->unitSystemRunner->run()
    //   - $this->templateSystemRunner->run()
    //   - $this->commandSystemRunner->execute()
    //   - Reporter()->report()
    //   - $this->treeManager->renderTree()
    // Side Effects:
    //   - Modifies tool state by running all scaffolding systems
    //   - Outputs logs and reports
    // ===============================================
    public function handle(): void
    {
        define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
        $this->bootstrapper->boot($this);
     
        Debugger()->header('Forge Foundary Command Started.', 'huge');

        $this->loadContexts();
        $this->componentSystemRunner->run();
        $this->directorySystemRunner->run();
        $this->unitSystemRunner->run();
        $this->templateSystemRunner->run();
        $this->commandSystemRunner->execute(false);

        Debugger()->header('Forge Foundary Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
        
        Reporter()->report($this->components);
        if($this->cliInputContextDTO->getOption("tree-view")){
            $this->treeManager->renderTree($this);
        }
    }
}
