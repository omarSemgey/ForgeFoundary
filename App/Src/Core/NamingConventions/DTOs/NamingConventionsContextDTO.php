<?php

namespace App\Src\Core\NamingConventions\DTOs;

// ========================================================
// Class: NamingConventionsContextDTO
// Purpose: Data Transfer Object (DTO) to hold naming convention rules.
//          Stores rules for various components, templates, or files keyed by a string.
// Functions:
//   - setRule(string $key, NamingConventionsRuleDTO $rule): Adds or updates a rule
//   - getRule(string $key): ?NamingConventionsRuleDTO: Retrieves a rule by key if it exists
// ========================================================
final class NamingConventionsContextDTO
{
    // ===============================================
    // Property: rules
    // Type: array
    // Purpose: Stores mapping of keys to NamingConventionsRuleDTO objects
    // Example: "templates:dto.mustache" => NamingConventionsRuleDTO instance
    // Side Effects: None directly
    // ===============================================
    public array $rules = [];

    // ===============================================
    // Function: setRule
    // Inputs:
    //   - string $key: Unique identifier for the rule (e.g., template or component name)
    //   - NamingConventionsRuleDTO $rule: The rule object to store
    // Outputs: void
    // Purpose: Adds a new rule or updates an existing rule for a given key
    // Logic Walkthrough:
    //   - Stores the given rule in the $rules array with the key provided
    // External Functions/Helpers Used: None
    // Side Effects: Modifies the internal $rules array
    // ===============================================
    public function setRule(string $key, NamingConventionsRuleDTO $rule): void
    {
        $this->rules[$key] = $rule;
    }

    // ===============================================
    // Function: getRule
    // Inputs:
    //   - string $key: The key identifying the rule to retrieve
    // Outputs: ?NamingConventionsRuleDTO (returns null if key does not exist)
    // Purpose: Retrieves a stored rule for a specific key
    // Logic Walkthrough:
    //   - Checks if the key exists in $rules array
    //   - Returns the rule if found, otherwise returns null
    // External Functions/Helpers Used: None
    // Side Effects: None
    // ===============================================
    public function getRule(string $key): ?NamingConventionsRuleDTO
    {
        return $this->rules[$key] ?? null;
    }
}
