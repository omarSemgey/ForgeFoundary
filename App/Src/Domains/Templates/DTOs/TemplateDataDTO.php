<?php

namespace App\Src\Domains\Templates\DTOs;

// ==========================================================
// Class: TemplateDataDTO
// Purpose: Data Transfer Object (DTO) that represents a single
//          template's data including its name, path, contents,
//          metadata, and any overrides.
// Functions:
//   - __construct(): initializes the DTO with all template properties
// ==========================================================
final class TemplateDataDTO
{
    public function __construct(
        public string $templateName,
        public string $templatePath,
        public string $templateContents,
        public string $templateMetaData,
        public array $templateOverrides,
    ) {}
}
