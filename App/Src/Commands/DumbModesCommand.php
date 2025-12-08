<?php

namespace App\Src\Commands;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\Helpers\PathManager;
use Symfony\Component\Yaml\Yaml;

class DumbModesCommand extends Command
{
    protected $signature = 'ForgeFoundary:dumb-modes {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
    protected $description = 'Dump all the modes available for the ForgeFoundry command';
    private ConfigContextDTO $configContextDTO;
    private const DUMB_MODES_COMMAND_CONFIG_KEYS = [
        "mode_metadata" => "mode_metadata"
    ];
    private array $modes; 
    private array $resolvedModes;

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
    }

    public function handle(): void
    {
        define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
        $this->bootstrapper->boot($this);

        $this->loadContext();

        Debugger()->header('Forge Foundary Dumb Modes Command Started.', 'huge');
        
        $this->resolveModes();

        $this->printModes();
     
        Debugger()->header('Forge Foundary Dumb Modes Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
    }

    private function printModes(): void
    {
        foreach ($this->resolvedModes as $modeName => $metadata) {

            // Print mode name
            $this->line($modeName);

            if (empty($metadata)) {
                $this->line("└─  <no metadata>");
                $this->newLine();
                continue;
            }

            $keys = array_keys($metadata);
            $lastKey = end($keys);

            foreach ($metadata as $key => $value) {
                $prefix = ($key === $lastKey) ? "└─" : "├─";

                // Handle multiline description (YAML > strings)
                if (is_string($value) && str_contains($value, "\n")) {
                    $lines = explode("\n", trim($value));

                    // Print first line
                    $this->line("$prefix $key: " . array_shift($lines));

                    // Print remaining lines indented under it
                    foreach ($lines as $line) {
                        $this->line("   │  " . $line);
                    }
                } else {
                    $this->line("$prefix $key: $value");
                }
            }

            $this->newLine();
        }
    }

    private function resolveModes(): void{
        $resolvedModes = [];
        $this->resolveModesNames($this->configContextDTO->modesPath);
        
        foreach($this->modes as $modeName => $modePath){
            $resolvedModes[$modeName] = $this->resolveModeMetadata($modePath);
        }

        $this->resolvedModes = $resolvedModes;
    }

    private function resolveModesNames(string $modesPath): void
    {
        $modes = [];

        foreach (scandir($modesPath) as $subDir) {
            if ($subDir === '.' || $subDir === '..') continue;

            $fullSubDirPath = $this->pathManager->normalizeSlashes($modesPath . '/' . $subDir);

            if (!is_dir($fullSubDirPath)) continue;

            foreach (scandir($fullSubDirPath) as $file) {
                if ($file === '.' || $file === '..') continue;

                if (preg_match('/\.ya?ml$/i', $file)) {
                    $modeName = pathinfo($file, PATHINFO_FILENAME);
                    $modePath = $this->pathManager->normalizeSlashes("$fullSubDirPath/$file");
                    $modes[$modeName] = $modePath;
                }
            }
        }

        $this->modes = $modes;
    }

    private function resolveModeMetadata(string $modePath):array{
        return Yaml::parseFile($modePath)[self::DUMB_MODES_COMMAND_CONFIG_KEYS["mode_metadata"]] ?? [];
    }
}