<?php

namespace App\Src\Domains\CliFlags\Executors;

use Arr;
use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\CliFlags\Helpers\ConfigLoader;
use Symfony\Component\Yaml\Yaml;

class CliFlagsExecuter
{
    private ConfigContextDTO $configContextDTO;
    private CliFlagsContextDTO $cliFlagsContextDTO;

    public function __construct(protected ConfigLoader $configLoader,){}

    private function loadContexts(): void{
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
        $this->cliFlagsContextDTO = ContextBus()->get(CliFlagsContextDTO::class);
        Debugger()->info("Loaded context: 'CliFlagsContextDTO' from the context bus");
    }

    public function executeCliFlags(): void{
        $this->loadContexts();
        $configValue = Yaml::dump($this->configContextDTO->modeValue);
        
        foreach($this->cliFlagsContextDTO->providedCliFlags as $flag){
            [$key, $providedValue] = explode('=', $flag, 2);
            $defaultValue = $this->cliFlagsContextDTO->definedCliFlags[$key];
            Debugger()->info("'{$key}' cli flag overrided the default value: '{$defaultValue}' to the provided value: '{$providedValue}'");
            $configValue = str_replace($defaultValue, $providedValue, $configValue);
        }
        $configValue = Yaml::parse($configValue);
        ContextBus()->mutateModeValue($configValue);
        $this->configLoader->loadConfig('mode_config', $configValue);
    }
}