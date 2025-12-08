<?php

namespace App\Src\Domains\Templates\DTOs;

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