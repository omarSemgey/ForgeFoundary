<?php

namespace App\Src\Domains\CliFlags\Resolvers;

use App\Src\Domains\CliFlags\DTOs\CliFlagsContextDTO;
use App\Src\Core\DTOs\CliInputContextDTO;

# TODO: change the cli flags system to make it cleaner and make it work without the custom=
# TODO: implement debugging messages that are clear and work with the associative arrays values
class CliFlagsResolver
{
    private array $definedCliFlags;
    private array $providedCliFlags;
    private CliInputContextDTO $cliInputContextDTO; 
    private const CLI_FLAGS_CONFIG_KEYS = [
        "cli_flags" => "cli_flags",
    ];

    private function loadContexts(){
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    public function resolveCliFlagsContext(): CliFlagsContextDTO{
        $this->loadContexts();
        $this->resolveDefinedCliFlags();
        $this->resolveProvidedCliFlags();
    
        return new CliFlagsContextDTO(
            $this->definedCliFlags,
            $this->providedCliFlags,
        );
    }

    private function resolveDefinedCliFlags(): void{
        // NOTE: this gives you the value of the variable that the cli will change not the acctual cli
        $this->definedCliFlags = Config()->get('mode_config.' . self::CLI_FLAGS_CONFIG_KEYS["cli_flags"], []);
        $logCliFlags = count($this->definedCliFlags) ?  "Defined cli flags: '[" . implode(', ', $this->definedCliFlags) . "]'" : 'No cli flags were defined';
        Debugger()->info($logCliFlags);
    }
    
    private function resolveProvidedCliFlags(): void{
        $flags = collect($this->cliInputContextDTO->getOption('custom') ?? []) // raw array from CLI
        ->filter() // remove empty strings
        ->flatMap(callback: fn($item) => explode(',', $item)) // split comma-separated
        ->unique() // remove duplicates
        ->values() // reset keys
        ->all(); // convert back to plain array
        $this->providedCliFlags = $flags;
        $logCliFlags = count($this->providedCliFlags) ?  "Provided cli flags: '[" . implode(', ', $this->providedCliFlags) . "]'" : 'No cli flags were provided';
        Debugger()->info($logCliFlags);
    }
}
