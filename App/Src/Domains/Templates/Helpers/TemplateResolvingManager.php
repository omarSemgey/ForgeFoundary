<?php

namespace App\Src\Domains\Templates\Helpers;

use Log;

// ===============================================
// Class: TemplateResolvingManager
// Purpose: Handles logging and visualization of template data, including defaults
//          and overrides, in a readable format using the Debugger.
// Functions:
//   - logOverrides(): logs template-specific overrides recursively
//   - logDefaults(): logs default template data recursively
//   - logData(): logs resolved template data of a specific template
//   - logArrayRecursively(): helper to recursively log arrays or objects
//   - logArrayIndented(): helper to recursively log arrays with indentation
//   - logLine(): logs a single line with indentation
// ===============================================
class TemplateResolvingManager
{
    // ===============================================
    // Function: logOverrides
    // Inputs:
    //   - $overrides: array of template overrides
    //   - $indent: int, number of spaces to indent logs
    // Outputs: none
    // Purpose: Logs all template overrides in a structured format
    // Logic:
    //   1. Checks if $overrides is empty; if so, logs info and returns
    //   2. Logs "Resolved Template overrides"
    //   3. Iterates each template override, prints name, and calls logArrayRecursively
    // Side Effects: outputs to Debugger
    // Uses: logArrayRecursively(), logLine(), Debugger()
    // ===============================================
    public function logOverrides(array $overrides, int $indent = 4): void
    {
        if (empty($overrides)) {
            Debugger()->info("No overrides defined");
            return;
        }

        Debugger()->info("Resolved Template overrides:");

        foreach ($overrides as $template => $templateOverrides) {
            $this->logLine("- Template: '{$template}'", $indent - 2);
            $this->logArrayRecursively($templateOverrides, $indent);
        }
    }

    // ===============================================
    // Function: logDefaults
    // Inputs:
    //   - $defaults: array of default template data
    //   - $indent: int, indentation level
    // Outputs: none
    // Purpose: Logs default template data recursively
    // Logic:
    //   1. Checks if $defaults is empty, logs info if so
    //   2. Calls logArrayRecursively to log the data
    // Side Effects: outputs to Debugger
    // Uses: logArrayRecursively(), Debugger()
    // ===============================================
    public function logDefaults(array $defaults, int $indent = 2): void
    {
        if (empty($defaults)) {
            Debugger()->info("No defaults defined");
            return;
        }

        Debugger()->info("Resolved defaults:");
        $this->logArrayRecursively($defaults, $indent);
    }

    // ===============================================
    // Function: logArrayRecursively
    // Inputs:
    //   - $data: array or object to log recursively
    //   - $indent: indentation level
    // Outputs: none
    // Purpose: Recursively logs nested arrays or objects
    // Logic:
    //   1. For each key-value pair:
    //       - If value is array/object, call itself recursively
    //       - Otherwise, print scalar values
    // Side Effects: outputs to Debugger
    // Uses: Debugger()
    // ===============================================
    private function logArrayRecursively(array|object $data, int $indent): void
    {
        $space = str_repeat(' ', $indent);

        foreach ($data as $key => $value) {

            if (is_array($value) || is_object($value)) {
                Debugger()->raw("{$space}• {$key}:");
                $this->logArrayRecursively((array)$value, $indent + 4);
                continue;
            }

            $val = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
            Debugger()->raw("{$space}• {$key}: {$val}");
        }
    }

    // ===============================================
    // Function: logLine
    // Inputs:
    //   - $message: string to log
    //   - $indent: int, number of spaces to indent
    // Outputs: none
    // Purpose: Logs a single line with indentation
    // Logic: prepends spaces and logs using Debugger()->raw()
    // Side Effects: outputs to Debugger
    // Uses: Debugger()
    // ===============================================
    private function logLine(string $message, int $indent): void
    {
        Debugger()->raw(str_repeat(' ', $indent) . $message);
    }

    // ===============================================
    // Function: logData
    // Inputs:
    //   - $templateName: string, template name
    //   - $data: array of resolved template data
    //   - $dataType: string, type of data (e.g., "placeholders", "metadata")
    // Outputs: none
    // Purpose: Logs all resolved data for a specific template
    // Logic:
    //   1. Logs the header info
    //   2. Iterates over data keys:
    //       - If value is null, logs warning or error
    //       - If value is array, calls logArrayIndented
    //       - Otherwise, prints scalar values
    // Side Effects: outputs to Debugger
    // Uses: logArrayIndented(), Debugger()
    // ===============================================
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

            if (is_array($value)) {
                Debugger()->raw("  - {$key}:");
                $this->logArrayIndented($value, 6);
                continue;
            }

            Debugger()->raw("  - {$key}: {$value}");
        }
    }

    // ===============================================
    // Function: logArrayIndented
    // Inputs:
    //   - $arr: array to log recursively
    //   - $indent: int, indentation level
    // Outputs: none
    // Purpose: Recursively logs nested arrays with custom indentation
    // Logic:
    //   1. Iterates array:
    //       - If value is array, call itself recursively
    //       - Otherwise, prints scalar values
    // Side Effects: outputs to Debugger
    // Uses: Debugger()
    // ===============================================
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
