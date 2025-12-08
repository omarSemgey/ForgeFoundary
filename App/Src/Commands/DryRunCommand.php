<?php

namespace App\Src\Commands;

use App\Src\Domains\Commands\Runners\CommandSystemRunner;
use App\Src\Domains\Components\Runners\ComponentSystemRunner;
use App\Src\Domains\Directories\Runners\DirectorySystemRunner;
use App\Src\Domains\Templates\Runners\TemplateSystemRunner;
use App\Src\Domains\Units\Runners\UnitSystemRunner;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\Helpers\TreeManager;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

class DryRunCommand extends Command
{
    protected $signature = 'ForgeFoundary:dry-run {--mode=} {--modes-path=} {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
    protected $description = 'Dry run the ForgeFoundary command';

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

    private const DRY_RUN_COMMAND_CONFIG_KEYS = [
        "component_path" => "component_path"
    ];

    public function handle(): void
    {
        define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));

        $tempPath = sys_get_temp_dir() . '/forge_dry_run_' . uniqid();
        File::makeDirectory($tempPath, 0755, true);

        $this->overrideComponentPath($tempPath);
    
        $this->bootstrapper->boot($this);
     
        Debugger()->header('Forge Foundary Command Started.', 'huge');

        $this->componentSystemRunner->run();
        $this->directorySystemRunner->run();
        $this->unitSystemRunner->run();
        $this->templateSystemRunner->run();
        $this->commandSystemRunner->execute(false);

        $this->treeManager->renderTree($this);

        Debugger()->header('Forge Foundary Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
        File::deleteDirectory($tempPath);
    }

    private function overrideComponentPath(string $path): void
    {
        $customs = $this->option('custom') ?? [];

        $keyPrefix = self::DRY_RUN_COMMAND_CONFIG_KEYS["component_path"] . "=";

        $foundIndex = null;
        foreach ($customs as $i => $value) {
            if (str_starts_with($value, $keyPrefix)) {
                $foundIndex = $i;
                break;
            }
        }

        if ($foundIndex !== null) {
            $customs[$foundIndex] = $keyPrefix . $path;
        } else {
            $customs[] = $keyPrefix . $path;
        }

        $this->input->setOption('custom', $customs);
    }
}