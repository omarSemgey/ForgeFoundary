<?php

namespace App\Src\Domains\Directories\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use Illuminate\Filesystem\Filesystem;

// ===============================================
// Class: DirectoriesGenerator
// Purpose: Generates directories for a given component 
//          based on the DirectoryContextDTO and ComponentContextDTO.
//          Applies naming conventions before creating directories.
// Functions:
//   - __construct(): Injects Filesystem dependency
//   - loadContexts(): Loads required DTOs from the ContextBus
//   - generateDirectories(): Main entry to generate all directories
//   - createDirectory(): Creates a single directory on disk if it doesn't exist
// ===============================================
class DirectoriesGenerator
{
    // Holds directory-related context for the current component
    private DirectoryContextDTO $directoryContextDTO;

    // Holds component-related context
    private ComponentContextDTO $componentContextDTO;

    public function __construct(
        private Filesystem $files,
    ){}

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads ComponentContextDTO and DirectoryContextDTO from the global ContextBus
    // Logic Walkthrough:
    //   1. Retrieves ComponentContextDTO from ContextBus
    //   2. Logs info that ComponentContextDTO has been loaded
    //   3. Retrieves DirectoryContextDTO from ContextBus
    //   4. Logs info that DirectoryContextDTO has been loaded
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Populates class properties $componentContextDTO and $directoryContextDTO
    // ===============================================
    private function loadContexts(): void{
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
        $this->directoryContextDTO = ContextBus()->get(DirectoryContextDTO::class);
        Debugger()->info("Loaded context: 'DirectoryContextDTO' from the context bus");
    }

    // ===============================================
    // Function: generateDirectories
    // Inputs: none
    // Outputs: void
    // Purpose: Generates all directories defined in DirectoryContextDTO for the current component
    // Logic Walkthrough:
    //   1. Calls loadContexts() to ensure required DTOs are available
    //   2. Collects all directories from $directoryContextDTO
    //   3. Iterates over each directory:
    //       a. Applies naming conventions
    //       b. Calls createDirectory() to create the directory on disk
    // External Functions/Helpers Used:
    //   - loadContexts()
    //   - NamingConventions()->apply()
    //   - createDirectory()
    // Side Effects:
    //   - Creates directories on disk if they do not exist
    // ===============================================
    public function generateDirectories(): void
    {
        $this->loadContexts();
        $dirs = collect($this->directoryContextDTO->directories);

        $dirs->each(function ($dir) {
            $dir = NamingConventions()->apply("directories", $dir);
            $this->createDirectory("{$this->componentContextDTO->componentPath}/{$dir}", $dir);
        });
    }

    // ===============================================
    // Function: createDirectory
    // Inputs:
    //   - string $path: The full path where the directory should be created
    //   - string $name: The name of the directory (used for logging)
    // Outputs: void
    // Purpose: Creates a single directory on disk if it does not already exist
    // Logic Walkthrough:
    //   1. Checks if the directory at $path already exists
    //       - If yes, returns immediately
    //   2. Logs info that the directory is being created
    //   3. Uses Filesystem->makeDirectory() to create the directory with 0755 permissions
    // External Functions/Helpers Used:
    //   - $this->files->exists()
    //   - Debugger()->info()
    //   - $this->files->makeDirectory()
    // Side Effects:
    //   - May create directories on disk
    // ===============================================
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
