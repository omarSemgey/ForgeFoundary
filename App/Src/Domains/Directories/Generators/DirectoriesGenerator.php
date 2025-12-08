<?php

namespace App\Src\Domains\Directories\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use Illuminate\Filesystem\Filesystem;
use App\Src\Core\Helpers\ReportManager;

class DirectoriesGenerator
{
    private DirectoryContextDTO $directoryContextDTO;
    private ComponentContextDTo $componentContextDTO;

    public function __construct(
        private Filesystem $files,
    ){}

    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
        $this->directoryContextDTO = ContextBus()->get(DirectoryContextDTO::class);
        Debugger()->info("Loaded context: 'DirectoryContextDTO' from the context bus");
    }

    public function generateDirectories(): void
    {
        $this->loadContexts();
        $dirs = collect($this->directoryContextDTO->directories);

        $dirs->each(function ($dir) {
            $dir = NamingConventions()->apply("directories", $dir);
            $this->createDirectory("{$this->componentContextDTO->componentPath}/{$dir}", $dir);
        });
    }

    protected function createDirectory(string $path, string $name): void
    {
        if ($this->files->exists($path)) {
            return;
        }

        Debugger()->info("Creating Directory: '{$path}'");
        // Reporter()->logCreated('Directories', $name);
        $this->files->makeDirectory($path, 0755, true);
    }
}
