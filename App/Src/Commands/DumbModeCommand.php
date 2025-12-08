<?php

namespace App\Src\Commands;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use Symfony\Component\Yaml\Yaml;

class DumbModeCommand extends Command
{
    protected $signature = 'dumb-mode {--mode=} {--modes-path=} {--raw-yaml} {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
    protected $description = 'Dump the configuration values for the ForgeFoundry command selected mode';
    private ConfigContextDTO $configContextDTO;
    private CliInputContextDTO $cliInputContextDTO;

    public function __construct(
        private Bootstrapper $bootstrapper,
        private PathManager $pathManager,
    )
    {
        parent::__construct();
    }

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

        Debugger()->header('Forge Foundary Dumb Mode Command Started.', 'huge');
        $this->dumbMode($this->configContextDTO->modeValue, $this->configContextDTO->modeName);
     
        Debugger()->header('Forge Foundary Dumb Mode Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
    }

    private function dumbMode(array $modeValue, string $modeName): void
    {
        $this->line("===== {$modeName} Value =====");
    
        if($this->cliInputContextDTO->getOption('raw-yaml')){
            $this->printRawYaml($modeValue);
            $this->line("==========");
            return;
        }

        $this->printTree($modeValue);
        $this->line("==========");
    }

    private function printTree(array $modeValue, string $prefix = '   '): void{
        $count = count($modeValue);
        $i = 0;

        foreach ($modeValue as $key => $value) {
            $i++;
            $connector = $i === $count ? '└─ ' : '├─ ';

            if (is_array($value)) {
                $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                $this->line($prefix . $connector . $key);
                $newPrefix = $prefix . ($i === $count ? '   ' : '│  ');

                if ($isAssoc) {
                    $this->printTree($value, $newPrefix);
                } else {
                    foreach ($value as $idx => $item) {
                        $itemConnector = $idx === count($value) - 1 ? '└─ ' : '├─ ';
                        if (is_array($item)) {
                            $this->line($newPrefix . $itemConnector . '[list]');
                            $this->printTree($item, $newPrefix . ($idx === count($value) - 1 ? '   ' : '│  '));
                        } else {
                            $this->line($newPrefix . $itemConnector . $item);
                        }
                    }
                }
            } else {
                $this->line($prefix . $connector . "{$key} → {$value}");
            }
        }
    }

    private function printRawYaml(array $modeValue): void{
        $this->line(Yaml::dump($modeValue, 4, 2));
    }
}