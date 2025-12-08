<?php

namespace App\Src\Domains\Templates\DTOs;

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