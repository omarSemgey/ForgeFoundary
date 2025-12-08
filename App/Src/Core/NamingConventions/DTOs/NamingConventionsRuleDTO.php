<?php

namespace App\Src\Core\NamingConventions\DTOs;

final class NamingConventionsRuleDTO
{
    public function __construct(
        public string $style,      
        public bool   $enabled,
    ) {}
}