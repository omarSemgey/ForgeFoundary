<?php

namespace App\Src\Domains\Directories\Resolvers;

use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;

// ===============================================
// Class: DirectoryResolver
// Purpose: Resolves and provides the directories configuration 
//          for the current mode/component in ForgeFoundary.
// Functions:
//   - resolveDirectoriesContext(): Resolves and returns directories as a DTO
//   - resolvedirectories(): Fetches directories from configuration and logs them
// ===============================================
class DirectoryResolver
{
    // Holds the list of resolved directories
    private array $directories;

    // Configuration keys used for fetching directories
    private const DIRECTORY_CONFIG_KEYS = [
        "directories" => "directories",
    ];

    // ===============================================
    // Function: resolveDirectoriesContext
    // Inputs: none
    // Outputs: DirectoryContextDTO instance containing resolved directories
    // Purpose: Public method to fetch and return directories context
    // Logic Walkthrough:
    //   1. Calls the private resolvedirectories() to fetch directories from config
    //   2. Wraps the result in a DirectoryContextDTO and returns it
    // External Functions/Helpers Used:
    //   - Config()->get() to fetch configuration values
    //   - Debugger()->info() for logging
    // Side Effects:
    //   - Populates the $directories property
    //   - Logs directories info
    // ===============================================
    public function resolveDirectoriesContext(): DirectoryContextDTO
    {
        $this->resolvedirectories();
        return new DirectoryContextDTO($this->directories);
    }
    
    // ===============================================
    // Function: resolvedirectories
    // Inputs: none
    // Outputs: void
    // Purpose: Fetches directories from the current mode's configuration
    // Logic Walkthrough:
    //   1. Retrieves directories array from Config singleton using the key "directories"
    //   2. Constructs a log message:
    //        - If directories exist: lists them
    //        - If none: logs 'No directories were provided'
    //   3. Logs the message using Debugger
    // External Functions/Helpers Used:
    //   - Config()->get()
    //   - Debugger()->info()
    // Side Effects:
    //   - Modifies $this->directories property
    //   - Produces a log output
    // ===============================================
    private function resolvedirectories(): void
    {
        $this->directories = Config()->get("mode_config." . self::DIRECTORY_CONFIG_KEYS['directories']);
    
        $logDirectories = count($this->directories) ?  "Provided directories: '[" . implode(', ', $this->directories) . "]'" : 'No directories were provided';
        Debugger()->info($logDirectories);
    }
}
