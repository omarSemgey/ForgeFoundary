<?php

namespace App\Src\Domains\CliFlags\Resolvers;

use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;

// ===============================================
// Class: CliFlagsResolver
// Purpose: Resolves and normalizes CLI flags for commands. 
//          Combines flags defined in the mode configuration YAML 
//          with the flags provided by the user at runtime, producing 
//          a unified DTO for the CLI flags system.
//
// Functions:
//   - resolveCliFlagsContext(): Main entry point, returns a resolved CLI flags DTO
//   - resolveMutatableConfigKeys(): Links CLI flags to the config keys they can mutate
//   - resolveProvidedCliFlags(): Normalizes and processes CLI flags input by the user
//   - removeCliFlagsBlock(): Removes the cli_flags section from YAML to avoid duplicate matches
//   - resolveCliFlagsMutators(): Extracts mutator aliases defined under cli_flags
//
// Notes/TODOs:
//   - Consider simplifying CLI flags system to remove the need for 'custom=' in CLI
//   - Improve debug logging to better handle associative arrays
// ===============================================
class CliFlagsResolver
{
    // Holds the flags provided by the user through the CLI
    private array $providedCliFlags;

    // Maps CLI flags to the configuration keys they affect
    private array $mutatableConfigKeys;

    // DTO holding CLI input context (user-provided options/arguments)
    private CliInputContextDTO $cliInputContextDTO;

    // DTO holding mode configuration context (path to YAML, etc.)
    private ConfigContextDTO $configContextDTO;

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: none
    // Purpose: Loads CLI input context from the ContextBus singleton
    // Logic:
    //   - Retrieves CliInputContextDTO from ContextBus
    //   - Retrieves ConfigContextDTO from ContextBus
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
        
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
    }
 
 
    // ===============================================
    // Function: resolveCliFlagsContext
    // Inputs: none
    // Outputs: CliFlagsContextDTO
    // Purpose: Main entry point for resolving CLI flags into a unified context DTO
    // Logic:
    //   1. Resolve the CLI flags provided by the user
    //   2. Resolve mutatable config keys from YAML
    //   3. Combine them into a CliFlagsContextDTO
    // Uses:
    //   - resolveProvidedCliFlags()
    //   - resolveMutatableConfigKeys()
    //   - loadContexts()
    // Side Effects:
    //   - Populates $this->providedCliFlags
    //   - Populates $this->mutatableConfigKeys
    // ===============================================
    public function resolveCliFlagsContext(): CliFlagsContextDTO
    {
        $this->loadContexts();
        $this->resolveProvidedCliFlags();
        $this->resolveMutatableConfigKeys();

        return new CliFlagsContextDTO(
            $this->providedCliFlags,
            $this->mutatableConfigKeys
        );
    }

    // ===============================================
    // Function: resolveProvidedCliFlags
    // Inputs: none
    // Outputs: void
    // Purpose: Fetches CLI flags passed by the user and normalizes them
    // Logic:
    //   - Retrieves 'custom' option from CLI input
    //   - Removes empty entries
    //   - Splits comma-separated flags
    //   - Removes duplicates and resets array keys
    //   - Logs normalized flags
    // Uses:
    //   - collect() for collection operations
    //   - Debugger()->info() for logging
    // Side Effects:
    //   - Sets $this->providedCliFlags
    // ===============================================
    private function resolveProvidedCliFlags(): void
    {
        $flags = collect($this->cliInputContextDTO->getOption('custom') ?? [])
            ->filter() // remove empty strings
            ->flatMap(fn($item) => explode(',', $item)) // split comma-separated values
            ->unique() // remove duplicates
            ->values() // reset numeric keys
            ->all(); // convert back to plain array

        $this->providedCliFlags = $flags;

        $logCliFlags = count($this->providedCliFlags)
            ? "Provided CLI flags: '[" . implode(', ', $this->providedCliFlags) . "]'"
            : 'No CLI flags were provided';

        Debugger()->info($logCliFlags);
    }

    // ===============================================
    // Function: resolveMutatableConfigKeys
    // Inputs: none
    // Outputs: void
    // Purpose: Links CLI flags to the configuration keys that use the corresponding mutator
    // Logic:
    //   1. Extract all CLI flags and their mutators
    //   2. Load YAML and remove cli_flags block to avoid duplicates
    //   3. For each CLI flag, find all config keys referencing its mutator
    //   4. Populate $this->mutatableConfigKeys
    // Uses:
    //   - resolveCliFlagsMutators()
    //   - removeCliFlagsBlock()
    //   - preg_match_all() for pattern matching
    // Side Effects:
    //   - Populates $this->mutatableConfigKeys
    // ===============================================
    private function resolveMutatableConfigKeys(): void
    {
        $cliFlags = $this->resolveCliFlagsMutators();
        $yaml = file_get_contents($this->configContextDTO->modeAbsolutePath);

        $yaml = $this->removeCliFlagsBlock($yaml);

        $linked = [];
        foreach ($cliFlags as $flag => $mutator) {
            $pattern = '/^\s*([a-zA-Z0-9_]+):\s*\*' . preg_quote($mutator, '/') . '\b/m';
            preg_match_all($pattern, $yaml, $matches);

            $linked[$flag] = $matches[1] ?? [];
        }

        $this->mutatableConfigKeys = $linked;

        Debugger()->info("Linked CLI flags to config keys using them: " . json_encode($linked));
    }

    // ===============================================
    // Function: removeCliFlagsBlock
    // Inputs: string $yaml
    // Outputs: string $yaml without the cli_flags section
    // Purpose: Removes the cli_flags block entirely to avoid duplicate matches when linking flags
    // Logic:
    //   1. Find 'cli_flags:' in YAML
    //   2. Slice from that position
    //   3. Detect next top-level key and slice only up to it
    //   4. Remove the sliced block from original YAML
    // ===============================================
    private function removeCliFlagsBlock(string $yaml): string
    {
        $start = strpos($yaml, "cli_flags:");
        if ($start === false) {
            return $yaml;
        }

        $slice = substr($yaml, $start);

        if (preg_match('/\n[A-Za-z_]+:/', $slice, $stop, PREG_OFFSET_CAPTURE)) {
            $slice = substr($slice, 0, $stop[0][1]);
        }

        return str_replace($slice, '', $yaml);
    }

    // ===============================================
    // Function: resolveCliFlagsMutators
    // Inputs: none
    // Outputs: array<string, string>|null
    // Purpose: Extracts mutators defined under cli_flags section in YAML
    // Logic:
    //   1. Find the cli_flags section in YAML
    //   2. Slice until the next top-level key
    //   3. Use regex to match all 'flag: *mutator' entries
    //   4. Return as associative array: CLI flag => mutator
    // Uses:
    //   - preg_match_all() for pattern matching
    // Side Effects:
    //   - Logs a warning if cli_flags section not found
    // ===============================================
    private function resolveCliFlagsMutators(): array|null
    {
        $yaml = file_get_contents($this->configContextDTO->modeAbsolutePath);

        $start = strpos($yaml, "cli_flags:");
        if ($start === false) {
            Debugger()->warning("cli_flags not found in YAML");
            return null;
        }

        $slice = substr($yaml, $start);

        if (preg_match('/\n[A-Za-z_]+:/', $slice, $stop, PREG_OFFSET_CAPTURE)) {
            $slice = substr($slice, 0, $stop[0][1]);
        }

        preg_match_all('/^\s{2}([a-zA-Z0-9_]+):\s*\*([a-zA-Z0-9_]+)/m', $slice, $matches);

        return array_combine($matches[1], $matches[2]);
    }
}