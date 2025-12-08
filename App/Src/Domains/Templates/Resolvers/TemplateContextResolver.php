<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

class TemplateContextResolver
{
    private string $templatesPath;
    protected array $templateEngineExtensions;
    private array $templates;
    private array $templatesDefaults;
    private array $templatesOverrides;
    private bool $templatesRequireExistingDirs;
    private const TEMPLATE_CONFIG_KEYS = [
        "templates" => "templates",
        "path" => "templates_path",
        "engine_extensions" => "template_engine_extensions",
        "defaults" => "defaults",
        "overrides" => "overrides",
        "templates_require_dirs" => "templates_require_existing_dirs"
    ];

    public function __construct(
        private TemplateResolvingManager $templateResolvingManager, 
        private PathManager $pathManager
    ){}
    
    public function resolveTemplatesContext(): TemplateContextDTO
    {
        $this->resolveTemplatesPath();
        $this->resolveTemplateEngineExtensions();
        $this->resolveTemplates();
        $this->resolveTemplatesDefaults();
        $this->resolveTemplatesOverrides();
        $this->resolveTemplateRequireExistingDirs();

        return new TemplateContextDTO(
            $this->templatesPath,
            $this->templateEngineExtensions,
            $this->templates,
            $this->templatesDefaults,
            $this->templatesOverrides,
            $this->templatesRequireExistingDirs,
        );
    }

    private function resolveTemplatesPath(): void{
        $this->templatesPath = $this->pathManager->getAbsolutePath(Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS["path"]), true); 
        Debugger()->info("Templates path: '{$this->templatesPath}'");
    }

    private function resolveTemplateEngineExtensions(): void{
        $this->templateEngineExtensions = Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS["engine_extensions"]);
        $logTemplateEngineExtensions = count($this->templateEngineExtensions) ?  "Template engine extensions available: '[" . implode(', ', $this->templateEngineExtensions) . "]'" : 'No template engine extensions available';
        Debugger()->info($logTemplateEngineExtensions);
    }

    private function resolveTemplates(): void{
        $templates = [];
        $templatesNames = [];
        $files = File::allFiles($this->templatesPath);
        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            
            if(!in_array($fileExtension, $this->templateEngineExtensions, true)){
                $logTemplateEngineExtensions = count($this->templateEngineExtensions) ?  "'[" . implode(', ', $this->templateEngineExtensions) . "]'" : 'No template engines extensions available';
                Debugger()->error("'{$fileExtension}' is not an accepted template engine. Accepted template engine extensions: '{$logTemplateEngineExtensions}'");
                continue;
            } 

            $templatesNames[] = $fileName;
            $templates[$fileName] = $file->getRealPath();
        }
        $logTemplateNames = count($templatesNames) ?  "Provided templates:'[" . implode(', ', $templatesNames) . "]'" : 'No templates were provided';
        Debugger()->info($logTemplateNames);
        $this->templates = $templates;
    }

    private function resolveTemplatesDefaults(): void{
        $this->templatesDefaults = Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS['templates'] . "." . self::TEMPLATE_CONFIG_KEYS["defaults"], []);
        $this->templateResolvingManager->logDefaults($this->templatesDefaults);
    }

    private function resolveTemplatesOverrides(): void{
        $this->templatesOverrides = Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS["templates"] . "." . self::TEMPLATE_CONFIG_KEYS['overrides'], []);
        $this->templateResolvingManager->logOverrides($this->templatesOverrides);
    }

    private function resolveTemplateRequireExistingDirs():void{
        $this->templatesRequireExistingDirs = Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS["templates_require_dirs"], true);
        $logTemplatedRequire= $this->templatesRequireExistingDirs ?
         "Templates require existing directoreis to be created" :
         "Templates dont require existing directoreis to be created"
        ;
        Debugger()->info($logTemplatedRequire);
    }
}