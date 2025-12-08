<?php

namespace App\Src\Domains\Units\Helpers;


class UnitResolvingManager
{
    public function logOverrides(string $contextLabel, string $targetLabel, array $overrides): void
    {
        if (empty($overrides)) {
            Debugger()->info("No '{$contextLabel}' overrides defined");
            return;
        }

        Debugger()->info("Resolved '{$contextLabel}' overrides:");

        foreach ($overrides as $source => $targets) {
            switch (true) {
                case $targets === ["*"]:
                    Debugger()->raw("  - '{$source}': All {$targetLabel}");
                    break;

                case empty($targets):
                    Debugger()->raw("  - '{$source}': No {$targetLabel}");
                    break;

                default:
                    Debugger()->raw("  - '{$source}': " . implode(', ', $targets));
                    break;
            }
        }
    }
}