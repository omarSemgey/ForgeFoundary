<?php

namespace App\Src\Core\NamingConventions\Resolvers;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\DTOs\NamingConventionsRuleDTO;

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
    // Outputs: NamingConventionsContextDTO
    // Purpose: Builds the full naming conventions context from configuration
    // Logic Walkthrough:
    //   1. Creates a new NamingConventionsContextDTO instance
    //   2. Loads naming conventions config from mode_config
    //   3. Iterates over styles and systems in the config
    //   4. For each system:
    //       a) Apply default naming rules using setRuleIfNotEnabled
    //       b) Apply any overrides for specific items using setRuleIfNotEnabled
    //   5. Returns the populated DTO
    // External Functions/Helpers Used:
    //   - Config()->get(): retrieves configuration
    //   - setRuleIfNotEnabled(): helper defined in this class
    // Side Effects: none (purely builds a DTO)
    // ===============================================
    public function resolveNamingConventionsContext(): NamingConventionsContextDTO
    {
        $dto = new NamingConventionsContextDTO();
        $config = Config()->get("mode_config." . self::NAMING_CONVENTIONS_CONFIG_KEYS["naming_conventions"], []);

        foreach ($config as $style => $systems) {
            foreach ($systems as $systemName => $configBlock) {

                if (!is_array($configBlock)) continue;

                $defaults  = $configBlock['defaults'] ?? false;
                $overrides = $configBlock['overrides'] ?? [];

                $wildKey = "{$systemName}:*";

                $this->setRuleIfNotEnabled($dto, $wildKey, $style, $defaults);

                foreach ($overrides as $item) {
                    $key = "{$systemName}:{$item}";
                    $enabled = $defaults ? false : true;

                    $this->setRuleIfNotEnabled($dto, $key, $style, $enabled);
                }
            }
        }

        return $dto;
    }

    // ===============================================
    // Function: setRuleIfNotEnabled
    // Inputs:
    //   - NamingConventionsContextDTO $dto: the DTO to store rules in
    //   - string $key: the rule key (system or system:item)
    //   - string $style: the naming style (e.g., camelCase, PascalCase)
    //   - bool $enabled: whether the rule is enabled
    // Outputs: void
    // Purpose: Safely sets a naming convention rule in the DTO if it isn’t already enabled
    // Logic Walkthrough:
    //   1. Retrieves the existing rule for the given key
    //   2. If the rule exists and is enabled, do nothing
    //   3. Otherwise, create a new NamingConventionsRuleDTO and set it in the DTO
    // External Functions/Helpers Used:
    //   - NamingConventionsContextDTO->getRule()
    //   - NamingConventionsContextDTO->setRule()
    // Side Effects:
    //   - Mutates the passed DTO by adding/updating rules
    // ===============================================
    private function setRuleIfNotEnabled(NamingConventionsContextDTO $dto, string $key, string $style, bool $enabled): void
    {
        $existing = $dto->getRule($key);

        if ($existing && $existing->enabled === true) {
            return;
        }

        $dto->setRule($key, new NamingConventionsRuleDTO($style, $enabled));
    }

}
