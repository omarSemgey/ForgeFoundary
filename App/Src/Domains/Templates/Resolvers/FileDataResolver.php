<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\FileDataDTO;
use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;
use Symfony\Component\Yaml\Yaml;

// =======================================================
// Class: FileDataResolver
// Purpose: Resolves all necessary data for a template file 
//          (e.g., name, paths, extension, placeholders, engine).
//          Ensures required data exists and prepares a FileDataDTO.
// Functions:
//   - resolveFileData(): main entry point for resolving a file's data
//   - runResolvers(): calls individual resolvers for each property
//   - resolveFileTemplateData(): sets template metadata and defaults
//   - resolveFilePlaceholders(): determines placeholders hierarchy
//   - resolveTemplateEngine(): detects template engine from template name
//   - updateTemplateMetadata(): applies placeholders to metadata via Mustache
//   - resolveFileName(): determines final file name
//   - resolveFilePaths(): determines paths for the file
//   - resolveFileExtension(): determines file extension
//   - resolveFileDisabled(): determines if file is disabled
// =======================================================
class FileDataResolver
{
    // Private properties to store resolved file data
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

    // Optional properties that can be null without invalidating the file
    private const OPTIONAL_DATA=[
        "filePlaceholders",
    ];

    public function __construct(
        private TemplateResolvingManager $templateResolvingManager, 
    ){}

