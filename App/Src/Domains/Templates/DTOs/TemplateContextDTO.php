<?php

namespace App\Src\Domains\Templates\DTOs;

// ===============================================
// Class: TemplateContextDTO
// Purpose: Holds the resolved context information for templates. 
//          This DTO is published to the global ContextBus and used by
//          generators and resolvers in the templates subsystem.
// Functions:
//   - __construct(): initializes all template context properties
// ===============================================
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
