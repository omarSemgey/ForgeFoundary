<?php

namespace App\Src\Domains\Units\Resolvers;

use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Helpers\UnitResolvingManager;
use Log;

class UnitResolver
{
    private array $units;
    private string $unitsMapMode;
    private array $unitsMapOverrides;
    private bool $unitsMapDefaults;
    private const UNTIT_CONFIG_KEYS = [
        "units" => "units",
        "map" => "units_map",
        "mode" => "mode",
        "defaults" => "units_created_by_default",
        "overrides" => "overrides",
    ];

    public function __construct(private UnitResolvingManager $unitResolvingManager){}

    public function resolveUnitsContext(): UnitContextDTO
     {
        $this->resolveUnits();
        $this->resolveUnitsMapMode();
        $this->resolveUnitsMapDefaults();
        $this->resolveUnitsMapOverrides();

        return new UnitContextDTO(
            $this->units,
            $this->unitsMapMode,
            $this->unitsMapDefaults,
            $this->unitsMapOverrides,
        );
     }

    private function resolveUnits(): void
    {
        $this->units = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['units'], []);

        $logUnits = count($this->units) ? "Provided units: '[" . implode(', ', $this->units) . "]'" : 'No units provided';
        Debugger()->info($logUnits);
    }

    private function resolveUnitsMapMode(): void{
        $this->unitsMapMode = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . self::UNTIT_CONFIG_KEYS['mode']);

        Debugger()->info("Units map mode: '{$this->unitsMapMode}'");
    }

    private function resolveUnitsMapDefaults(): void{
        $defaults =  Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . $this->unitsMapMode . "." . self::UNTIT_CONFIG_KEYS['defaults']);
        
        if(!$defaults) {
            Debugger()->warning("Units map defaults not provided; defaulting to false");
            $defaults = false;
        }

        $this->unitsMapDefaults = $defaults;
        
        $unitsCreatedByDefault = $this->unitsMapDefaults
        ? "Units will be created by default."
        : "Units will not be created by default.";

        Debugger()->info($unitsCreatedByDefault);
    }

    private function resolveUnitsMapOverrides(): void{
        $this->unitsMapOverrides = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . $this->unitsMapMode . "." . self::UNTIT_CONFIG_KEYS['overrides'], []);

        $logContextLabel = $this->unitsMapMode === 'units' ? 'Unit' : 'Directory';
        $logTargetLabel  = $this->unitsMapMode === 'units' ? 'Directories' : 'Units';

        $this->unitResolvingManager->logOverrides($logContextLabel, $logTargetLabel, $this->unitsMapOverrides);
    }
}