<?php

namespace App\Src\Core\NamingConventions\DTOs;

final class NamingConventionsSectionDTO
{
    public function __construct(
        public array $defaults,
        public array $overrides,
    ) {}
}
