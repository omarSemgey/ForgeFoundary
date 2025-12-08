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

// ===============================================
// Class: ConfigResolver
// Purpose: Resolves both the main configuration and the currently selected mode configuration
//          for ForgeFoundary. Handles CLI overrides, defaults, YAML loading, and paths normalization.
// Functions:
//   - __construct(): injects ConfigManager and PathManager
//   - resolveConfigsContext(): orchestrates loading main + mode configs and returns a DTO
//   - resolveMainConfigName(): determines main config filename
//   - resolveMainConfigPath(): determines main config path
//   - resolveMainConfigValue(): loads main config YAML
//   - resolveModeConfigName(): determines mode config filename
//   - resolveModesConfigPath(): determines path where mode configs are located
//   - resolveModeConfigValue(): loads mode config YAML
//   - findMode(): searches for mode YAML in modes subdirectories
// ===============================================
class ConfigResolver
{
    private string $mainConfigName;
    private string $mainConfigPath;
    private array $mainConfigValue;
    
    private string $modeConfigName;
    private string $modesConfigPath;
    private array $modeConfigValue;

    private CliInputContextDTO $cliInputContextDTO;

    // Default values for main config when not provided by user
    private const DEFAULT_MAIN_CONFIG_VALUES = [
        "name" => "ForgeFoundary",
        "path" => "Configs",
    ];

    // Key mapping for main config entries
    private const CONFIG_CONFIG_KEYS = [
        "modes_path" => "modes_path",
        "mode" => "mode",
    ];

    public function __construct(
        private ConfigManager $configManager,
        private PathManager $pathManager,
    ) {}

    // ===============================================
    // Private: loadContexts
    // Purpose: retrieve CLI input context from the ContextBus
    // Side Effects:
    //   - sets $this->cliInputContextDTO
    //   - logs info via Debugger
    // ===============================================
    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    // ===============================================
    // Public: resolveConfigsContext
    // Inputs: none (uses injected helpers and context)
    // Outputs: ConfigContextDTO containing main and mode configs
    // Purpose: orchestrates resolution of main config + mode config
    // Logic:
    //   1. Load CLI input context
    //   2. Resolve main config: name, path, values
    //   3. Resolve mode config: name, path, values
    //   4. Return a DTO encapsulating both configs
    // Side Effects: loads config into ConfigManager
    // ===============================================
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

    // ===============================================
    // Private: resolveMainConfigName
    // Purpose: determine main config filename from CLI or fallback to default
    // Side Effects: sets $this->mainConfigName and logs info/warnings
    // ===============================================
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

    // ===============================================
    // Private: resolveMainConfigPath
    // Purpose: determine main config path from CLI or default
    // Side Effects: sets $this->mainConfigPath and logs info/warnings
    // ===============================================
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

    // ===============================================
    // Private: resolveMainConfigValue
    // Purpose: load main config YAML file
    // Logic:
    //   1. Determine .yaml or .yml path
    //   2. Parse YAML using Symfony Yaml parser
    //   3. Load config into ConfigManager
    // Side Effects:
    //   - sets $this->mainConfigValue
    //   - may throw RuntimeException if no file found
    // ===============================================
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

    // ===============================================
    // Private: resolveModesConfigPath
    // Purpose: determine folder path where mode configs reside
    // Side Effects: sets $this->modesConfigPath and logs info
    // ===============================================
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

    // ===============================================
    // Private: resolveModeConfigName
    // Purpose: determine which mode to load based on CLI or main config default
    // Side Effects: sets $this->modeConfigName and logs info
    // ===============================================
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

    // ===============================================
    // Private: resolveModeConfigValue
    // Purpose: parse YAML mode config file
    // Logic:
    //   1. Search mode file using findMode()
    //   2. Parse YAML
    //   3. Load into ConfigManager
    // Side Effects:
    //   - sets $this->modeConfigValue
    //   - logs info
    // ===============================================
    private function resolveModeConfigValue(): void
    {
        Debugger()->info("Attempting to load mode config '{$this->modeConfigName}' from path: '{$this->modesConfigPath}'");
        
        $modePath = $this->findMode();

        $this->modeConfigValue = Yaml::parseFile($modePath);
        $this->configManager::loadConfig('mode_config', $this->modeConfigValue);

        Debugger()->info("Loaded mode config file: '{$modePath}'");
        Debugger()->info("Mode '{$this->modeConfigName}' loaded successfully");
    }

    // ===============================================
    // Private: findMode
    // Purpose: locate YAML file for the given mode inside all subdirectories
    // Outputs: string path to the mode YAML file
    // Logic:
    //   1. Iterate over subdirectories of $this->modesConfigPath
    //   2. Check for {$mode}.yaml or {$mode}.yml in each subdir
    //   3. Return path if found, else throw RuntimeException
    // Side Effects: none
    // ===============================================
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
