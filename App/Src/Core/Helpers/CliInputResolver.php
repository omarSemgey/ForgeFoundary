<?php

namespace App\Src\Core\Helpers;

use Illuminate\Console\Command;
use App\Src\Core\DTOs\CliInputContextDTO;

// ===============================================
// Class: CliInputResolver
// Purpose: Resolves the inputs (arguments and options) of a CLI command
//          and publishes them to the ContextBus for other systems to access.
// Functions:
//   - resolve(Command $command): Parses CLI input and publishes a DTO to the ContextBus
// ===============================================
class CliInputResolver
{
    // ===============================================
    // Function: resolve
    // Inputs:
    //   - Command $command: The current CLI command being executed
    // Outputs: void
    // Purpose: Converts the raw CLI input into a structured DTO and publishes it
    // Logic Walkthrough:
    //   1. Creates a new CliInputContextDTO using the command's options and arguments
    //   2. Publishes the DTO to the global ContextBus so other systems can consume it
    // External Functions/Helpers Used:
    //   - Command->options() : Retrieves all CLI options
    //   - Command->arguments() : Retrieves all CLI arguments
    //   - ContextBus()->publish() : Publishes the DTO to the central context bus
    // Side Effects:
    //   - Publishes CLI input data globally via ContextBus
    // ===============================================
    public function resolve(Command $command): void
    {
        $cliInput = new CliInputContextDTO(
            $command->options(),
            $command->arguments()
        );

        ContextBus()->publish(CliInputContextDTO::class, $cliInput);
    }
}
