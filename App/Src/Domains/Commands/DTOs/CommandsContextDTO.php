<?php

namespace App\Src\Domains\Commands\DTOs;

// ===============================================
// Class: CommandsContextDTO
// Purpose: Data Transfer Object (DTO) to store command hooks for pre- and post-scaffold execution.
// Functions:
//   - __construct(): initializes the DTO with before and after commands
// ===============================================
final class CommandsContextDTO
{
    public function __construct(
        public array $beforeCommands,
        public array $afterCommands,
    ) {}
}
