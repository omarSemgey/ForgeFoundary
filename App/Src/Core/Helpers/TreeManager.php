<?php

namespace App\Src\Core\Helpers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class TreeManager
{
    private ComponentContextDTO $componentContextDTO;
    private Command $commandInstance;
    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    public function renderTree(Command $command): void
    {
        $this->commandInstance = $command;

        $this->loadContexts();
        $this->commandInstance->line($this->componentContextDTO->componentName);
        $this->printTree($this->componentContextDTO->componentPath, '');
    }

    private function printTree(string $path, string $prefix = '   '): void
    {
        $items = array_merge(
            File::directories($path),
            File::files($path)
        );

        $count = count($items);
        $i = 0;

        foreach ($items as $item) {
            $i++;
            $connector = $i === $count ? '└─ ' : '├─ ';
            $name = is_dir($item) ? ' ' . basename($item) : basename($item);

            $this->commandInstance->line($prefix . $connector . $name);

            // Recurse into directories
            if (is_dir($item)) {
                $this->printTree($item, $prefix . ($i === $count ? '   ' : '│  '));
            }
        }
    }
}