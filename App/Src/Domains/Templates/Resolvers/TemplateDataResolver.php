<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;

// ===============================================
// Class: TemplateDataResolver
// Purpose: Resolves the data for a single template file, including
//          its contents, metadata (YAML front-matter), and any overrides.
//          Returns a TemplateDataDTO with all resolved data.
// Functions:
//   - resolveTemplateData(): main function to resolve all aspects of a template
//   - resolveTemplateMetadata(): extracts YAML front-matter metadata
//   - resolveTemplateContents(): extracts template content without metadata
//   - resolveTemplateOverrides(): applies any overrides for the template
// ===============================================
class TemplateDataResolver
{
    // Internal properties used to store template state
    private string $templateName;
    private string $templatePath;
    private string $templateContents;
    private string $templateMetaData;
    private array $templateOverrides;

    public function __construct(
        private TemplateResolvingManager $templateResolvingManager
    ) {}

    // ===============================================
    // Function: resolveTemplateData
    // Inputs:
    //   - $templatePath: string path to the template file
    //   - $templateName: string template identifier
    //   - $overrides: array of template-specific overrides
    // Outputs: TemplateDataDTO containing name, path, contents, metadata, and overrides
    // Purpose: Central method to resolve a template's full data
    // Logic:
    //   1. Store template path and name
    //   2. Resolve template contents (excluding metadata)
    //   3. Resolve template metadata (YAML front-matter)
    //   4. Resolve any overrides for this template
    //   5. Log the template resolution data
    //   6. Return a new TemplateDataDTO with all resolved info
    // Side Effects: writes log via TemplateResolvingManager
    // Uses: resolveTemplateContents(), resolveTemplateMetadata(), resolveTemplateOverrides()
    // ===============================================
    public function resolveTemplateData(string $templatePath, string $templateName, array $overrides): TemplateDataDTO
    {
        $this->templateName = $templateName;
        $this->templatePath = $templatePath;

        $this->resolveTemplateContents($this->templatePath);
        $this->resolveTemplateMetadata($this->templatePath);
        $this->resolveTemplateOverrides($this->templateName, $overrides);

        $templateLogData = [
            'templateName' => $this->templateName,
            'templatePath' => $this->templatePath,
            'templateMetaData' => $this->templateMetaData,
            'templateOverrides' => $this->templateOverrides,
        ];

        $this->templateResolvingManager->logData($this->templateName, $templateLogData, "file");

        return new TemplateDataDTO(
            $this->templateName,
            $this->templatePath,
            $this->templateContents,
            $this->templateMetaData,
            $this->templateOverrides,
        );
    }

    // ===============================================
    // Function: resolveTemplateMetadata
    // Inputs: $path - string path to template file
    // Outputs: none (stores metadata in $this->templateMetaData)
    // Purpose: Extracts YAML front-matter metadata block from template
    // Logic:
    //   1. Reads file contents
    //   2. Uses regex to extract content between '---' delimiters
    //   3. Stores extracted metadata or empty string if none found
    // Side Effects: sets $this->templateMetaData
    // Uses: none
    // ===============================================
    private function resolveTemplateMetadata(string $path): void
    {
        $contents = file_get_contents($path);
        if (preg_match('/^---\s*(.*?)\s*---/s', $contents, $matches)) {
            $yamlBlock = $matches[1];
            $this->templateMetaData = $yamlBlock;
            return;
        }

        $this->templateMetaData = '';
    }

    // ===============================================
    // Function: resolveTemplateContents
    // Inputs: $path - string path to template file
    // Outputs: none (stores template content in $this->templateContents)
    // Purpose: Extracts template content excluding metadata block
    // Logic:
    //   1. Reads file contents
    //   2. Uses regex to separate metadata from body
    //   3. Stores body content in $this->templateContents
    // Side Effects: sets $this->templateContents
    // Uses: none
    // ===============================================
    private function resolveTemplateContents($path): void
    {
        $contents = file_get_contents($path);

        if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $contents, $matches)) {
            $bodyPart = $matches[2];
            $this->templateContents = $bodyPart;
            return;
        }

        $this->templateContents = $contents;
    }

    // ===============================================
    // Function: resolveTemplateOverrides
    // Inputs:
    //   - $templateName: string template identifier
    //   - $overrides: array containing overrides for all templates
    // Outputs: none (stores relevant overrides in $this->templateOverrides)
    // Purpose: Extracts any template-specific overrides from the overrides array
    // Logic:
    //   1. Checks if overrides exist for the given template
    //   2. Stores them in $this->templateOverrides
    // Side Effects: sets $this->templateOverrides
    // Uses: none
    // ===============================================
    private function resolveTemplateOverrides(string $templateName, array $overrides): void
    {
        $this->templateOverrides = $overrides[$templateName] ?? [];
    }
}
