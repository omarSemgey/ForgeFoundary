<?php

namespace App\Src\Core\DTOs;

// ===============================================
// Class: CliInputContextDTO
// Purpose: Data Transfer Object (DTO) that holds parsed CLI input
//          including options and arguments for a command execution.
// Functions:
//   - __construct(): Initializes the DTO with options and arguments arrays
//   - getOption(string $key): Retrieves a CLI option by key
//   - getArgument(string $key): Retrieves a CLI argument by key
// ===============================================
class CliInputContextDTO
{
    public function __construct(
        public readonly array $options = [],
        public readonly array $arguments = []
    ) {}

    // ===============================================
    // Function: getOption
    // Inputs:
    //   - string $key: the name of the CLI option to retrieve
    // Outputs: mixed (value of the option or null if it does not exist)
    // Purpose: Retrieve a single option from the CLI input context
    // Logic Walkthrough:
    //   1. Checks if $key exists in $options array
    //   2. Returns the corresponding value or null if key is missing
    // External Functions/Helpers Used: None
    // Side Effects: None
    // ===============================================
    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }
}
