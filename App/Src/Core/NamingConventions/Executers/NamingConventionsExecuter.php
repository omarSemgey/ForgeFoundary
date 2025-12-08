<?php

namespace App\Src\Core\NamingConventions\Executers;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\Helpers\StyleManager;

// ===============================================
// Class: NamingConventionsExecuter
// Purpose: Singleton class responsible for applying naming conventions to strings
//          according to configured rules in the NamingConventionsContextDTO.
// Functions:
//   - getInstance(): returns the singleton instance
//   - apply(string $system, string $value): applies the appropriate naming convention to a value
//   - loadContexts(): loads naming convention context from ContextBus
// ===============================================
class NamingConventionsExecuter
{
    // Singleton instance
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

    // Loaded context DTO
    private NamingConventionsContextDTO $namingConventionsContextDTO;

    // Flag to ensure contexts are loaded only once
    private bool $contextsLoaded = false;

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads NamingConventionsContextDTO from ContextBus
    // Logic Walkthrough:
    //   - If contexts are already loaded, return immediately
    //   - Fetch the NamingConventionsContextDTO from ContextBus singleton
    //   - Log info using Debugger
    //   - Set $contextsLoaded to true
    // External functions used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Modifies $namingConventionsContextDTO and $contextsLoaded
    // ===============================================
    private function loadContexts(): void
    {
        if ($this->contextsLoaded) return;
        $this->namingConventionsContextDTO = ContextBus()->get(NamingConventionsContextDTO::class);
        Debugger()->info("Loaded context: 'NamingConventionsContextDTO' from the context bus");
        $this->contextsLoaded = true;
    }

    // ===============================================
    // Function: apply
    // Inputs:
    //   - string $system: the system/component name to identify rules
    //   - string $value: the string to apply naming conventions to
    // Outputs: string (the styled value according to the convention)
    // Purpose: Applies the correct naming convention to the input value
    // Logic Walkthrough:
    //   - Ensures context is loaded
    //   - Creates an exact key and wildcard key to lookup in DTO
    //   - Retrieves the rule using exact key first, then wildcard
    //   - If no rule exists or rule is disabled, returns original value
    //   - Otherwise, applies the rule's style using StyleManager
    // External functions/helpers used:
    //   - loadContexts()
    //   - NamingConventionsContextDTO->getRule()
    //   - StyleManager->applyStyle()
    // Side Effects: None
    // ===============================================
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
