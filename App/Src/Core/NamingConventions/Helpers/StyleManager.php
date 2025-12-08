<?php

namespace App\Src\Core\NamingConventions\Helpers;
use Illuminate\Support\Str;


class StyleManager{
    private const STYLES = [
        "camel_case"        => "camelCase",
        "pascal_case"       => "pascalCase",
        "snake_case"        => "snakeCase",
        "kebab_case"        => "kebabCase",
        "upper_snake_case"  => "upperSnakeCase",
        "dot_case"          => "dotCase",
        "studly_case"       => "studlyCase",
        "title_case"        => "titleCase",
        "sentence_case"     => "sentenceCase",
        "screaming_kebab_case" => "screamingKebabCase",
        "slash_case"           => "slashCase",
        "backslash_case"       => "backslashCase",
        "dot_kebab_case"       => "dotKebabCase",
        "flat_case"            => "flatCase",
        "train_case"           => "trainCase",
    ];
    
    public function applyStyle($style, $value):string{
        if (!isset(self::STYLES[$style])) {
            Debugger()->error("Unsupported naming convention: '{$style}'");
            return $value;
        }

        $value = trim($value);
        $value = str_replace(['-', '_'], ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        $styleHandler = self::STYLES[$style];

        Debugger()->info("Applying naming convention: '{$style}' to value: '{$value}'");
        
        return $this->$styleHandler($value);
    }

    // ---------------------------------------------------------
    // Naming convention handlers
    // ---------------------------------------------------------

    private function camelCase(string $value): string
    {
        return Str::camel($value);
    }

    private function pascalCase(string $value): string
    {
        return Str::studly($value);
    }

    private function snakeCase(string $value): string
    {
        return Str::snake($value);
    }

    private function kebabCase(string $value): string
    {
        return Str::kebab($value);
    }

    private function upperSnakeCase(string $value): string
    {
        return Str::upper(Str::snake($value));
    }

    private function dotCase(string $value): string
    {
        return str_replace('_', '.', Str::snake($value));
    }

    private function studlyCase(string $value): string
    {
        return Str::studly($value);
    }

    private function titleCase(string $value): string
    {
        return Str::title(str_replace(['_', '-'], ' ', $value));
    }

    private function sentenceCase(string $value): string
    {
        $value = str_replace(['_', '-'], ' ', $value);
        $value = strtolower($value);
        return ucfirst($value);
    }

    private function screamingKebabCase(string $value): string
    {
        return strtoupper(Str::kebab($value));
    }

    private function slashCase(string $value): string
    {
        return str_replace(['_', '-'], '/', Str::kebab($value));
    }

    private function backslashCase(string $value): string
    {
        return str_replace('/', '\\', $this->slashCase($value));
    }

    private function dotKebabCase(string $value): string
    {
        return str_replace('/', '.', $this->slashCase($value));
    }

    private function flatCase(string $value): string
    {
        return str_replace(['_', '-', ' '], '', strtolower($value));
    }

    private function trainCase(string $value): string
    {
        return str_replace('-', '-', Str::title(Str::kebab($value)));
    }
}