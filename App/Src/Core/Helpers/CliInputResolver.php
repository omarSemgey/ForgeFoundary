<?php

namespace App\Src\Core\Helpers;

use Illuminate\Console\Command;
use App\Src\Core\DTOs\CliInputContextDTO;

class CliInputResolver
{
    public function resolve(Command $command): void
    {
        $cliInput = new CliInputContextDTO(
            $command->options(),
            $command->arguments()
        );

        ContextBus()->publish(CliInputContextDTO::class, $cliInput);
        // Debugger()->info("Published Context: 'CliInputContextDTO' published to the global context bus");
    }
}