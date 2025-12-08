<?php

namespace App\Src\Core\Contexts;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use RuntimeException;

class ContextBus
{
    private static ?self $instance = null;
    private bool $mutationEnabled = true;

    private array $contexts = [];

    private function __construct() {}

    private function __clone() {}
    
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function publish(string $key, object $dto): void
    {
        $this->contexts[$key] = $dto;
    }

    public function get(string $key): object
    {
        if (!isset($this->contexts[$key])) {
            throw new RuntimeException("Context '{$key}' not found in ContextBus");
        }
        return $this->contexts[$key];
    }

    public function mutateModeValue(array $newModeValue): void{
        if (!$this->mutationEnabled) {
            throw new RuntimeException(
                "Mutation denied: 'ConfigContextDTO' can only be changed during the bootstrap phase"
            );
        }

        $configContext = $this->contexts[ConfigContextDTO::class] ?? null;

        if (!$configContext) {
            throw new RuntimeException("Cannot mutate modeValue — 'ConfigContextDTO' not found in ContextBus");
        }
        $configContext->modeValue = $newModeValue;
    }

    public function disableMutation(): void{
        $this->mutationEnabled = false;
    }
}
