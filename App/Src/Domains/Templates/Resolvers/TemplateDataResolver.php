<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\TemplateDataDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;
use Log;
use Symfony\Component\Yaml\Yaml;

class TemplateDataResolver
{
    private string $templateName;
    private string $templatePath;
    private string $templateContents;
    private string $templateMetaData;
    private array $templateOverrides;

    public function __construct(
        private TemplateResolvingManager $templateResolvingManager, 
    ){}

    public function resolveTemplateData(string $templatePath, string $templateName,array $overrides): TemplateDataDTO{
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

    private function resolveTemplateMetadata(string $path): void{
        $contents = file_get_contents($path);
        if (preg_match('/^---\s*(.*?)\s*---/s', $contents, $matches)) {
            $yamlBlock = $matches[1];
            $this->templateMetaData = $yamlBlock;
            return;
        }

        $this->templateMetaData = '';
    }

    # Gets the template contents without the metadata
    private function resolveTemplateContents($path): void{
        $contents = file_get_contents($path);

        if (preg_match('/^---\s*(.*?)\s*---\s*(.*)$/s', $contents, $matches)) {
            $bodyPart = $matches[2];
            
            $this->templateContents = $bodyPart;
            return;
        }

        $this->templateContents = $contents;
    }

    private function resolveTemplateOverrides(string $templateName, array $overrides): void{
        $this->templateOverrides = $overrides[$templateName] ?? [];
    }
}