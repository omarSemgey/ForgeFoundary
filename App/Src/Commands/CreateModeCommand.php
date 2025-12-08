<?php

namespace App\Src\Commands;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use RuntimeException;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;

class CreateModeCommand extends Command
{
    protected $signature = 'create-mode {--mode-name=} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
    protected $description = 'Create a new mode for the ForgeFoundary command';

    public function __construct(
        private Bootstrapper $bootstrapper,
        private PathManager $pathManager,
        private Filesystem $files,
    )
    {
        parent::__construct();
    }

    private ConfigContextDTO $configContextDTO;
    private CliInputContextDTO $cliInputContextDTO;

    private function loadContext(): void{
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }
    public function handle(): void
    {
        define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
        $this->bootstrapper->boot($this);
        $this->loadContext();

        Debugger()->header('Forge Foundary Create Mode Command Started.', 'huge');

        $this->generateMode();
     
        Debugger()->header('Forge Foundary Create Mode Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
    }

    private function generateMode(): void{
        $modeName = $this->cliInputContextDTO->getOption("mode-name");

        if(!$modeName){
            throw new RuntimeException("No mode name given");
        }

        $modesPath = $this->configContextDTO->modesPath;
        $modePath = $this->pathManager->normalizeSlashes($modesPath . "/" . $modeName);
        $modeFilePath = $this->pathManager->normalizeSlashes($modePath . "/" . $modeName . ".yaml");
        $modeTemplatesPath = $this->pathManager->normalizeSlashes($modePath . "/Templates");
        $modeTemplatePath = $this->pathManager->normalizeSlashes(TOOL_BASE_PATH . "/Core/Templates/mode-template.yaml");
        $modeTemplateValue = file_get_contents($modeTemplatePath);

        $this->files->makeDirectory($modePath, 0755, true);
        $this->files->makeDirectory($modeTemplatesPath, 0755, true);
        
        File::put($modeFilePath, $modeTemplateValue);

        Debugger()->info("Created mode '{$modeName}' at path '{$modePath}");
    }
}