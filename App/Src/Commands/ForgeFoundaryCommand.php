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

class ForgeFoundaryCommand extends Command
{
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

    private CliInputContextDTO $cliInputContextDTO;

    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

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
