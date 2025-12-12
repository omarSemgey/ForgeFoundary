<?php

namespace App\Src\Core\NamingConventions\Executers;

use App\Src\Core\Debuggers\Debugger;
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
    //   - string $section: the naming convention section (e.g., "directories", "templates")
    //   - string $key: 
    //   - string $value: the string value to apply naming conventions to
    // Outputs: string - the value transformed according to the naming conventions
    // Purpose: Applies the configured naming conventions (defaults and overrides) to a given value
    //          Handles values containing '/' by applying conventions to each segment individually
    // Logic Walkthrough:
    //   1. Loads naming conventions contexts via loadContexts()
    //   2. Retrieves the defaults and overrides for the specified section
    //   3. Checks if $value contains '/':
    //        a. Splits $value into segments by '/'
    //        b. Loops over each segment and applies naming conventions individually
    //        c. Joins the segments back together with '/'
    //   4. If no '/', applies naming conventions directly on $value
    // Uses: loadContexts(), applyNamingConvention()
    // ===============================================
    public function apply(string $section,string $key, string $value): string
    {
        $this->loadContexts();
        $currentNamingConventionsSection = $this->namingConventionsContextDTO->NamingConventionsSections[$section];
        $defaults = $currentNamingConventionsSection->defaults;
        $overrides = $currentNamingConventionsSection->overrides;
        
        if (str_contains($value, '/')) {
            $segments = explode('/', $value);

            foreach ($segments as $i => $segment) {
                $segments[$i] = $this->applyNamingConvention($key, $segment, $defaults, $overrides);
            }

            return implode('/', $segments);
        }

        
        return $this->applyNamingConvention($key, $value, $defaults, $overrides);
        
    }

    // ===============================================
    // Function: applyNamingConvention
    // Inputs:
    //   - string $key: 
    //   - string $value: the string to transform
    //   - array $defaults: default naming conventions to apply
    //   - array $overrides: specific overrides for particular values
    // Outputs: string - the value transformed by the specified naming conventions
    // Purpose: Determines which naming conventions to apply and applies them in order
    // Logic Walkthrough:
    //   1. Checks if an override exists for $key; if not, use defaults
    //   2. Loops through each style in the selected conventions array
    //   3. Calls the StyleManager to apply the style
    //   4. Returns the fully transformed value
    // Side Effects: Uses StyleManager to apply transformations
    // Uses: StyleManager->applyStyle()
    // ===============================================
    private function applyNamingConvention(string $key, string $value, array $defaults, array $overrides): string 
    {
        $namingConventions = $overrides[$key] ?? $defaults;
      
        foreach($namingConventions as $style){
            $value = $this->styleManager->applyStyle($style, $value);
        }

        return $value;
    }
}
