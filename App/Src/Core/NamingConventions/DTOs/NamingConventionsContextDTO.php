<?php

namespace App\Src\Core\NamingConventions\DTOs;

final class NamingConventionsContextDTO
{
    public array $rules = []; // e.g. "templates:dto.mustache" => NamingConventionsRuleDTO

    public function setRule(string $key, NamingConventionsRuleDTO $rule): void
    {
        $this->rules[$key] = $rule;
    }

    public function getRule(string $key): ?NamingConventionsRuleDTO
    {
        return $this->rules[$key] ?? null;
    }
}
