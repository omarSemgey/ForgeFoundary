<?php

namespace App\Src\Domains\CliFlags\DTOs;

// ===============================================
// Class: CliFlagsContextDTO
// Purpose: Data Transfer Object (DTO) for storing CLI flags state.
//          Holds both the CLI flags that are defined in the system and
//          the flags actually provided by the user at runtime.
// Functions:
//   - __construct(): Initializes the DTO with defined and provided flags
// ===============================================
final class CliFlagsContextDTO
{
    public function __construct(
        public array $definedCliFlags,
        public array $providedCliFlags,
    ) {}
}
