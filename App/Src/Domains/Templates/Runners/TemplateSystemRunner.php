<?php

namespace App\Src\Domains\Templates\Runners;

use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplatesDTO;
use App\Src\Domains\Templates\Generators\TemplatesGenerator;
use App\Src\Domains\Templates\Resolvers\TemplateContextResolver;
use App\Src\Domains\Templates\Resolvers\TemplatesResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: TemplateSystemRunner
// Purpose: Orchestrates the templates subsystem. Validates, resolves contexts, 
//          and triggers template generation. Publishes resulting DTOs to the 
//          global ContextBus.
// Functions:
//   - run(): main entry point to execute the system runner
//   - validate(): placeholder for future validation logic
//   - resolve(): resolves template context and templates, publishing DTOs
//   - generate(): triggers template generation
//   - publishDTO(): publishes a given DTO to the ContextBus
// ===============================================
class TemplateSystemRunner
{
    public function __construct(
        private TemplateContextResolver $templateContextResolver,
        private TemplatesResolver $templatesResolver, 
        private TemplatesGenerator $templatesGenerator,
        private SystemStateManager $systemStateManager,
    ) {}

    // ===============================================
    // Function: run
    // Inputs: none
    // Outputs: none
    // Purpose: Executes the templates system runner
    // Logic:
    //   1. Logs start
    //   2. Checks if templates system is enabled
    //   3. Runs validate(), resolve(), generate() in order
    //   4. Logs finish
    // Side Effects: may trigger template generation, publishes DTOs
    // Uses: validate(), resolve(), generate(), Debugger(), SystemStateManager
    // ===============================================
    public function run(): void
    {
        Debugger()->header('Templates System Runner Started.', 'big');
        if(!$this->systemStateManager->assertEnabled('templates', 'Templates')){
            return;
        }

        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Templates System Runner Finished.', 'big');
    }

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: none
    // Purpose: Placeholder for future validation logic of templates
    // Logic: Currently empty
    // Side Effects: none
    // Uses: none
    // ===============================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ===============================================
    // Function: generate
    // Inputs: none
    // Outputs: none
    // Purpose: Triggers template generation process
    // Logic:
    //   1. Logs start
    //   2. Calls $templatesGenerator->generateTemplates()
    //   3. Logs finish
    // Side Effects: may create template files
    // Uses: TemplatesGenerator, Debugger
    // ===============================================
    private function generate(): void
    {
        Debugger()->header('Templates Generator Started.', 'medium');
        $this->templatesGenerator->generateTemplates();
        Debugger()->header('Templates Generator Finished.', 'medium');
    }

    // ===============================================
    // Function: resolve
    // Inputs: none
    // Outputs: none
    // Purpose: Resolves template-related context and templates
    // Logic:
    //   1. Logs start
    //   2. Resolves TemplateContextDTO via TemplateContextResolver
    //   3. Publishes TemplateContextDTO
    //   4. Resolves TemplatesDTO via TemplatesResolver
    //   5. Publishes TemplatesDTO
    //   6. Logs finish
    // Side Effects: publishes DTOs to ContextBus
    // Uses: TemplateContextResolver, TemplatesResolver, publishDTO, Debugger
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Templates system Resolvers Started.', 'medium');

        Debugger()->header('Template Context Resolver Started.', 'medium');
        $templateContextDTO = $this->templateContextResolver->resolveTemplatesContext();
        $this->publishDTO(TemplateContextDTO::class,$templateContextDTO);
        Debugger()->header('Template Context Resolver Finished.', 'medium');

        Debugger()->header('Templates Resolver Started.', 'medium');
        $templatesDTO = $this->templatesResolver->resolveTemplates();
        $this->publishDTO(TemplatesDTO::class,$templatesDTO);
        Debugger()->header('Templates Resolver Finished.', 'medium');

        Debugger()->header('Templates system Resolvers Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - $dtoKey: string, the class name/key of the DTO
    //   - $dto: object, the DTO instance to publish
    // Outputs: none
    // Purpose: Publishes a DTO to the global ContextBus
    // Logic:
    //   1. Logs start
    //   2. Calls ContextBus()->publish with the DTO
    //   3. Logs info message
    //   4. Logs finish
    // Side Effects: updates global context bus
    // Uses: ContextBus(), Debugger()
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Templates Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Templates Context Publisher Finished.', 'medium');
    }
}
