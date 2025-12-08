<?php

namespace App\Src\Domains\Templates\DTOs;

// ===============================================
// Class: FileDataDTO
// Purpose: Represents metadata for a single template-generated file.
// Functions:
//   - __construct(): initializes all file data properties
// ===============================================
final class FileDataDTO
{
    public function __construct(
        public string $fileName,
        public array $filePaths,
        public string $fileExtension,
        public bool $fileDisabled,
        public string $templateEngine,
        public array $filePlaceholders,
    ) {}
}
