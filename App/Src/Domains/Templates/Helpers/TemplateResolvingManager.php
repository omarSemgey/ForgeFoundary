<?php

namespace App\Src\Domains\Templates\Helpers;

use Log;

class TemplateResolvingManager
{
    public function logOverrides(array $overrides, int $indent = 4): void
    {
        if (empty($overrides)) {
            Debugger()->info("No overrides defined");
            return;
        }

        Debugger()->info("Resolved Template overrides:");

        foreach ($overrides as $template => $templateOverrides) {
            $this->logLine("- Template: '{$template}'", $indent - 2);

            $this->logArrayRecursively( $templateOverrides, $indent);
        }
    }

    public function logDefaults(array $defaults, int $indent = 2): void{
        if (empty($defaults)) {
            Debugger()->info("No defaults defined");
            return;
        }

        Debugger()->info("Resolved defaults:");

        $this->logArrayRecursively($defaults, $indent);
    }

    private function logArrayRecursively(array|object $data, int $indent): void
    {
        $space = str_repeat(' ', $indent);

        foreach ($data as $key => $value) {

            // Nested structures
            if (is_array($value) || is_object($value)) {
                Debugger()->raw("{$space}• {$key}:");
                $this->logArrayRecursively((array)$value, $indent + 4);
                continue;
            }

            // Scalars
            $val = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
            Debugger()->raw("{$space}• {$key}: {$val}");
        }
    }

    private function logLine(string $message, int $indent): void
    {
        Debugger()->raw(str_repeat(' ', $indent) . $message);
    }

    public function logData(string $templateName, array $data, string $dataType): void
    {
        Debugger()->info("Resolved {$dataType} data for template '{$templateName}':");

        foreach ($data as $key => $value) {

            if ($value === null) {
                if ($key === 'filePlaceholders') {
                    Debugger()->warning("  - {$key}: No placeholders");
                } else {
                    Debugger()->error("  - {$key}: MISSING");
                }
                continue;
            }

            // If it's an array, print recursively
            if (is_array($value)) {
                Debugger()->raw("  - {$key}:");
                $this->logArrayIndented($value, 6);
                continue;
            }

            // Simple scalar
            Debugger()->raw("  - {$key}: {$value}");
        }
    }

    private function logArrayIndented(array $arr, int $indent): void
    {
        $space = str_repeat(' ', $indent);

        foreach ($arr as $k => $v) {

            if (is_array($v)) {
                Debugger()->raw("{$space}• {$k}:");
                $this->logArrayIndented($v, $indent + 4);
                continue;
            }

            Debugger()->raw("{$space}• {$k}: {$v}");
        }
    }
}
