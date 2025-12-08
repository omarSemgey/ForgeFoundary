<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Templates\DTOs\TemplateCombinedDTO;
use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplatesDTO;

class TemplatesResolver
{
    private TemplateContextDTO $templateContextDTO;
    private ComponentContextDTO $componentContextDTO;

    public function __construct(
        protected TemplateDataResolver $templateDataResolver,
        protected FileDataResolver $fileDataResolver,
    ){}

    private function loadContexts(): void{
        $this->templateContextDTO = ContextBus()->get(TemplateContextDTO::class);
        Debugger()->info("Loaded context: 'TemplateContextDTO' from the context bus");
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
    }

    public function resolveTemplates(): TemplatesDTO
    {
        $this->loadContexts();
        $templates = [];
        
        foreach($this->templateContextDTO->templates as $templateName => $templatePath){
            Debugger()->header("Template {$templateName} Resolver Started.", "medium");
            
            $templatesDefaults = $this->templateContextDTO->templatesDefaults;

            $templateData = $this->templateDataResolver->resolveTemplateData($templatePath, $templateName, $this->templateContextDTO->templatesOverrides);
            
            $fileData = $this->fileDataResolver->resolveFileData($templateData, $templatesDefaults);
            if($fileData === null) {
                Debugger()->error("'{$templateName}' does not have valid file data.");
                continue;
            }
            
            $templates[$templateName] = new TemplateCombinedDTO($templateData, $fileData);

            Debugger()->header("Template {$templateName} Resolver Finished.", "medium");
        }

        return new TemplatesDTO($templates);
    }
}