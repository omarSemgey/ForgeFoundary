<?php

namespace App\Src\Domains\CliFlags\Resolvers;

use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Core\DTOs\CliInputContextDTO;

// ===============================================
// Class: CliFlagsResolver
// Purpose: Resolves CLI flags for the current command.
//          Combines flags defined in configuration with those provided by the user at runtime.
//          Produces a unified context DTO for the CLI flags system.
// Functions:
//   - resolveCliFlagsContext(): main entry point, returns resolved context
//   - resolveDefinedCliFlags(): fetches defined flags from mode configuration
//   - resolveProvidedCliFlags(): fetches and normalizes flags passed via CLI
//   - loadContexts(): loads necessary context objects from ContextBus
// ===============================================
# TODO: change the cli flags system to make it cleaner and make it work without the custom=
# TODO: implement debugging messages that are clear and work with the associative arrays values
class CliFlagsResolver
{
    // Array of flags defined in the mode config
    private array $definedCliFlags;

    // Array of flags provided by the user at runtime
    private array $providedCliFlags;

    // Holds the CLI input context (options/arguments)
    private CliInputContextDTO $cliInputContextDTO; 

    // Key mapping for config access
    private const CLI_FLAGS_CONFIG_KEYS = [
        "cli_flags" => "cli_flags",
    ];

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: none
    // Purpose: Loads CLI input context from the ContextBus singleton
    // Logic:
    //   - Retrieves CliInputContextDTO from ContextBus
    //   - Logs info about the context load
    // External Functions/Helpers:
    //   - ContextBus() helper to get context
    //   - Debugger()->info() for logging
    // Side Effects:
    //   - Initializes $this->cliInputContextDTO
    // ===============================================
    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    // ===============================================
    // Function: resolveCliFlagsContext
    // Inputs: none
    // Outputs: CliFlagsContextDTO
    // Purpose: Main entry point to produce a resolved CLI flags context
    // Logic:
    //   1. Load required contexts
    //   2. Resolve defined flags from configuration
    //   3. Resolve provided flags from CLI input
    //   4. Return unified DTO
    // External Functions/Helpers:
    //   - loadContexts()
    //   - resolveDefinedCliFlags()
    //   - resolveProvidedCliFlags()
    // Side Effects:
    //   - Sets $this->definedCliFlags and $this->providedCliFlags
    // ===============================================
    public function resolveCliFlagsContext(): CliFlagsContextDTO{
        $this->loadContexts();
        $this->resolveDefinedCliFlags();
        $this->resolveProvidedCliFlags();
    
        return new CliFlagsContextDTO(
            $this->definedCliFlags,
            $this->providedCliFlags,
        );
    }

    // ===============================================
    // Function: resolveDefinedCliFlags
    // Inputs: none
    // Outputs: void
    // Purpose: Fetches CLI flags defined in the current mode configuration
    // Logic:
    //   - Reads from Config()->get() using CLI_FLAGS_CONFIG_KEYS
    //   - Logs defined flags if any, otherwise logs that none were defined
    // External Functions/Helpers:
    //   - Config()->get() to read configuration
    //   - Debugger()->info() for logging
    // Side Effects:
    //   - Sets $this->definedCliFlags
    // ===============================================
    private function resolveDefinedCliFlags(): void{
        // NOTE: this gives you the value of the variable that the cli will change, not the actual CLI input
        $this->definedCliFlags = Config()->get('mode_config.' . self::CLI_FLAGS_CONFIG_KEYS["cli_flags"], []);
        $logCliFlags = count($this->definedCliFlags) ?  
            "Defined cli flags: '[" . implode(', ', $this->definedCliFlags) . "]'" 
            : 'No cli flags were defined';
        Debugger()->info($logCliFlags);
    }
    
    // ===============================================
    // Function: resolveProvidedCliFlags
    // Inputs: none
    // Outputs: void
    // Purpose: Fetches CLI flags passed by the user and normalizes them
    // Logic:
    //   - Retrieves 'custom' option from $cliInputContextDTO
    //   - Removes empty strings
    //   - Splits comma-separated values
    //   - Removes duplicates and resets array keys
    //   - Logs provided flags
    // External Functions/Helpers:
    //   - collect() for collection operations
    //   - Debugger()->info() for logging
    // Side Effects:
    //   - Sets $this->providedCliFlags
    // ===============================================
    private function resolveProvidedCliFlags(): void{
        $flags = collect($this->cliInputContextDTO->getOption('custom') ?? []) // raw array from CLI
        ->filter() // remove empty strings
        ->flatMap(callback: fn($item) => explode(',', $item)) // split comma-separated
        ->unique() // remove duplicates
        ->values() // reset keys
        ->all(); // convert back to plain array
        $this->providedCliFlags = $flags;
        $logCliFlags = count($this->providedCliFlags) ?  
            "Provided cli flags: '[" . implode(', ', $this->providedCliFlags) . "]'" 
            : 'No cli flags were provided';
        Debugger()->info($logCliFlags);
    }
}