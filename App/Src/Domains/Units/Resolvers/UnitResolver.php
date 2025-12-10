<?php

namespace App\Src\Domains\Units\Resolvers;

use App\Src\Domains\Units\DTOs\UnitContextDTO;
use App\Src\Domains\Units\Helpers\UnitResolvingManager;
use Log;

// ===============================================
// Class: UnitResolver
// Purpose: Resolves unit configuration for a mode. Fetches the list of units, 
//          determines the mapping mode, default creation behavior, and any overrides.
//          This class produces a UnitContextDTO encapsulating all resolved unit settings.
// Functions:
//   - __construct(UnitResolvingManager $unitResolvingManager): constructor, injects manager
//   - resolveUnitsContext(): returns UnitContextDTO with resolved units context
//   - resolveUnits(): fetches units from config
//   - resolveUnitsMapMode(): fetches units mapping mode from config
//   - resolveUnitsMapDefaults(): determines if units should be created by default
//   - resolveUnitsMapOverrides(): fetches mapping overrides from config and logs them
// ===============================================
class UnitResolver
{
    private array $units;                // List of units configured for this mode
    private string $unitsMapMode;        // Mode of mapping (directories -> units or units -> directories)
    private array $unitsMapOverrides;    // Overrides for the mapping (custom behavior)
    private bool $unitsMapDefaults;      // Whether units should be created by default
    private const UNTIT_CONFIG_KEYS = [  // Keys used for resolving configuration
        "units" => "units",
        "map" => "units_map",
        "mode" => "mode",
        "defaults" => "units_created_by_default",
        "overrides" => "overrides",
    ];

    public function __construct(private UnitResolvingManager $unitResolvingManager){}

    // ===============================================
    // Function: resolveUnitsContext
    // Inputs: none
    // Outputs: UnitContextDTO instance
    // Purpose: Orchestrates the resolution of units configuration
    // Logic Walkthrough: Calls internal resolve functions in order to populate all fields, 
    //                    then constructs a UnitContextDTO
    // Side Effects: None
    // Uses: resolveUnits(), resolveUnitsMapMode(), resolveUnitsMapDefaults(), resolveUnitsMapOverrides()
    // ===============================================
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

    // ===============================================
    // Function: resolveUnits
    // Inputs: none
    // Outputs: none (sets $this->units)
    // Purpose: Retrieves the units defined in the mode configuration
    // Logic Walkthrough: Fetches units array from Config and logs them via Debugger
    // Side Effects: Populates $this->units
    // Uses: Config(), Debugger()
    // ===============================================
    private function resolveUnits(): void
    {
        $this->units = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['units'], []);

        $logUnits = count($this->units) ? "Provided units: '[" . implode(', ', $this->units) . "]'" : 'No units provided';
        Debugger()->info($logUnits);
    }

    // ===============================================
    // Function: resolveUnitsMapMode
    // Inputs: none
    // Outputs: none (sets $this->unitsMapMode)
    // Purpose: Determines the units mapping mode (directories->units or units->directories)
    // Logic Walkthrough: Reads mapping mode from Config and logs it
    // Side Effects: Populates $this->unitsMapMode
    // Uses: Config(), Debugger()
    // ===============================================
    private function resolveUnitsMapMode(): void{
        $this->unitsMapMode = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . self::UNTIT_CONFIG_KEYS['mode']);

        Debugger()->info("Units map mode: '{$this->unitsMapMode}'");
    }

    // ===============================================
    // Function: resolveUnitsMapDefaults
    // Inputs: none
    // Outputs: none (sets $this->unitsMapDefaults)
    // Purpose: Determines if units should be created automatically by default
    // Logic Walkthrough: Reads default creation flag from Config; if missing, sets false and logs warning
    // Side Effects: Populates $this->unitsMapDefaults
    // Uses: Config(), Debugger()
    // ===============================================
    private function resolveUnitsMapDefaults(): void{
        $defaults =  Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . $this->unitsMapMode . "." . self::UNTIT_CONFIG_KEYS['defaults']);
        Debugger()->info("Units created by default config value: " . var_export($defaults, true));
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

    // ===============================================
    // Function: resolveUnitsMapOverrides
    // Inputs: none
    // Outputs: none (sets $this->unitsMapOverrides)
    // Purpose: Fetches any mapping overrides for units/directories and logs them
    // Logic Walkthrough: Reads overrides from Config; determines context and target labels 
    //                    based on mapping mode, then logs using UnitResolvingManager
    // Side Effects: Populates $this->unitsMapOverrides
    // Uses: Config(), UnitResolvingManager::logOverrides()
    // ===============================================
    private function resolveUnitsMapOverrides(): void{
        $this->unitsMapOverrides = Config()->get("mode_config." . self::UNTIT_CONFIG_KEYS['map'] . "." . $this->unitsMapMode . "." . self::UNTIT_CONFIG_KEYS['overrides'], []);

        $logContextLabel = $this->unitsMapMode === 'units' ? 'Unit' : 'Directory';
        $logTargetLabel  = $this->unitsMapMode === 'units' ? 'Directories' : 'Units';

        $this->unitResolvingManager->logOverrides($logContextLabel, $logTargetLabel, $this->unitsMapOverrides);
    }
}
