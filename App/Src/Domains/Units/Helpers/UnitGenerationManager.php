<?php

namespace App\Src\Domains\Units\Helpers;

use Illuminate\Filesystem\Filesystem;
use App\Src\Core\Helpers\ReportManager;

class UnitGenerationManager
{
    public function getUnitDirectories(string $unit, string $mode, array $overrides, bool $defaults = false, array $allDirectories): array
    {
        $directories = [];
        if ($mode === 'units') {
            $directories =  $this->handleUnitsUnitMap($unit, $overrides, $allDirectories, $defaults);
        }

        if ($mode === 'directories') {
            $directories =  $this->handleDirectoriesUnitMap($unit, $overrides, $allDirectories, $defaults);
        }
        
        $logDirectories = count($directories) ?  "'[" . implode(', ', $directories) . "]'" : 'No directories';
        Debugger()->info("Intermediate directories for unit '{$unit}' after applying overrides: '{$unit}': {$logDirectories}");

        return $directories;
    }

    private function handleDirectoriesUnitMap(string $unit, array $overrides, array $allDirectories, bool $defaults):array{
            $directories = [];

            foreach ($overrides as $dir => $dirUnits) {
                switch (true) {
                    case empty($dirUnits):
                        Debugger()->info("Directory '{$dir}' has no overrides. skipping");
                        break;
                        
                        case $dirUnits === ["*"]:
                        Debugger()->info("Directory '{$dir}' applies to all units");
                        $directories [] = $dir;
                        break;

                    case !empty($dirUnits) && !in_array($unit, $dirUnits, true):
                        Debugger()->info("Unit '{$unit}' is not in directory '{$dir}' override. skipping");
                        break;

                    case !empty($dirUnits) && in_array($unit, $dirUnits, true):
                        $directories [] = $dir;
                        Debugger()->info("Unit '{$unit}' is included in directory '{$dir}' override");
                        break;
                }
            }

            if($defaults){
                $overridenDirectories = array_keys($overrides);

                $remaining = array_values(array_filter(
                    $allDirectories,
                    fn($dir) => !in_array($dir, $overridenDirectories)
                ));

                $directories = array_merge($directories, $remaining);


                Debugger()->info(message: "Default unit creation enabled, Generating '{$unit}' inside all non-overriden directories");
            }

            return $directories;
    }

    private function handleUnitsUnitMap(string $unit, array $overrides, array $allDirectories, bool $defaults):array{
        $directories = [];
        
        foreach ($overrides as $u => $dirs) {
            if ($u !== $unit) continue;

            if ($dirs === ['*']) {
                Debugger()->info(message: "'{$unit}' accepts all directories");
                return $allDirectories;
            }
                
            $directories = $dirs;

            $logDirectories = count($directories) ?  "'[" . implode(', ', $directories) . "]'" : 'No directories';
            Debugger()->info("Intermediate directories for unit '{$unit}' after applying overrides: '{$unit}': {$logDirectories}");
        }

        if($defaults){
            $overridenDirectories = array_keys($overrides);

            $remaining = array_values(array_filter(
                $allDirectories,
                fn($dir) => !in_array($dir, $overridenDirectories)
            ));

            $directories = array_merge($directories, $remaining);


            Debugger()->info(message: "Default unit creation enabled, Generating '{$unit}' inside all non-overriden directories");
        }

        return $directories;
    }

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