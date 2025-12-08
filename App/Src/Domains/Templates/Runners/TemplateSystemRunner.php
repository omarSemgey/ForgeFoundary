<?php

namespace App\Src\Domains\Templates\Runners;

use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\DTOs\TemplatesDTO;
use App\Src\Domains\Templates\Generators\TemplatesGenerator;
use App\Src\Domains\Templates\Resolvers\TemplateContextResolver;
use App\Src\Domains\Templates\Resolvers\TemplatesResolver;
use App\Src\Core\Helpers\SystemStateManager;

class TemplateSystemRunner
{
    public function __construct(
        private TemplateContextResolver $templateContextResolver,
        private TemplatesResolver $templatesResolver, 
        private TemplatesGenerator $templatesGenerator,
        private SystemStateManager $systemStateManager,
        ) {}

    public function run(): void
    {
        Debugger()->header('Templates System Runner Started.', 'big');
        if(!$this->systemStateManager->assertEnabled('templates', 'Templates')){
            return;
        };

        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Templates System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function generate(): void{
        Debugger()->header('Templates Generator Started.', 'medium');
        $this->templatesGenerator->generateTemplates();
        Debugger()->header('Templates Generator Finished.', 'medium');
    }

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

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Templates Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Templates Context Publisher Finished.', 'medium');
    }
}
