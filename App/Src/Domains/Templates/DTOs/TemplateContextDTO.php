<?php

namespace App\Src\Domains\Templates\DTOs;

final class TemplateContextDTO
{
    public function __construct(
        public string $templatesPath,
        public array $templateEngingeExtensions,
        public array $templates,
        public array $templatesDefaults,
        public array $templatesOverrides,
        public bool $templateRequireExistingDirs,
    ) {}
}