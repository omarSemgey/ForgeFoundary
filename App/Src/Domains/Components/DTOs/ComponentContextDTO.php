<?php

namespace App\Src\Domains\Components\DTOs;

final class ComponentContextDTO
{
    public function __construct(
        public string $componentName,
        public string $componentPath,
    ) {}
}