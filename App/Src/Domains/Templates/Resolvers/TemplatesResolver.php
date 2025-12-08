<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Templates\DTOs\TemplateCombinedDTO;
use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplatesDTO;

// ===============================================
// Class: TemplatesResolver
// Purpose: Resolves all templates and their associated file data. Combines 
//          template and file data into TemplateCombinedDTOs and returns a 
//          TemplatesDTO containing all resolved templates.
// Functions:
//   - resolveTemplates(): main public function to resolve all templates
//   - loadContexts(): loads required contexts from the global ContextBus
// ===============================================
class TemplatesResolver
{
    private TemplateContextDTO $templateContextDTO; // Holds template-related context

    public function __construct(
        protected TemplateDataResolver $templateDataResolver,
        protected FileDataResolver $fileDataResolver,
    ){}

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: none
    // Purpose: Loads the TemplateContextDTO and ComponentContextDTO from ContextBus
    // Logic:
    //   1. Fetch TemplateContextDTO from ContextBus and store in $this->templateContextDTO
    //   2. Fetch ComponentContextDTO from ContextBus and store in $this->componentContextDTO
    //   3. Logs info messages for both
    // Side Effects: none besides logging
    // Uses: ContextBus(), Debugger()
    // ===============================================
    private function loadContexts(): void
    {
        $this->templateContextDTO = ContextBus()->get(TemplateContextDTO::class);
        Debugger()->info("Loaded context: 'TemplateContextDTO' from the context bus");
    }

    // ===============================================
    // Function: resolveTemplates
    // Inputs: none
    // Outputs: TemplatesDTO containing all resolved templates
    // Purpose: Iterates through each template in TemplateContextDTO, resolves
    //          its data and file data, and packages them into TemplateCombinedDTOs
    // Logic:
    //   1. Loads contexts via loadContexts()
    //   2. Initializes empty array for resolved templates
    //   3. Loops over each template:
    //        a. Logs start header
    //        b. Resolves template data using TemplateDataResolver
    //        c. Resolves file data using FileDataResolver
    //        d. Skips template if file data is null (invalid)
    //        e. Combines template and file data into TemplateCombinedDTO
    //        f. Stores in templates array
    //        g. Logs finished header
    //   4. Returns a new TemplatesDTO with all resolved templates
    // Side Effects: Logs progress via Debugger()
    // Uses: loadContexts(), TemplateDataResolver, FileDataResolver, Debugger
    // ===============================================
    public function resolveTemplates(): TemplatesDTO
    {
        $this->loadContexts();
        $templates = [];
        
        foreach($this->templateContextDTO->templates as $templateName => $templatePath){
            Debugger()->header("Template {$templateName} Resolver Started.", "medium");
            
            $templatesDefaults = $this->templateContextDTO->templatesDefaults;

            // Resolve template metadata/data with overrides
            $templateData = $this->templateDataResolver->resolveTemplateData(
                $templatePath,
                $templateName,
                $this->templateContextDTO->templatesOverrides
            );
            
            // Resolve actual file data (filename, path, content, etc.)
            $fileData = $this->fileDataResolver->resolveFileData($templateData, $templatesDefaults);
            if($fileData === null) {
                Debugger()->error("'{$templateName}' does not have valid file data.");
                continue;
            }
            
            // Combine template metadata and file data into TemplateCombinedDTO
            $templates[$templateName] = new TemplateCombinedDTO($templateData, $fileData);

            Debugger()->header("Template {$templateName} Resolver Finished.", "medium");
        }

        return new TemplatesDTO($templates);
    }
}