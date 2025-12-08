<?php

namespace App\Src\Domains\Components\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Components\Helpers\ComponentGenerationManager;
use Illuminate\Filesystem\Filesystem;

// ===============================================
// Class: ComponentGenerator
// Purpose: Handles generation of a new "component" in the ForgeFoundary tool.
//          Responsible for verifying existence, creating the folder structure,
//          and adding initial placeholder files.
// Functions:
//   - __construct(): Injects Filesystem and ComponentGenerationManager
//   - loadContexts(): Loads ComponentContextDTO from the global context bus
//   - generateComponent(): Main method to generate component directories and initial files
// ===============================================
class ComponentGenerator
{
    // Stores context for the component being generated
    private ComponentContextDTO $componentContextDTO;

    public function __construct(
        private Filesystem $files, 
        private ComponentGenerationManager $componentGenerationManager
    ){}

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads the component context DTO from the global ContextBus
    // Logic Walkthrough:
    //   1. Retrieves ComponentContextDTO from ContextBus()
    //   2. Assigns it to the private property $componentContextDTO
    //   3. Logs the loading via Debugger()->info()
    // External Functions/Helpers Used:
    //   - ContextBus(): global context bus accessor
    //   - Debugger()->info(): logs informational message
    // Side Effects:
    //   - Loads context into $componentContextDTO
    // ===============================================
    private function loadContexts(): void
    {
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    // ===============================================
    // Function: generateComponent
    // Inputs: none
    // Outputs: void
    // Purpose: Generates a new component directory with an initial placeholder
    // Logic Walkthrough:
    //   1. Calls $this->loadContexts() to populate $componentContextDTO
    //   2. Retrieves component path from $componentContextDTO
    //   3. Uses ComponentGenerationManager->componentExists() to check existence
    //   4. Uses Filesystem->makeDirectory() to create the component directory recursively
    //   5. Adds a .gitkeep placeholder file in the new directory
    //   6. Logs the successful generation via Debugger()->info()
    // External Functions/Helpers Used:
    //   - ComponentGenerationManager->componentExists()
    //   - Filesystem->makeDirectory()
    //   - Filesystem->put()
    //   - Debugger()->info()
    // Side Effects:
    //   - Creates directories and a .gitkeep file
    //   - Logs component generation
    // ===============================================
    public function generateComponent(): void
    {
        $this->loadContexts();
        $componentPath = $this->componentContextDTO->componentPath;
        $this->componentGenerationManager->componentExists($componentPath);
        
        $this->files->makeDirectory($componentPath, 0755, true);
        $this->files->put("{$componentPath}/.gitkeep", '');

        Debugger()->info("Generated component: '{$componentPath}'");
    }
}
