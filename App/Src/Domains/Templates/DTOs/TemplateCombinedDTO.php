<?php

namespace App\Src\Domains\Templates\DTOs;

final class TemplateCombinedDTO
{
    public function __construct(
        public TemplateDataDTO $templateDataDTO,
        public FileDataDTO $fileDataDTO,
    ) {}
}
