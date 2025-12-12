<?php

namespace App\Src\Domains\Components\Resolvers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use Illuminate\Filesystem\Filesystem;
use App\Src\Core\Helpers\PathManager;

// ===============================================
// Class: ComponentResolver
// Purpose: Resolves the component context for the current mode.
//          Determines the component's name and its absolute path
//          based on the current mode configuration and returns
//          a DTO representing this context.
// Functions:
//   - __construct(): injects dependencies
//   - resolveComponentContext(): returns a ComponentContextDTO
//   - resolveComponentName(): determines component name from config
//   - resolveComponentPath(): determines absolute path of component
// ===============================================
class ComponentResolver
{
    private string $componentName;  // Stores resolved component name
    private string $componentPath;  // Stores resolved absolute path of component

    // Mapping of config keys to internal property names
    private const COMPONENT_CONFIG_KEYS = [
        "name" => "component_name",
        "path" => "component_path",
    ];

    public function __construct(
        private Filesystem $files,
         private PathManager $pathManager
    ){}

    // ===============================================
    // Function: resolveComponentContext
    // Inputs: none
    // Outputs: ComponentContextDTO
    // Purpose: Resolves the component name and path and returns as a DTO
    // Logic Walkthrough:
    //   1. Calls resolveComponentName() to get component name from config
    //   2. Calls resolveComponentPath() to get component absolute path
    //   3. Instantiates and returns ComponentContextDTO with resolved name/path
    // External Functions/Helpers Used:
    //   - resolveComponentName()
    //   - resolveComponentPath()
    // Side Effects:
    //   - Logs resolved name/path using Debugger
    // ===============================================
    public function resolveComponentContext(): ComponentContextDTO
    {
        $this->resolveComponentName();
        $this->resolveComponentPath();
        
        return new ComponentContextDTO(
            $this->componentName,
            $this->componentPath,
        );
    }

    // ===============================================
    // Function: resolveComponentName
    // Inputs: none
    // Outputs: void (sets $this->componentName)
    // Purpose: Fetches component name from mode config
    // Logic Walkthrough:
    //   1. Reads "component_name" from mode_config using Config() helper
    //   2. Stores value in $this->componentName
    //   3. Logs resolved name using Debugger
    // External Functions/Helpers Used:
    //   - Config()->get()
    //   - Debugger()->info()
    // Side Effects: Writes info to Debugger log
    // ===============================================
    private function resolveComponentName(): void
    {
        $name = Config()->get("mode_config." . self::COMPONENT_CONFIG_KEYS['name']);
        $this->componentName = NamingConventions()->apply("component", $name, $name); 

        Debugger()->info("Component name: '{$this->componentName}'");
    }

    // ===============================================
    // Function: resolveComponentPath
    // Inputs: none
    // Outputs: void (sets $this->componentPath)
    // Purpose: Determines absolute path for the component
    // Logic Walkthrough:
    //   1. Reads base path for component from mode_config using Config() helper
    //   2. Concatenates base path with component name
    //   3. Resolves absolute path using PathManager->getAbsolutePath()
    //   4. Stores absolute path in $this->componentPath
    //   5. Logs resolved path using Debugger
    // External Functions/Helpers Used:
    //   - Config()->get()
    //   - PathManager->getAbsolutePath()
    //   - Debugger()->info()
    // Side Effects: Writes info to Debugger log
    // ===============================================
    private function resolveComponentPath(): void
    {
        $basePath = Config()->get("mode_config." . self::COMPONENT_CONFIG_KEYS['path']);
     
        $this->componentPath = $this->pathManager->getAbsolutePath($basePath . '/' . $this->componentName, false);

        Debugger()->info("Component path: '{$this->componentPath}'");
    }
}
