<?php

namespace App\Src\Domains\Units\Generators;

use App\Src\Domains\Components\DTOs\ComponentContextDTO;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Helpers\UnitGenerationManager;
use Illuminate\Filesystem\Filesystem;
use Log;

// ===============================================
// Class: UnitsGenerator
// Purpose: Handles the generation of "units" (e.g., Crud, Auth, Logic) in the project 
//          based on the current contexts for Units, Components, and Directories.
// Functions:
//   - __construct(Filesystem $files, UnitGenerationManager $UnitGenerationManager)
//       Constructor initializes dependencies for file operations and unit generation.
//   - generateUnits(): void
//       Main entry to generate all units specified in UnitContextDTO.
//   - generateUnit(string $unit): void
//       Generates a single unit into its mapped directories according to conventions and mappings.
//   - loadContexts(): void
//       Loads required DTOs (Unit, Component, Directory) from the ContextBus.
// ===============================================
class UnitsGenerator
{
    private UnitContextDTO $unitContextDTO;
    private ComponentContextDTO $componentContextDTO;
    private DirectoryContextDTO $directoryContextDTO;
    
    public function __construct(
        private Filesystem $files, 
        private UnitGenerationManager $UnitGenerationManager
    ){}

    // ===============================================
    // Function: loadContexts
    // Inputs: None
    // Outputs: None
    // Purpose: Loads DTO contexts required for unit generation
    // Logic:
    //   - Fetches UnitContextDTO, ComponentContextDTO, DirectoryContextDTO from ContextBus
    //   - Logs info about loaded contexts using Debugger
    // Side Effects: Initializes private properties for later use
    // Uses: ContextBus(), Debugger()
    // ===============================================
    private function loadContexts(): void{
        $this->unitContextDTO = ContextBus()->get(UnitContextDTO::class);
        Debugger()->info("Loaded context: 'UnitContextDTO' from the context bus");
        $this->componentContextDTO = ContextBus()->get(ComponentContextDTO::class);
        Debugger()->info("Loaded context: 'ComponentContextDTO' from the context bus");
        $this->directoryContextDTO = ContextBus()->get(DirectoryContextDTO::class);
        Debugger()->info("Loaded context: 'DirectoryContextDTO' from the context bus");
    }

    // ===============================================
    // Function: generateUnits
    // Inputs: None
    // Outputs: None
    // Purpose: Generates all units specified in UnitContextDTO
    // Logic:
    //   - Calls loadContexts() to initialize required DTOs
    //   - Checks if units array is empty, logs and returns if so
    //   - Iterates over each unit and calls generateUnit() for each
    // Side Effects: May create directories/files in the filesystem
    // Uses: loadContexts(), generateUnit(), Debugger(), collect()
    // ===============================================
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

    // ===============================================
    // Function: generateUnit
    // Inputs:
    //   - string $unit: the name of the unit to generate
    // Outputs: None
    // Purpose: Generates a single unit in all mapped directories according to project conventions
    // Logic:
    //   - Logs the unit being generated
    //   - Fetches mode, overrides, defaults, and all directories from DTOs
    //   - Gets directories for the unit via UnitGenerationManager
    //   - Applies naming conventions to the unit name
    //   - Iterates over directories and calls UnitGenerationManager->createUnit() for each
    // Side Effects: Creates directories/files in the filesystem
    // Uses: Debugger(), NamingConventions(), UnitGenerationManager->getUnitDirectories(), 
    //       UnitGenerationManager->createUnit()
    // ===============================================
    private function generateUnit(string $unit): void
    {
        Debugger()->info("Generating unit: '{$unit}'");

        $mode = $this->unitContextDTO->unitsMapMode;
        $overrides = $this->unitContextDTO->unitsMapOverrides;
        $defaults = $this->unitContextDTO->unitsMapDefaults;
        $allDirectories =$this->directoryContextDTO->directories;

        $directories = $this->UnitGenerationManager->getUnitDirectories($unit, $mode, $overrides, $defaults, $allDirectories);
        $unit = NamingConventions()->apply("units", $unit, $unit);
        foreach ($directories as $directory) {
            $path = "{$this->componentContextDTO->componentPath}/{$directory}/{$unit}";
            $this->UnitGenerationManager->createUnit($path, $this->files, $unit);
        }   
    }
}
