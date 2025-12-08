<?php

namespace App\Src\Domains\Units\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Helpers\UnitGenerationManager;
use Illuminate\Filesystem\Filesystem;
use Log;

class UnitsGenerator
{
    private UnitContextDTO $unitContextDTO;
    private ComponentContextDTO $componentContextDTO;
    private DirectoryContextDTO $directoryContextDTO;
    
    public function __construct(
        private Filesystem $files, 
        private UnitGenerationManager $UnitGenerationManager
    ){}

    private function loadContexts(): void{
        $this->unitContextDTO = ContextBus()->get(UnitContextDTO::class);
        Debugger()->info("Loaded context: 'UnitContextDTO' from the context bus");
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
        $this->directoryContextDTO = ContextBus()->get(DirectoryContextDTO::class);
        Debugger()->info("Loaded context: 'DirectoryContextDTO' from the context bus");
    }

    public function generateUnits(): void
    {
        $this->loadContexts();
        if (empty($this->unitContextDTO->units)) {
            Debugger()->info("No units specified, skipping unit generation");
            return;
        }

        collect($this->unitContextDTO->units)
            ->each(function ($unit) {
                $this->generateUnit($unit);
            });
    }

    private function generateUnit(string $unit): void
    {
        Debugger()->info("Generating unit: '{$unit}'");

        $mode = $this->unitContextDTO->unitsMapMode;
        $overrides = $this->unitContextDTO->unitsMapOverrides;
        $defaults = $this->unitContextDTO->unitsMapDefaults;
        $allDirectories =$this->directoryContextDTO->directories;

        $directories = $this->UnitGenerationManager->getUnitDirectories($unit, $mode, $overrides, $defaults, $allDirectories);
        $unit = NamingConventions()->apply("units", $unit);
        foreach ($directories as $directory) {
            $path = "{$this->componentContextDTO->componentPath}/{$directory}/{$unit}";
            $this->UnitGenerationManager->createUnit($path, $this->files, $unit);
        }   
    }
}