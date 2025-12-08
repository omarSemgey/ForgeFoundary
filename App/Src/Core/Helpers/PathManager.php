<?php

namespace App\Src\Core\Helpers;

// ===============================================
// Class: PathManager
// Purpose: Central utility for handling paths within ForgeFoundary.
//          Provides absolute path resolution, normalization of slashes across OSes,
//          and specific logic for resolving tool vs project base paths.
// Functions:
//   - getAbsolutePath(): Returns absolute path from a relative or absolute input
//   - isAbsolutePath(): Determines if a path is already absolute
//   - normalizeSlashes(): Converts all slashes to the OS-specific DIRECTORY_SEPARATOR
//   - resolveToolPath(): Resolves the base tool directory from a command directory
//   - getProjectBasePath(): Returns current working directory
//   - getToolBasePath(): Returns tool's root directory (TOOL_BASE_PATH constant)
// ===============================================
class PathManager
{
    private const COMMANDS_PREFIX = "Commands";

    // ===============================================
    // Function: getAbsolutePath
    // Inputs:
    //   - string $path: Path to resolve
    //   - bool $toolRelative: Whether to resolve relative to the tool's base path or project cwd
    // Outputs: string - normalized absolute path
    // Purpose: Returns an absolute path for a given relative or absolute input path
    // Logic Walkthrough:
    //   1. If path is already absolute, normalize slashes and return
    //   2. Determine base path depending on $toolRelative (tool base or project base)
    //   3. Concatenate base path with relative path
    //   4. Normalize slashes and trim trailing slash
    // External Functions/Helpers Used:
    //   - $this->isAbsolutePath()
    //   - $this->normalizeSlashes()
    //   - $this->getToolBasePath()
    //   - $this->getProjectBasePath()
    // Side Effects: None
    // ===============================================
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

    // ===============================================
    // Function: isAbsolutePath
    // Inputs: string $path
    // Outputs: bool - true if path is absolute, false otherwise
    // Purpose: Detects if the input path is already absolute
    // Logic Walkthrough:
    //   1. Checks for Unix absolute path starting with '/'
    //   2. Checks for Windows absolute path pattern (e.g., C:\)
    // External Functions/Helpers Used: none
    // Side Effects: None
    // ===============================================
    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || preg_match('/^[A-Za-z]:\\\\/', $path);
    }

    // ===============================================
    // Function: normalizeSlashes
    // Inputs: string $path
    // Outputs: string - path with slashes normalized to OS DIRECTORY_SEPARATOR
    // Purpose: Ensures consistent path formatting across operating systems
    // Logic Walkthrough:
    //   1. Replaces both forward and backward slashes with DIRECTORY_SEPARATOR
    // External Functions/Helpers Used: none
    // Side Effects: None
    // ===============================================
    public function normalizeSlashes(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    // ===============================================
    // Function: resolveToolPath
    // Inputs: string $commandDir - path ending in Commands
    // Outputs: string - tool's base path (removes trailing "Commands" segment)
    // Purpose: Extracts the root tool path from a Commands directory path
    // Logic Walkthrough:
    //   1. Uses a regex to remove trailing /Commands or \Commands from $commandDir
    // External Functions/Helpers Used: none
    // Side Effects: None
    // ===============================================
    public function resolveToolPath(string $commandDir):string{
        $CommandsPrefix = self::COMMANDS_PREFIX;
        return preg_replace("/[\/\\\\]{$CommandsPrefix}$/", '', $commandDir);
    }

    // ===============================================
    // Function: getProjectBasePath
    // Inputs: none
    // Outputs: string - current working directory
    // Purpose: Returns the base path of the project (where the command was executed)
    // Logic Walkthrough: Simply calls getcwd()
    // External Functions/Helpers Used: getcwd()
    // Side Effects: None
    // ===============================================
    private function getProjectBasePath(): string
    {
       return getcwd();
    }

    // ===============================================
    // Function: getToolBasePath
    // Inputs: none
    // Outputs: string - absolute path to the tool root
    // Purpose: Returns the tool's root directory defined by TOOL_BASE_PATH constant
    // Logic Walkthrough: Simply returns TOOL_BASE_PATH
    // External Functions/Helpers Used: TOOL_BASE_PATH
    // Side Effects: None
    // ===============================================
    private function getToolBasePath(): string
    {
        return TOOL_BASE_PATH;
    }
}
