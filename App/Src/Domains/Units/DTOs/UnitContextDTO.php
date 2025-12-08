<?php

namespace App\Src\Domains\Units\DTOs;

// ===============================================
// Class: UnitContextDTO
// Purpose: Data Transfer Object (DTO) representing the context for units during scaffolding.
//          Carries configuration and mapping information about units for a particular run.
// Functions:
//   - __construct(): initializes the DTO with unit data, mapping mode, defaults, and overrides.
// ===============================================
final class UnitContextDTO
{
    public function __construct(
        public array $units,
        public string $unitsMapMode,
        public bool $unitsMapDefaults,
        public array $unitsMapOverrides,
    ) {}
}
