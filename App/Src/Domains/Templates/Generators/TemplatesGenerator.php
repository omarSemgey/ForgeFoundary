<?php

namespace App\Src\Domains\Templates\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Templates\DTOs\TemplatesDTO;
use App\Src\Domains\Templates\Helpers\FileGenerationManager;
use App\Src\Domains\Templates\Helpers\TemplateEngineManager;

class TemplatesGenerator
{
    private TemplatesDTO $templatesDTO;
    private ComponentContextDTO $componentContextDTO;

    public function __construct(
        private FileGenerationManager $fileGenerationManager,
        private TemplateEngineManager $templateEngineManager,
        ){}

    private function loadContexts(): void{
        $this->templatesDTO = ContextBus()->get(TemplatesDTO::class);
        Debugger()->info("Loaded context: 'TemplatesDTO' from the context bus");
    }

    public function generateTemplates():void
    {
        $this->loadContexts();
        foreach($this->templatesDTO->templates as $templateName => $combined){
            $templateData = $combined->templateDataDTO;
            $fileData = $combined->fileDataDTO;

            $fileContent = $this->templateEngineManager->renderTemplate($fileData->filePlaceholders, $templateData->templateContents, $fileData->templateEngine);
            $this->fileGenerationManager->createFile($fileData, $templateData, $fileContent);
        }
    }
} 