<?php

namespace App\Src\Domains\Commands\DTOs;

final class CommandsContextDTO
{
    public function __construct(
        public array $beforeCommands,
        public array $afterCommands,
    ) {}
}
