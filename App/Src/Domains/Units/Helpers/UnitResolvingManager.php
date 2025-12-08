<?php

namespace App\Src\Domains\Units\Helpers;

// ===============================================
// Class: UnitResolvingManager
// Purpose: Provides helper functionality to resolve and log unit overrides
//          in a human-readable way using the Debugger.
// Functions:
//   - logOverrides(string $contextLabel, string $targetLabel, array $overrides): void
//       Logs resolved overrides with a summary of what is applied for each source.
// ===============================================
class UnitResolvingManager
{
    // ===============================================
    // Function: logOverrides
    // Inputs:
    //   - $contextLabel (string): label describing the context of the overrides (e.g., "Directories")
    //   - $targetLabel (string): label describing the target items (e.g., "Units")
    //   - $overrides (array): key-value pairs where the key is a source and the value is an array of targets
    // Outputs: void
    // Purpose: Logs resolved overrides in a readable format for debugging purposes.
    // Logic:
    //   1. If $overrides is empty, log that no overrides are defined.
    //   2. Otherwise, iterate over each source => targets pair:
    //       - If targets is ["*"], log "All {$targetLabel}".
    //       - If targets is empty, log "No {$targetLabel}".
    //       - Otherwise, log a comma-separated list of targets.
    // Side Effects: Outputs messages to the Debugger (info and raw logs).
    // Uses: Debugger()->info(), Debugger()->raw()
    // ===============================================
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
