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

class Bootstrapper
{
    public function __construct(
        private ConfigSystemRunner $configSystemRunner, 
        private CliFlagsSystemRunner $cliFlagsSystemRunner, 
        private NamingConventionsSystemRunner $namingConventionsSystemRunner,
        private CommandSystemRunner $commandSystemRunner,
        private CliInputResolver $cliInputResolver,
        ){}

    public function boot(Command $command): void
    {
        $contextBus = ContextBus::getInstance();
        
        $this->cliInputResolver->resolve($command);
     
        // Note: debugger MUST be initialized after cli input is resulved
        $debugger = Debugger::getInstance();
        
        $debugger->boot($command);

        // Requiring aliases moved to ForgeFoundary file
        // require_once base_path('App/Src/Core/Aliases/Aliases.php');

        $this->configSystemRunner->run();
        
        $this->cliFlagsSystemRunner->run();
        
        $this->commandSystemRunner->run(true);

        $this->namingConventionsSystemRunner->run();

        $contextBus->disableMutation();
    }
}

