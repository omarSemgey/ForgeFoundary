<?php

namespace App\Src\Domains\Units\DTOs;

final class UnitContextDTO
{
    public function __construct(
        public array $units,
        public string $unitsMapMode,
        public bool $unitsMapDefaults,
        public array $unitsMapOverrides,
    ) {}
}