<?php

namespace App\Src\Domains\Components\Helpers;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class ComponentGenerationManager
{
    public function __construct(private Filesystem $files){}

    public function componentExists(string $path): void{
        if ($this->files->exists( $path)) {
            throw new RuntimeException("Comopnent: '{$path}' already exists");
        }
    }
}