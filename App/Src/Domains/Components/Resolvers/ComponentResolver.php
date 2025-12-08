<?php

namespace App\Src\Domains\Components\Resolvers;
use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use Illuminate\Filesystem\Filesystem;
use App\Src\Core\Helpers\PathManager;

class ComponentResolver
{
    private string $componentName;
    
    private string $componentPath;

    private const COMPONENT_CONFIG_KEYS = [
        "name" => "component_name",
        "path" => "component_path",
    ];
    
    public function __construct(private Filesystem $files, private PathManager $pathManager){}
    
    public function resolveComponentContext(): ComponentContextDTO
    {
        $this->resolveComponentName();
        $this->resolveComponentPath();
        
        return new ComponentContextDTO(
            $this->componentName,
            $this->componentPath,
        );
    }

    private function resolveComponentName(): void
    {
        $this->componentName = Config()->get("mode_config." . self::COMPONENT_CONFIG_KEYS['name']);

        Debugger()->info("Component name: '{$this->componentName}'");
    }

    private function resolveComponentPath(): void
    {
        $basePath = Config()->get("mode_config." . self::COMPONENT_CONFIG_KEYS['path']);
     
        $this->componentPath = $this->pathManager->getAbsolutePath($basePath . '/' . $this->componentName, false);

        Debugger()->info("Component path: '{$this->componentPath}'");
    }
}
