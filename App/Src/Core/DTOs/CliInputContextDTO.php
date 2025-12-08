<?php

namespace App\Src\Core\DTOs;

class CliInputContextDTO
{
    public function __construct(
        public readonly array $options = [],
        public readonly array $arguments = []
    ) {}

    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function getArgument(string $key): mixed
    {
        return $this->arguments[$key] ?? null;
    }
}
