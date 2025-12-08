<?php

namespace App\Console\Commands\Traits\Validates;

// ===============================================
// Trait: ComponentValidator
// Purpose: Provides validation logic for components (domains) before scaffolding.
//          Ensures that a component with the same name does not already exist.
// Functions:
//   - domainExists(): Checks if the target component directory exists and throws an exception if it does.
// ===============================================
trait ComponentValidator
{
    // ===============================================
    // Function: domainExists
    // Inputs: none (relies on $this->componentPath and $this->componentName from the using class)
    // Outputs: void
    // Purpose: Prevents overwriting an existing domain/component by validating its existence
    // Logic Walkthrough:
    //   1. Uses the injected `$files` filesystem helper to check if the `$componentPath` exists
    //   2. If the path exists, throws a RuntimeException with a descriptive message
    // External Functions/Helpers Used:
    //   - $this->files->exists(): checks if a filesystem path exists
    // Side Effects:
    //   - Throws RuntimeException if the domain/component already exists
    // ===============================================
    protected function domainExists(): void
    {
        if ($this->files->exists($this->componentPath)) {
            throw new \RuntimeException("Domain '{$this->componentName}' already exists");
        }
    }
}
