<?php

namespace App\Src\Domains\Templates\Helpers;

// ===============================================
// Class: TemplateEngineManager
// Purpose: Handles rendering of templates using different engines (Mustache, Twig, Blade).
// Functions:
//   - renderTemplate(): main interface to render templates using the selected engine
//   - MustacheEngineHandler(): renders template using Mustache engine
//   - TwigEngineHandler(): renders template using Twig engine
//   - BladeEngineHandler(): renders template using BladeOne engine
// ===============================================
class TemplateEngineManager
{
    // List of supported engines mapped to their handler methods
    private const ENGINES = [
        "mustache" => "MustacheEngineHandler",
        "twig" => "TwigEngineHandler",
        "blade.php" => "BladeEngineHandler"
    ];

    // ===============================================
    // Function: renderTemplate
    // Inputs:
    //   - $placeholders: associative array of placeholders to substitute in template
    //   - $contents: string, the raw template content
    //   - $engine: string, the template engine to use ("mustache", "twig", "blade.php")
    // Outputs: string|null, rendered template or null if error occurs
    // Purpose: Entry point for rendering a template with the specified engine
    // Logic:
    //   1. If placeholders are null, return raw content
    //   2. Validate that engine is supported
    //   3. Call the corresponding engine handler method dynamically
    // Side Effects: may log errors via Debugger
    // Uses: MustacheEngineHandler, TwigEngineHandler, BladeEngineHandler, Debugger
    // ===============================================
    public function renderTemplate(array $placeholders, string $contents, string $engine): string|null
    {
        if($placeholders === null){
            return $contents;
        }

        if (!isset(self::ENGINES[$engine])) {
            Debugger()->error("Unsupported template engine: '{$engine}'");
            return null;
        }

        $engineHandler = self::ENGINES[$engine];

        return $this->$engineHandler($placeholders, $contents);
    }

    // ===============================================
    // Function: MustacheEngineHandler
    // Inputs: 
    //   - $placeholders: array of values to inject
    //   - $contents: template string
    // Outputs: string, rendered template
    // Purpose: Handles rendering using Mustache engine
    // Logic:
    //   1. Instantiates Mustache engine
    //   2. Renders template with placeholders
    // Side Effects: none
    // Uses: \Mustache\Engine
    // ===============================================
    private function MustacheEngineHandler(array $placeholders, string $contents): string
    {
        $mustacheEngineInstance = new \Mustache\Engine();
        return $mustacheEngineInstance->render($contents, $placeholders);
    }

    // ===============================================
    // Function: TwigEngineHandler
    // Inputs: 
    //   - $placeholders: array of values to inject
    //   - $contents: template string
    // Outputs: string, rendered template
    // Purpose: Handles rendering using Twig engine
    // Logic:
    //   1. Creates ArrayLoader with the template content
    //   2. Creates Twig Environment
    //   3. Renders template with placeholders
    // Side Effects: none
    // Uses: \Twig\Loader\ArrayLoader, \Twig\Environment
    // ===============================================
    private function TwigEngineHandler(array $placeholders, string $contents): string
    {
        $loader = new \Twig\Loader\ArrayLoader([
            "template" => $contents,
        ]);
        $twig = new \Twig\Environment($loader);
        return $twig->render("template", $placeholders);
    }

    // ===============================================
    // Function: BladeEngineHandler
    // Inputs: 
    //   - $placeholders: array of values to inject
    //   - $contents: template string
    // Outputs: string, rendered template
    // Purpose: Handles rendering using BladeOne engine
    // Logic:
    //   1. Writes template contents to a temporary file
    //   2. Creates BladeOne instance pointing to temp directory
    //   3. Runs template with placeholders
    // Side Effects: writes temporary file in sys_get_temp_dir()
    // Uses: \eftec\bladeone\BladeOne
    // ===============================================
    private function BladeEngineHandler(array $placeholders, string $contents): string
    {
        $tmpFile = sys_get_temp_dir() . "/template.blade.php";
        file_put_contents($tmpFile, $contents);
        $blade = new \eftec\bladeone\BladeOne(sys_get_temp_dir(), sys_get_temp_dir());
        return $blade->run("template", $placeholders);
    }
}
