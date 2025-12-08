<?php

namespace App\Src\Core\NamingConventions\Executers;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\Helpers\StyleManager;

class NamingConventionsExecuter{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(
                new StyleManager()
            );
        }

        return self::$instance;
    }

    private function __construct(
        private StyleManager $styleManager,
    ) {}

    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }

    private NamingConventionsContextDTO $namingConventionsContextDTO;
    private bool $contextsLoaded = false;

    private function loadContexts(): void{
        if ($this->contextsLoaded) return;
        $this->namingConventionsContextDTO = ContextBus()->get(NamingConventionsContextDTO::class);
        Debugger()->info("Loaded context: 'NamingConventionsContextDTO' from the context bus");
        $this->contextsLoaded = true;
    }

    public function apply(string $system, string $value): string
    {
        $this->loadContexts();
        $exactKey = "{$system}:{$value}";
        $wildKey  = "{$system}:*";
        
        $rule = $this->namingConventionsContextDTO->getRule($exactKey)
        ?? $this->namingConventionsContextDTO->getRule($wildKey);
        
        if (!$rule || !$rule->enabled) {
            return $value;
        }
        
        return $this->styleManager->applyStyle($rule->style, $value);
    }
}