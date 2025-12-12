<?php

namespace App\Src\Core\NamingConventions\DTOs;

final class NamingConventionsContextDTO
{
    public function __construct(
        public array $NamingConventionsSections,
    ) {}
}