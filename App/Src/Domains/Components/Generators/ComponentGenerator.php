<?php

namespace App\Src\Domains\Components\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Components\Helpers\ComponentGenerationManager;
use Illuminate\Filesystem\Filesystem;


class ComponentGenerator
{
    private ComponentContextDTO $componentContextDTO;

    public function __construct(private Filesystem $files, private ComponentGenerationManager $componentGenerationManager){}

    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

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
