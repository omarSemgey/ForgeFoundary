<?php

namespace App\Src\Core\Helpers;

class PathManager
{
    private const COMMANDS_PREFIX = "Commands";
    public function getAbsolutePath(string $path, bool $toolRelative = false): string
    {
        if ($this->isAbsolutePath($path)) {
            return rtrim($this->normalizeSlashes($path), '/');
        }

        $base = $toolRelative 
            ? $this->getToolBasePath() 
            : $this->getProjectBasePath();

        $full = $base . '/' . $path;

        return rtrim($this->normalizeSlashes($full), '/');
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || preg_match('/^[A-Za-z]:\\\\/', $path);
    }

    public function normalizeSlashes(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public function resolveToolPath(string $commandDir):string{
        $CommandsPrefix = self::COMMANDS_PREFIX;
        return preg_replace("/[\/\\\\]{$CommandsPrefix}$/", '', $commandDir);
    }

    private function getProjectBasePath(): string
    {
       return getcwd();
    }

    private function getToolBasePath(): string
    {
        return TOOL_BASE_PATH;
    }
}