    // =======================================================
    // Function: resolveFileData
    // Inputs: 
    //   - $templateData: TemplateDataDTO instance
    //   - $templatesDefaults: array of default template values
    // Outputs: FileDataDTO|null
    // Purpose: Resolves all properties for a template file, validates required values,
    //          and returns a FileDataDTO if valid.
    // Logic Walkthrough:
    //   1. Calls runResolvers() to fill all properties
    //   2. Checks if any required property is missing; returns null if so
    //   3. Logs resolved data using TemplateResolvingManager
    //   4. Returns a FileDataDTO with all resolved properties
    // Side Effects: logs warnings for missing optional properties, logs resolved data
    // Uses: runResolvers(), TemplateResolvingManager, Debugger
    // =======================================================
    public function resolveFileData(TemplateDataDTO $templateData, array $templatesDefaults): FileDataDTO|null
    {
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
                // Return null if a required property is missing
                if(!in_array($key, self::OPTIONAL_DATA, true)) return null;
                
                Debugger()->warning("Template '{$this->templateName}' does not provide {$key}");
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

    // =======================================================
    // Function: runResolvers
    // Inputs: same as resolveFileData
    // Outputs: none
    // Purpose: Executes all individual resolvers for this file
    // Logic: calls resolvers in a fixed order to populate properties
    // Side Effects: sets internal properties
    // Uses: resolveFileTemplateData(), resolveFilePlaceholders(), resolveTemplateEngine(),
    //       updateTemplateMetadata(), resolveFileName(), resolveFilePaths(),
    //       resolveFileExtension(), resolveFileDisabled()
    // =======================================================
    private function runResolvers(TemplateDataDTO $templateData, array $templatesDefaults): void
    {
        $this->resolveFileTemplateData($templateData,$templatesDefaults);
        $this->resolveFilePlaceholders();
        $this->resolveTemplateEngine();
        $this->updateTemplateMetadata();
        $this->resolveFileName();
        $this->resolveFilePaths();
        $this->resolveFileExtension();
        $this->resolveFileDisabled();
    }

    // =======================================================
    // Function: resolveFileTemplateData
    // Inputs: TemplateDataDTO, templatesDefaults
    // Outputs: none
    // Purpose: sets initial template metadata and overrides
    // Side Effects: populates templateMetadata, templateOverrides, templatesDefaults, templateName
    // =======================================================
    private function resolveFileTemplateData(TemplateDataDTO $templateData, array $templatesDefaults): void
    {
        $this->templateMetadata = $templateData->templateMetaData;
        $this->templateOverrides = $templateData->templateOverrides;
        $this->templatesDefaults = $templatesDefaults;
        $this->templateName = $templateData->templateName;
    }

    // =======================================================
    // Function: resolveTemplateEngine
    // Inputs: none
    // Outputs: sets $this->templateEngine
    // Purpose: extracts template engine from template name (e.g., "controller.mustache" => "mustache")
    // Side Effects: sets templateEngine property
    // =======================================================
    private function resolveTemplateEngine(): void
    {
        $parts = explode('.', $this->templateName, 2);
        $this->templateEngine = $parts[1] ?? 'unknown';
    }

    // =======================================================
    // Function: resolveFilePlaceholders
    // Inputs: none
    // Outputs: sets $this->filePlaceholders
    // Purpose: merges placeholders from overrides, metadata, defaults
    // Side Effects: sets filePlaceholders property
    // Uses: Yaml::parse()
    // =======================================================
    private function resolveFilePlaceholders(): void
    {
        $override = $this->templateOverrides['placeholders'] ?? [];
        $defaults = $this->templatesDefaults['placeholders'] ?? [];
        $yamlParsedTemplateMetadata = Yaml::parse($this->templateMetadata) ?? [];

        // Hierarchy: Overrides > Metadata > Defaults
        $placeholders = $override + $yamlParsedTemplateMetadata + $defaults;

        $this->filePlaceholders = empty($placeholders) ? null : $placeholders;
    }

    // =======================================================
    // Function: updateTemplateMetadata
    // Inputs: none
    // Outputs: sets $this->updatedTemplateMetadata
    // Purpose: applies placeholders to metadata using Mustache engine and parses YAML
    // Side Effects: sets updatedTemplateMetadata property
    // Uses: Mustache\Engine, Yaml::parse()
    // =======================================================
    private function updateTemplateMetadata(): void
    {
        $mustaceEngineInstance = new \Mustache\Engine();
        $updatedMetadata = $mustaceEngineInstance->render($this->templateMetadata, $this->filePlaceholders);
        $updatedMetadata = Yaml::parse($updatedMetadata) ?? [];
        $this->updatedTemplateMetadata = $updatedMetadata;
    }

    // =======================================================
    // Function: resolveFileName
    // Inputs: none
    // Outputs: sets $this->fileName
    // Purpose: determines file name from overrides > metadata > defaults
    // Side Effects: sets fileName property
    // =======================================================
    private function resolveFileName(): void
    {
        $override = $this->templateOverrides['file_name'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_name'] ?? null;
        $default = $this->templatesDefaults['file_name'] ?? null;
        $this->fileName = $override ?? $metadata ?? $default;
    }

    // =======================================================
    // Function: resolveFilePaths
    // Inputs: none
    // Outputs: sets $this->filePaths
    // Purpose: determines file paths from overrides > metadata > defaults
    // Side Effects: sets filePaths property
    // =======================================================
    private function resolveFilePaths(): void
    {
        $override = $this->templateOverrides['file_paths'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_paths'] ?? null;
        $default = $this->templatesDefaults['file_paths'] ?? null;
        $this->filePaths = $override ?? $metadata ?? $default;
    }

    // =======================================================
    // Function: resolveFileExtension
    // Inputs: none
    // Outputs: sets $this->fileExtension
    // Purpose: determines file extension from overrides > metadata > defaults
    // Side Effects: sets fileExtension property
    // =======================================================
    private function resolveFileExtension(): void
    {
        $override = $this->templateOverrides['file_extension'] ?? null;
        $metadata = $this->updatedTemplateMetadata['file_extension'] ?? null;
        $default = $this->templatesDefaults['file_extension'] ?? null;
        $this->fileExtension = $override ?? $metadata ?? $default;
    }

    // =======================================================
    // Function: resolveFileDisabled
    // Inputs: none
    // Outputs: sets $this->fileDisabled
    // Purpose: determines if the file is disabled from overrides, metadata, defaults
    // Side Effects: sets fileDisabled property
    // =======================================================
    private function resolveFileDisabled(): void
    {
        $override = $this->templateOverrides['file_disabled'] ?? false;
        $metadata = $this->updatedTemplateMetadata['file_disabled'] ?? false;
        $default = $this->templatesDefaults['file_disabled'] ?? false;
        $this->fileDisabled = $override || $metadata || $default;
    }
}
