<?php

namespace App\Src\Core\NamingConventions\Resolvers;

use App\Src\Core\NamingConventions\DTOs\NamingConventionsContextDTO;
use App\Src\Core\NamingConventions\DTOs\NamingConventionsRuleDTO;

class NamingConventionsResolver
{
    private const NAMING_CONVENTIONS_CONFIG_KEYS = [
        "naming_conventions" => "naming_conventions"
    ];

    public function resolveNamingConventionsContext(): NamingConventionsContextDTO
    {
        $dto = new NamingConventionsContextDTO();
        $config = Config()->get("mode_config." . self::NAMING_CONVENTIONS_CONFIG_KEYS["naming_conventions"], []);

        foreach ($config as $style => $systems) {
            foreach ($systems as $systemName => $configBlock) {

                // Skip non-array entries (like "component: true")
                if (!is_array($configBlock)) continue;

                $defaults  = $configBlock['defaults'] ?? false;
                $overrides = $configBlock['overrides'] ?? [];

                $wildKey = "{$systemName}:*";

                // Apply default only if not already enabled
                $this->setRuleIfNotEnabled($dto, $wildKey, $style, $defaults);

                // Apply overrides
                foreach ($overrides as $item) {
                    $key = "{$systemName}:{$item}";
                    $enabled = $defaults ? false : true;

                    // Overrides also respect priority: a previous true wins
                    $this->setRuleIfNotEnabled($dto, $key, $style, $enabled);
                }
            }
        }

        return $dto;
    }

    private function setRuleIfNotEnabled(NamingConventionsContextDTO $dto, string $key, string $style, bool $enabled): void
    {
        $existing = $dto->getRule($key);

        // If there is already a rule with true, keep it
        if ($existing && $existing->enabled === true) {
            return;
        }

        // Otherwise set/overwrite
        $dto->setRule($key, new NamingConventionsRuleDTO($style, $enabled));
    }

}