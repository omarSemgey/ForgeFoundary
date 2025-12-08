<?php

namespace App\Src\Domains\CliFlags\DTOs;

final class CliFlagsContextDTO
{
    public function __construct(
        public array $definedCliFlags,
        public array $providedCliFlags,
    ) {}
}
