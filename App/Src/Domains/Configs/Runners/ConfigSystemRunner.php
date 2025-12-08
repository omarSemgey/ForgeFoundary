<?php

namespace App\Src\Domains\Configs\Runners;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use App\Src\Domains\Configs\Resolvers\ConfigResolver;
use App\Src\Core\Contexts\ContextBus;
use App\Src\Core\Helpers\SystemStateManager;

class ConfigSystemRunner
{
    // private ConfigResolver $configResolver;

    public function __construct(
        private ConfigResolver $configResolver,
        private SystemStateManager $systemStateManager,
        ) {}
        
    public function run(): void
    {
        Debugger()->header('Configs System Runner Started.', 'big');
        $this->validate();
        $this->resolve();
        Debugger()->header('Configs System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function resolve(): void
    {
        Debugger()->header('Configs Resolver Finished.', 'medium');
        $configContextDTO = $this->configResolver->resolveConfigsContext();
        $this->publishDTO(ConfigContextDTO::class, $configContextDTO);
        Debugger()->header('Configs Resolver Finished.', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Configs Context Publisher Finished.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Configs Context Publisher Finished.', 'medium');
    }
}
