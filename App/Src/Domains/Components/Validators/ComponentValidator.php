<?php

namespace App\Console\Commands\Traits\Validates;

trait ComponentValidator
{

    protected function domainExists(): void
    {
        if ($this->files->exists($this->componentPath)) {
            throw new \RuntimeException("Domain '{$this->componentName}' already exists");
        }
    }
}
