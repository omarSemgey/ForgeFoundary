<?php

namespace App\Src\Core\Helpers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

// ===============================================
// Class: TreeManager
// Purpose: Responsible for rendering a visual tree 
//          representation of a component's directory structure
// Functions:
//   - loadContexts(): Loads necessary contexts from ContextBus
//   - renderTree(Command $command): Entry point for printing tree of a component
//   - printTree(string $path, string $prefix): Recursive helper to print directories and files
// ===============================================
class TreeManager
{
    private ComponentContextDTO $componentContextDTO; // Holds current component context
    private Command $commandInstance; // Holds reference to current CLI command instance

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads the ComponentContextDTO from the global ContextBus
    // Logic Walkthrough:
    //   1. Retrieves ComponentContextDTO instance from ContextBus()
    //   2. Logs info using Debugger that the context has been loaded
    // External Functions/Helpers Used:
    //   - ContextBus()->get(ComponentContextDTO::class)
    //   - Debugger()->info()
    // Side Effects:
    //   - Sets $this->componentContextDTO
    //   - Writes a log message
    // ===============================================
    private function loadContexts(): void {
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    // ===============================================
    // Function: renderTree
    // Inputs:
    //   - Command $command: Current CLI command instance
    // Outputs: void
    // Purpose: Public entry point to display the component's directory tree in CLI
    // Logic Walkthrough:
    //   1. Stores the CLI command instance in $this->commandInstance
    //   2. Calls loadContexts() to get component context
    //   3. Prints the component's root name
    //   4. Calls private printTree() recursively to display full directory structure
    // External Functions/Helpers Used:
    //   - $this->loadContexts()
    //   - $this->printTree()
    // Side Effects:
    //   - Writes output to CLI using $command->line()
    // ===============================================
    public function renderTree(Command $command): void
    {
        $this->commandInstance = $command;

        $this->loadContexts();
        $this->commandInstance->line($this->componentContextDTO->componentName);
        $this->printTree($this->componentContextDTO->componentPath, '');
    }

    // ===============================================
    // Function: printTree
    // Inputs:
    //   - string $path: Directory path to render
    //   - string $prefix: String prefix used for indentation (default: '   ')
    // Outputs: void
    // Purpose: Recursively prints directories and files in a tree-like CLI structure
    // Logic Walkthrough:
    //   1. Gets all directories and files in $path using File::directories and File::files
    //   2. Loops through all items, determining connector symbols ('├─ ' or '└─ ')
    //   3. Prints each item with the correct prefix and connector
    //   4. If item is a directory, calls printTree recursively with updated prefix
    // External Functions/Helpers Used:
    //   - File::directories($path)
    //   - File::files($path)
    //   - basename()
    // Side Effects:
    //   - Writes output to CLI using $this->commandInstance->line()
    // ===============================================
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
