<?php

namespace App\Src\Domains\Templates\DTOs;

// ===============================================
// Class: TemplatesDTO
// Purpose: Data Transfer Object (DTO) to hold an array of template objects.
// Functions:
//   - __construct(): initializes the DTO with an array of templates
// ===============================================
final class TemplatesDTO
{
    public function __construct(
        public array $templates
    ) {}
}