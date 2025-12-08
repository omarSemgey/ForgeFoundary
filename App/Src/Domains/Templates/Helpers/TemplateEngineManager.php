<?php

namespace App\Src\Domains\Templates\Helpers;

class TemplateEngineManager
{
    private const ENGINES = [
        "mustache" => "MustacheEngineHandler",
        "twig" => "TwigEngineHandler",
        "blade.php" => "BladeEngineHandler"
        # engine extension => engine handler function
    ];

    public function renderTemplate(array $placeholders, string $contents, string $engine): string|null{
        if($placeholders === null){
            return $contents;
        }

        if (!isset(self::ENGINES[$engine])) {
            Debugger()->error("Unsupported template engine: '{$engine}'");
            return null;
        }

        $engineHadnler = self::ENGINES[$engine];
        
        return $this->$engineHadnler($placeholders, $contents);
    }

    private function MustacheEngineHandler(array $placeholders, string $contents): string{
        $mustaceEngineInstance = new \Mustache\Engine();
        return $mustaceEngineInstance->render($contents, $placeholders);
    }

    private function TwigEngineHandler(array $placeholders, string $contents): string{
        $loader = new \Twig\Loader\ArrayLoader([
            "template" => $contents,
        ]);
        $twig = new \Twig\Environment($loader);
        return $twig->render("template", $placeholders);
    }

    private function BladeEngineHandler(array $placeholders, string $contents): string {
        $tmpFile = sys_get_temp_dir() . "/template.blade.php";
        file_put_contents($tmpFile, $contents);
        $blade = new \eftec\bladeone\BladeOne(sys_get_temp_dir(), sys_get_temp_dir());
        return $blade->run("template", $placeholders);
    }
}