<?php

namespace App\Src\Domains\Templates\Resolvers;

use App\Src\Domains\Templates\DTOs\TemplateContextDTO;
use App\Src\Domains\Templates\Helpers\TemplateResolvingManager;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

// ==========================================================
// Class: TemplateContextResolver
// Purpose: Resolves the context for templates including path, 
//          engine extensions, templates, defaults, overrides,
//          and whether existing directories are required.
// Functions:
//   - resolveTemplatesContext(): returns a TemplateContextDTO with resolved data
//   - resolveTemplatesPath(): resolves the absolute path to templates
//   - resolveTemplateEngineExtensions(): resolves allowed template engine extensions
//   - resolveTemplates(): loads templates from the filesystem
//   - resolveTemplatesDefaults(): loads default template metadata from config
//   - resolveTemplatesOverrides(): loads override template metadata from config
//   - resolveTemplateRequireExistingDirs(): checks if template directories must exist
// ==========================================================
class TemplateContextResolver
{
    // Properties
    private string $templatesPath; // Absolute path to templates folder
    protected array $templateEngineExtensions; // Allowed template engine extensions
    private array $templates; // Mapping of template file names to real paths
    private array $templatesDefaults; // Defaults per template
    private array $templatesOverrides; // Overrides per template
    private bool $templatesRequireExistingDirs; // Whether directories must exist

    // Template configuration keys used to read from mode config
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

    // ==========================================================
    // Function: resolveTemplatesContext
    // Inputs: none
    // Outputs: TemplateContextDTO containing all resolved template data
    // Purpose: Orchestrates resolution of all template-related configuration
    // Logic:
    //   1. Resolves templates path
    //   2. Resolves template engine extensions
    //   3. Resolves templates from filesystem
    //   4. Resolves template defaults and overrides
    //   5. Resolves whether existing directories are required
    // Uses: all private resolve* functions
    // Side Effects: none
    // ==========================================================
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

    // ==========================================================
    // Function: resolveTemplatesPath
    // Inputs: none
    // Outputs: none
    // Purpose: Resolves absolute path for templates from config
    // Logic:
    //   1. Reads path from config
    //   2. Converts to absolute path
    //   3. Logs the resolved path
    // Uses: PathManager, Config(), Debugger()
    // Side Effects: sets $this->templatesPath
    // ==========================================================
    private function resolveTemplatesPath(): void
    {
        $this->templatesPath = $this->pathManager->getAbsolutePath(
            Config()->get("mode_config." . self::TEMPLATE_CONFIG_KEYS["path"]), 
            true
        ); 
        Debugger()->info("Templates path: '{$this->templatesPath}'");
    }

    // ==========================================================
    // Function: resolveTemplateEngineExtensions
    // Inputs: none
    // Outputs: none
    // Purpose: Loads allowed template engine extensions from config
    // Logic:
    //   1. Reads extensions from config
    //   2. Logs available extensions
    // Uses: Config(), Debugger()
    // Side Effects: sets $this->templateEngineExtensions
    // ==========================================================
    private function resolveTemplateEngineExtensions(): void
    {
        $this->templateEngineExtensions = Config()->get(
            "mode_config." . self::TEMPLATE_CONFIG_KEYS["engine_extensions"]
        );

        $logTemplateEngineExtensions = count($this->templateEngineExtensions) 
            ?  "Template engine extensions available: '[" . implode(', ', $this->templateEngineExtensions) . "]'" 
            : 'No template engine extensions available';

        Debugger()->info($logTemplateEngineExtensions);
    }

    // ==========================================================
    // Function: resolveTemplates
    // Inputs: none
    // Outputs: none
    // Purpose: Loads template files from the filesystem and filters by allowed extensions
    // Logic:
    //   1. List all files in $this->templatesPath
    //   2. For each file, check if extension is allowed
    //   3. Log errors for unaccepted extensions
    //   4. Store valid templates in $this->templates
    // Uses: File, Debugger()
    // Side Effects: sets $this->templates
    // ==========================================================
    private function resolveTemplates(): void
    {
        $templates = [];
        $templatesNames = [];
        $files = File::allFiles($this->templatesPath);

        foreach ($files as $file) {
            $fileName = $file->getFilename();
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            if(!in_array($fileExtension, $this->templateEngineExtensions, true)){
                $logTemplateEngineExtensions = count($this->templateEngineExtensions) 
                    ?  "'[" . implode(', ', $this->templateEngineExtensions) . "]'" 
                    : 'No template engines extensions available';

                Debugger()->error("'{$fileExtension}' is not an accepted template engine. Accepted template engine extensions: '{$logTemplateEngineExtensions}'");
                continue;
            } 

            $templatesNames[] = $fileName;
            $templates[$fileName] = $file->getRealPath();
        }

        $logTemplateNames = count($templatesNames) 
            ?  "Provided templates:'[" . implode(', ', $templatesNames) . "]'" 
            : 'No templates were provided';

        Debugger()->info($logTemplateNames);
        $this->templates = $templates;
    }

    // ==========================================================
    // Function: resolveTemplatesDefaults
    // Inputs: none
    // Outputs: none
    // Purpose: Loads template default metadata from config
    // Logic:
    //   1. Reads defaults from config
    //   2. Logs defaults using TemplateResolvingManager
    // Uses: Config(), TemplateResolvingManager
    // Side Effects: sets $this->templatesDefaults
    // ==========================================================
    private function resolveTemplatesDefaults(): void
    {
        $this->templatesDefaults = Config()->get(
            "mode_config." . self::TEMPLATE_CONFIG_KEYS['templates'] . "." . self::TEMPLATE_CONFIG_KEYS["defaults"], 
            []
        );
        $this->templateResolvingManager->logDefaults($this->templatesDefaults);
    }

    // ==========================================================
    // Function: resolveTemplatesOverrides
    // Inputs: none
    // Outputs: none
    // Purpose: Loads template override metadata from config
    // Logic:
    //   1. Reads overrides from config
    //   2. Logs overrides using TemplateResolvingManager
    // Uses: Config(), TemplateResolvingManager
    // Side Effects: sets $this->templatesOverrides
    // ==========================================================
    private function resolveTemplatesOverrides(): void
    {
        $this->templatesOverrides = Config()->get(
            "mode_config." . self::TEMPLATE_CONFIG_KEYS["templates"] . "." . self::TEMPLATE_CONFIG_KEYS['overrides'], 
            []
        );
        $this->templateResolvingManager->logOverrides($this->templatesOverrides);
    }

    // ==========================================================
    // Function: resolveTemplateRequireExistingDirs
    // Inputs: none
    // Outputs: none
    // Purpose: Determines whether template directories must already exist
    // Logic:
    //   1. Reads boolean flag from config
    //   2. Logs the requirement
    // Uses: Config(), Debugger()
    // Side Effects: sets $this->templatesRequireExistingDirs
    // ==========================================================
    private function resolveTemplateRequireExistingDirs(): void
    {
        $this->templatesRequireExistingDirs = Config()->get(
            "mode_config." . self::TEMPLATE_CONFIG_KEYS["templates_require_dirs"], 
            true
        );

        $logTemplatedRequire = $this->templatesRequireExistingDirs 
            ? "Templates require existing directoreis to be created" 
            : "Templates dont require existing directoreis to be created";

        Debugger()->info($logTemplatedRequire);
    }
}