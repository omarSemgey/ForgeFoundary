<?php

namespace App\Src\Domains\Templates\DTOs;

// =======================================================
// Class: TemplateCombinedDTO
// Purpose: A simple Data Transfer Object (DTO) that combines
//          template metadata and file metadata into a single object.
// Functions:
//   - __construct(): initializes the combined DTO with a TemplateDataDTO and FileDataDTO
// =======================================================
final class TemplateCombinedDTO
{
    public function __construct(
        public TemplateDataDTO $templateDataDTO,
        public FileDataDTO $fileDataDTO,
    ) {}
}
