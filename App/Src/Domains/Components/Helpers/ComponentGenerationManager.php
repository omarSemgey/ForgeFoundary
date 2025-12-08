<?php

namespace App\Src\Domains\Components\Helpers;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

// ===============================================
// Class: ComponentGenerationManager
// Purpose: Manages the generation of components by providing helper methods 
//          for verifying paths and ensuring components do not conflict with existing files.
// Functions:
//   - __construct(Filesystem $files): Injects the filesystem dependency
//   - componentExists(string $path): Checks if a component path already exists and throws an error if it does
// ===============================================
class ComponentGenerationManager
{
    public function __construct(private Filesystem $files){}

    // ===============================================
    // Function: componentExists
    // Inputs:
    //   - string $path: Path to check for an existing component
    // Outputs: void
    // Purpose: Ensures a component does not already exist at the given path
    // Logic Walkthrough:
    //   1. Uses the Filesystem instance to check if $path exists
    //   2. If it exists, throws a RuntimeException with a descriptive message
    // External Functions/Helpers Used:
    //   - $this->files->exists()
    // Side Effects:
    //   - Throws RuntimeException if the component path already exists
    // ===============================================
    public function componentExists(string $path): void{
        if ($this->files->exists($path)) {
            throw new RuntimeException("Component: '{$path}' already exists");
        }
    }
}
