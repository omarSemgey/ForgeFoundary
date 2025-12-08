<?php

namespace App\Src\Core\NamingConventions\DTOs;

// ===============================================
// Class: NamingConventionsRuleDTO
// Purpose: Data Transfer Object representing a single naming convention rule. 
//          Holds the style type and whether it is enabled or disabled.
// Functions:
//   - __construct(): initializes the DTO with style and enabled status
// ===============================================
final class NamingConventionsRuleDTO
{
    // ===============================================
    // Constructor: __construct
    // Inputs:
    //   - string $style: The naming style, e.g., "camelCase", "PascalCase", etc.
    //   - bool $enabled: Flag indicating if this rule is active
    // Outputs: none
    // Purpose: Initializes the DTO with a naming style and its enabled state
    // Logic: Simply assigns the inputs to public properties
    // External Functions/Helpers Used: none
    // Side Effects: None
    // ===============================================
    public function __construct(
        public string $style,      
        public bool   $enabled,
    ) {}
}
