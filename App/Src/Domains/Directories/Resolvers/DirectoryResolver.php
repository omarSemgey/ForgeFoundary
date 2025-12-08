<?php

namespace App\Src\Domains\Directories\Resolvers;

use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;

class DirectoryResolver
{
    private array $directories;
    private const DIRECTORY_CONFIG_KEYS = [
        "directories" => "directories",
    ];

    public function resolveDirectoriesContext(): DirectoryContextDTO
    {
        $this->resolvedirectories();
        return new DirectoryContextDTO($this->directories);
    }
    
    private function resolvedirectories(): void
    {
        $this->directories = Config()->get("mode_config." . self::DIRECTORY_CONFIG_KEYS['directories']);
    
        $logDirectories = count($this->directories) ?  "Provided directories: '[" . implode(', ', $this->directories) . "]'" : 'No directories were provided';
        Debugger()->info($logDirectories);
    }
}