<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\FileDataDTO;
use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;
use Symfony\Component\Yaml\Yaml;

class FileDataResolver
{
    private string|null $fileName;
    private array|null $filePaths;
    private string|null $fileExtension;
    private bool|null $fileDisabled;
    private string|null $templateEngine;
    private array|null $filePlaceholders;
    private string|null $templateName;
    private string|null $templateMetadata;
    private array|null $updatedTemplateMetadata;
    private array|null $templateOverrides;
    private array|null $templatesDefaults;
    private const OPTIONAL_DATA=[
        "filePlaceholders",
    ];

    public function __construct(
        private TemplateResolvingManager $templateResolvingManager, 
    ){}

    public function resolveFileData(TemplateDataDTO $templateData, array $templatesDefaults):FileDataDTO|null{
        // Exaplination: if all required values exist it returns a valid FileDataDTO if not it returns null
        $this->runResolvers($templateData,$templatesDefaults);

        $fileData = [
            'fileName' => $this->fileName,
            'filePaths' => $this->filePaths,
            'fileExtension' => $this->fileExtension,
            'fileDisabled' => $this->fileDisabled,
            'templateEngine' => $this->templateEngine,
            'filePlaceholders' => $this->filePlaceholders,
        ];

        foreach($fileData as $key => $value){
            if($value === null){
                // Returns null if it doesnt have a required value
                if(!in_array($key, self::OPTIONAL_DATA, true)) return null;
                
                Debugger()->warning("Template '{$this->templateName}' Does not provide {$key}");
            }
        }

        $this->templateResolvingManager->logData($this->templateName, $fileData, "template");

        return new FileDataDTO(
            $this->fileName,
            $this->filePaths,
            $this->fileExtension,
            $this->fileDisabled,
            $this->templateEngine,
            $this->filePlaceholders,
        );
    }

    private function runResolvers(TemplateDataDTO $templateData, array $templatesDefaults): void{
        $this->resolveFileTemplateData($templateData,$templatesDefaults);
        $this->resolveFilePlaceholders();
        $this->resolveTemplateEngine();
        $this->updateTemplateMetadata();
        $this->resolveFileName();
        $this->resolveFilePaths();
        $this->resolveFileExtension();
        $this->resolveFileDisabled();
    }

    private function updateTemplateMetadata(): void{
        $mustaceEngineInstance = new \Mustache\Engine();
        $updatedMetadata = $mustaceEngineInstance->render($this->templateMetadata, $this->filePlaceholders);
        $updatedMetadata = Yaml::parse($updatedMetadata) ?? [];
        $this->updatedTemplateMetadata = $updatedMetadata;
    }

    private function resolveFileTemplateData(TemplateDataDTO $templateData, array $templatesDefaults): void{
        $this->templateMetadata = $templateData->templateMetaData;
        $this->templateOverrides = $templateData->templateOverrides;
        $this->templatesDefaults = $templatesDefaults;
        $this->templateName = $templateData->templateName;
    }

    private function resolveTemplateEngine(): void{
        $parts = explode('.', $this->templateName, 2);
        $this->templateEngine = $parts[1] ?? 'unknown';
    }
    
    private function resolveFilePlaceholders():void{
        $override = $this->templateOverrides['placeholders'] ?? [];
        $defaults = $this->templatesDefaults['placeholders'] ?? [];
        $yamlParsedTemplateMetadata = Yaml::parse($this->templateMetadata) ?? [];

        # Note: this insures the hierchy Overrides -> Metadata -> Defaults
        $placeholders = $override + $yamlParsedTemplateMetadata + $defaults;

        $this->filePlaceholders = empty($placeholders) ? null : $placeholders;
    }

    private function resolveFileName():void{
        $override = $this->templateOverrides['file_name'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_name'] ?? null;
        $default = $this->templatesDefaults['file_name'] ?? null;
        $this->fileName = $override ?? $metadata ?? $default;
    }

    private function resolveFileExtension():void {
        $override = $this->templateOverrides['file_extension'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_extension'] ?? null;
        $default = $this->templatesDefaults['file_extension'] ?? null;
        $this->fileExtension = $override ?? $metadata ?? $default;
    }

    private function resolveFilePaths(): void{
        $override = $this->templateOverrides['file_paths'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_paths'] ?? null;
        $default = $this->templatesDefaults['file_paths'] ?? null;
        $this->filePaths = $override ?? $metadata ?? $default;
    }

    private function resolveFileDisabled() : void{
        $override = $this->templateOverrides['file_disabled'] ?? false;
        $metadata = $this->updatedTemplateMetadata['file_disabled'] ?? false;
        $default = $this->templatesDefaults['file_disabled'] ?? false;
        $this->fileDisabled = $override || $metadata || $default;
    }
}