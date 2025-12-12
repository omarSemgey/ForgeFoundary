<?php

namespace App\Src\Core\NamingConventions\Resolvers;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\DTOs\NamingConventionsRuleDTO;
use App\Src\Core\NamingConventions\DTOs\NamingConventionsSectionDTO;

// ===============================================
// Class: NamingConventionsResolver
// Purpose: Resolves and builds the naming conventions context
//          based on the configuration defined in the mode_config YAML.
// Functions:
//   - resolveNamingConventionsContext(): returns a populated NamingConventionsContextDTO
//   - setRuleIfNotEnabled(): helper to safely set a naming rule if it’s not already enabled
// ===============================================
class NamingConventionsResolver
{
    // ===============================================
    // Constant: NAMING_CONVENTIONS_CONFIG_KEYS
    // Purpose: Holds config keys for naming conventions lookup
    // ===============================================
    private const NAMING_CONVENTIONS_CONFIG_KEYS = [
        "naming_conventions" => "naming_conventions"
    ];

    // ===============================================
    // Function: resolveNamingConventionsContext
    // Inputs: none
    // Outputs: NamingConventionsContextDTO containing all resolved naming convention sections
    // Purpose: Reads the naming conventions configuration from the mode config and converts
    //          each section into a strongly-typed DTO for use in the scaffolder
    // Logic Walkthrough:
    //   1. Fetches the raw naming conventions array from the config using Config()->get()
    //   2. Initializes an empty array to store NamingConventionsSectionDTOs
    //   3. Loops through each section key/value pair:
    //        a. For each section, creates a NamingConventionsSectionDTO using 'defaults' and 'overrides'
    //        b. Falls back to empty arrays if 'defaults' or 'overrides' are not set
    //   4. Packages all section DTOs into a NamingConventionsContextDTO
    //   5. Returns the fully-resolved NamingConventionsContextDTO
    // Side Effects: none
    // Uses: Config(), NamingConventionsSectionDTO, NamingConventionsContextDTO
    // ===============================================
    public function resolveNamingConventionsContext(): NamingConventionsContextDTO
    {
        $sections = Config()->get("mode_config." . self::NAMING_CONVENTIONS_CONFIG_KEYS["naming_conventions"], []);
        $namingConventionsSections = [];

        foreach($sections as $key => $value){
            $namingConventionsSections[$key] = new NamingConventionsSectionDTO(
                $value['defaults'] ?? [],
                $value['overrides'] ?? [],
            );
        }
        return new NamingConventionsContextDTO($namingConventionsSections);
    }
}