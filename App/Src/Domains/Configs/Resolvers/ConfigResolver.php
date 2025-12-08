<?php

namespace App\Src\Domains\Configs\Resolvers;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\Configs\Helpers\ConfigManager;
use Log;
use RuntimeException;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use Str;
use Symfony\Component\Yaml\Yaml;

class ConfigResolver
{
    private string $mainConfigName;
    private string $mainConfigPath;
    private array $mainConfigValue;
    
    private string $modeConfigName;
    private string $modesConfigPath;
    private array $modeConfigValue;
    private CliInputContextDTO $cliInputContextDTO;
    private const DEFAULT_MAIN_CONFIG_VALUES = [
        "name" => "ForgeFoundary",
        "path" => "Configs",
    ];

    private const CONFIG_CONFIG_KEYS = [
        "modes_path" => "modes_path",
        "mode" => "mode",
    ];
    
    public function __construct(
        private ConfigManager $configManager,
        private PathManager $pathManager,
    ) {}

    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    public function resolveConfigsContext(): ConfigContextDTO
    {
        $this->loadContexts();
        $this->resolveMainConfigName();
        $this->resolveMainConfigPath();
        $this->resolveMainConfigValue();
    
        $this->resolveModeConfigName();
        $this->resolveModesConfigPath();
        $this->resolveModeConfigValue();

        return new ConfigContextDTO(
            // Main Config
            $this->mainConfigPath,
            $this->mainConfigName,
            $this->mainConfigValue,
            // Mode Config
            $this->modeConfigName,
            $this->modesConfigPath,
            $this->modeConfigValue,
        );
    }

    private function resolveMainConfigName(): void{
        $customMainConfigName = $this->cliInputContextDTO->getOption('custom-config-name');

        if ($customMainConfigName){
            $this->mainConfigName = $customMainConfigName;
            Debugger()->info("Main config name: '{$customMainConfigName}'");
            return;
        }

        $this->mainConfigName = self::DEFAULT_MAIN_CONFIG_VALUES['name'];
        Debugger()->warning("No custom main config name provided; using default: '{$this->mainConfigName}'");
    }

    private function resolveMainConfigPath(): void{
        $customMainConfigPath = $this->cliInputContextDTO->getOption('custom-config-path');

        if ($customMainConfigPath){
            $this->mainConfigPath = $customMainConfigPath;
            Debugger()->info("Main config file path: '{$customMainConfigPath}'" );
            return;
        }

        $path = self::DEFAULT_MAIN_CONFIG_VALUES['path'];
        $this->mainConfigPath = $this->pathManager->getAbsolutePath($path, true);

        Debugger()->warning("No custom config path provided. Using default: {$this->mainConfigPath}" );
    }

    private function resolveMainConfigValue(): void{
        Debugger()->info("Attempting to load main config '{$this->mainConfigName}' from path: '{$this->mainConfigPath}'");
        $yamlPath = $this->pathManager->normalizeSlashes($this->mainConfigPath . '/' . $this->mainConfigName . '.yaml');
        $ymlPath = $this->pathManager->normalizeSlashes($this->mainConfigPath . '/' . $this->mainConfigName . '.yml');
        
        $this->mainConfigValue = [];
    
        if (file_exists($yamlPath)) {
            $this->mainConfigValue = Yaml::parseFile($yamlPath);
            Debugger()->info("Loaded YAML config file: '{$yamlPath}'");
        } elseif (file_exists($ymlPath)) {
            $this->mainConfigValue = Yaml::parseFile($ymlPath);
            Debugger()->info("Loaded YAML config file: '{$ymlPath}'");
        }
        else{
            throw new RuntimeException("No main config file found");
        }
        $this->configManager::loadConfig('main_config', $this->mainConfigValue);
        
        Debugger()->info("Main config: '{$this->mainConfigName}' loaded successfully");
    }

    private function resolveModesConfigPath(): void
    {
        $customModesPath = $this->cliInputContextDTO->getOption('modes-path');

        if ($customModesPath){
            $this->modesConfigPath = $customModesPath;
            Debugger()->info("Modes path: '{$customModesPath}'" );
            return;
        }
    
        $path = Config()->get("main_config." . self::CONFIG_CONFIG_KEYS['modes_path']);
        $this->modesConfigPath = $this->pathManager->getAbsolutePath($path, true);
        Debugger()->info("Modes path: '{$this->modesConfigPath}'");
    }

    private function resolveModeConfigName(): void{
        $customModeConfigName = $this->cliInputContextDTO->getOption('mode');

        if ($customModeConfigName){
            $this->modeConfigName = $customModeConfigName;
            Debugger()->info("Loaded mode name: '{$customModeConfigName}'" );
            return;
        }
        
        $this->modeConfigName = Config()->get( "main_config." . self::CONFIG_CONFIG_KEYS['mode']);
        Debugger()->info("Loaded mode name: '{$this->modeConfigName}'" );
    }

    private function resolveModeConfigValue(): void
    {
        Debugger()->info("Attempting to load mode config '{$this->modeConfigName}' from path: '{$this->modesConfigPath}'");
        
        $modePath = $this->findMode();

        $this->modeConfigValue = Yaml::parseFile($modePath);
        $this->configManager::loadConfig('mode_config', $this->modeConfigValue);

        Debugger()->info("Loaded mode config file: '{$modePath}'");
        Debugger()->info("Mode '{$this->modeConfigName}' loaded successfully");
    }

    private function findMode(): string{

        $foundPath = null;

        foreach (scandir($this->modesConfigPath) as $subDir) {
            $fullSubDirPath = $this->pathManager->normalizeSlashes($this->modesConfigPath . '/' . $subDir);

            if ($subDir === '.' || $subDir === '..') continue;
            if (!is_dir($fullSubDirPath)) continue;

            $yamlFile = $this->pathManager->normalizeSlashes($fullSubDirPath . '/' . $this->modeConfigName . '.yaml');
            $ymlFile  = $this->pathManager->normalizeSlashes($fullSubDirPath . '/' . $this->modeConfigName . '.yml');

            if (file_exists($yamlFile)) {
                $foundPath = $yamlFile;
                break;
            } elseif (file_exists($ymlFile)) {
                $foundPath = $ymlFile;
                break;
            }
        }

        if (!$foundPath) {
            throw new RuntimeException("No mode config file found for '{$this->modeConfigName}' in any subfolder of '{$this->modesConfigPath}'");
        }

        return $foundPath;
    }
}