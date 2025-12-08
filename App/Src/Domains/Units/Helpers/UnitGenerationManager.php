<?php

namespace App\Src\Domains\Units\Helpers;

use Illuminate\Filesystem\Filesystem;
use App\Src\Core\Helpers\ReportManager;

// ===============================================
// Class: UnitGenerationManager
// Purpose: Handles the generation of units by determining which directories 
//          a unit should exist in and creating the corresponding folders.
// External dependencies: Debugger() for logging, Filesystem for directory creation
// Functions:
//   - getUnitDirectories(): Determines directories for a unit based on mode and overrides
//   - handleDirectoriesUnitMap(): Helper for 'directories' mode mapping
//   - handleUnitsUnitMap(): Helper for 'units' mode mapping
//   - createUnit(): Creates the physical unit folder on disk
// ===============================================
class UnitGenerationManager
{
    // ===============================================
    // Function: getUnitDirectories
    // Inputs:
    //   - string $unit: name of the unit to generate
    //   - string $mode: mapping mode ('units' or 'directories')
    //   - array $overrides: override mappings for directories/units
    //   - bool $defaults: whether to include all default directories
    //   - array $allDirectories: list of all possible directories
    // Outputs: array of directory names where the unit should be generated
    // Purpose: Determines the set of directories for the unit based on overrides and mode
    // Logic:
    //   - Checks mode and delegates to helper functions for mapping
    //   - Logs intermediate directory decisions
    // Side Effects:
    //   - Logs debug information using Debugger()
    // External Functions/Helpers:
    //   - handleUnitsUnitMap()
    //   - handleDirectoriesUnitMap()
    //   - Debugger()->info()
    // ===============================================
    public function getUnitDirectories(
        string $unit, 
        string $mode, 
        array $overrides, 
        bool $defaults = false, 
        array $allDirectories
    ): array {
        $directories = [];
        if ($mode === 'units') {
            $directories = $this->handleUnitsUnitMap($unit, $overrides, $allDirectories, $defaults);
        }

        if ($mode === 'directories') {
            $directories = $this->handleDirectoriesUnitMap($unit, $overrides, $allDirectories, $defaults);
        }
        
        $logDirectories = count($directories) ?  "'[" . implode(', ', $directories) . "]'" : 'No directories';
        Debugger()->info("Intermediate directories for unit '{$unit}' after applying overrides: '{$unit}': {$logDirectories}");

        return $directories;
    }

    // ===============================================
    // Function: handleDirectoriesUnitMap
    // Inputs:
    //   - string $unit
    //   - array $overrides
    //   - array $allDirectories
    //   - bool $defaults
    // Outputs: array of directories
    // Purpose: Handles mapping when 'directories' mode is used
    // Logic:
    //   - Iterates through overrides
    //   - Adds directories if unit matches override or if override is '*'
    //   - If defaults=true, appends directories not mentioned in overrides
    // Side Effects: Logs debug info
    // External Functions/Helpers: Debugger()->info()
    // ===============================================
    private function handleDirectoriesUnitMap(
        string $unit, 
        array $overrides, 
        array $allDirectories, 
        bool $defaults
    ): array {
        $directories = [];

        foreach ($overrides as $dir => $dirUnits) {
            switch (true) {
                case empty($dirUnits):
                    Debugger()->info("Directory '{$dir}' has no overrides. skipping");
                    break;
                    
                case $dirUnits === ["*"]:
                    Debugger()->info("Directory '{$dir}' applies to all units");
                    $directories[] = $dir;
                    break;

                case !empty($dirUnits) && !in_array($unit, $dirUnits, true):
                    Debugger()->info("Unit '{$unit}' is not in directory '{$dir}' override. skipping");
                    break;

                case !empty($dirUnits) && in_array($unit, $dirUnits, true):
                    $directories[] = $dir;
                    Debugger()->info("Unit '{$unit}' is included in directory '{$dir}' override");
                    break;
            }
        }

        if ($defaults) {
            $overridenDirectories = array_keys($overrides);
            $remaining = array_values(array_filter(
                $allDirectories,
                fn($dir) => !in_array($dir, $overridenDirectories)
            ));
            $directories = array_merge($directories, $remaining);

            Debugger()->info("Default unit creation enabled, Generating '{$unit}' inside all non-overriden directories");
        }

        return $directories;
    }

    // ===============================================
    // Function: handleUnitsUnitMap
    // Inputs:
    //   - string $unit
    //   - array $overrides
    //   - array $allDirectories
    //   - bool $defaults
    // Outputs: array of directories
    // Purpose: Handles mapping when 'units' mode is used
    // Logic:
    //   - Iterates overrides keyed by unit
    //   - Returns all directories if override is '*'
    //   - Adds default directories if defaults=true
    // Side Effects: Logs debug info
    // External Functions/Helpers: Debugger()->info()
    // ===============================================
    private function handleUnitsUnitMap(
        string $unit, 
        array $overrides, 
        array $allDirectories, 
        bool $defaults
    ): array {
        $directories = [];
        
        foreach ($overrides as $u => $dirs) {
            if ($u !== $unit) continue;

            if ($dirs === ['*']) {
                Debugger()->info("'{$unit}' accepts all directories");
                return $allDirectories;
            }
                
            $directories = $dirs;

            $logDirectories = count($directories) ?  "'[" . implode(', ', $directories) . "]'" : 'No directories';
            Debugger()->info("Intermediate directories for unit '{$unit}' after applying overrides: '{$unit}': {$logDirectories}");
        }

        if ($defaults) {
            $overridenDirectories = array_keys($overrides);
            $remaining = array_values(array_filter(
                $allDirectories,
                fn($dir) => !in_array($dir, $overridenDirectories)
            ));
            $directories = array_merge($directories, $remaining);

            Debugger()->info("Default unit creation enabled, Generating '{$unit}' inside all non-overriden directories");
        }

        return $directories;
    }

    // ===============================================
    // Function: createUnit
    // Inputs:
    //   - string $path: path to create the unit folder
    //   - Filesystem $files: instance of Illuminate Filesystem
    //   - string $unit: name of the unit
    // Outputs: void
    // Purpose: Physically creates the folder for a unit if it doesn't already exist
    // Logic:
    //   - Checks if the path exists, logs and returns if it does
    //   - Otherwise, creates the directory recursively and adds a .gitkeep file
    // Side Effects:
    //   - Writes directories and files to disk
    //   - Logs actions using Debugger()
    // External Functions/Helpers:
    //   - Filesystem->exists()
    //   - Filesystem->makeDirectory()
    //   - Filesystem->put()
    //   - Debugger()->info()
    // ===============================================
    public function createUnit(string $path, Filesystem $files, string $unit): void {
        if ($files->exists($path)) {
            Debugger()->info("Unit already exists at '{$path}', skipping creation");
            return;
        }

        Debugger()->info("Creating unit at path: '{$path}'");
        // Reporter()->logCreated('Units', $unit);
    
        $files->makeDirectory($path, 0755, true);
        $files->put("{$path}/.gitkeep", '');
    }
}
