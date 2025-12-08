<?php

namespace App\Src\Domains\Directories\DTOs;

final class DirectoryContextDTO
{
    public function __construct(
        public array $directories
    ) {}
}
