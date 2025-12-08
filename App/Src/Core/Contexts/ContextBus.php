<?php

namespace App\Src\Core\Contexts;

use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use RuntimeException;

// ===============================================
// Class: ContextBus
// Purpose: Singleton that acts as a global registry for context objects (DTOs).
//          Provides controlled mutation of certain contexts during bootstrap phase.
//          Ensures single instance access and centralizes context management.
//
// Functions:
//   - getInstance(): Retrieve singleton instance
//   - publish(string $key, object $dto): Publish a new context under a key
//   - get(string $key): Retrieve a published context
//   - mutateModeValue(array $newModeValue): Modify the modeValue in ConfigContextDTO during bootstrap
//   - disableMutation(): Disable further mutations for safety
// ===============================================
class ContextBus
{
    private static ?self $instance = null; 
    private bool $mutationEnabled = true;  // Flag controlling whether mutation is allowed
    private array $contexts = [];           // Stores all published context objects

    private function __construct() {}

    private function __clone() {}

    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ===============================================
    // Function: publish
    // Inputs:
    //   - string $key: Key to store the context under
    //   - object $dto: The context object (usually a DTO)
    // Outputs: void
    // Purpose: Publish a new context object in the registry
    // Logic: Sets the DTO in the internal $contexts array with the provided key
    // Side Effects: Modifies internal contexts storage
    // ===============================================
    public function publish(string $key, object $dto): void
    {
        $this->contexts[$key] = $dto;
    }

    // ===============================================
    // Function: get
    // Inputs:
    //   - string $key: Key of the context to retrieve
    // Outputs: object (the context DTO)
    // Purpose: Retrieve a published context
    // Logic:
    //   - Check if the context exists for the key
    //   - If not, throw RuntimeException
    //   - Return the context object
    // Side Effects: Throws exception if key not found
    // ===============================================
    public function get(string $key): object
    {
        if (!isset($this->contexts[$key])) {
            throw new RuntimeException("Context '{$key}' not found in ContextBus");
        }
        return $this->contexts[$key];
    }

    // ===============================================
    // Function: mutateModeValue
    // Inputs:
    //   - array $newModeValue: New mode configuration to apply
    // Outputs: void
    // Purpose: Update modeValue in ConfigContextDTO during bootstrap phase
    // Logic:
    //   - Check if mutation is enabled
    //   - Retrieve ConfigContextDTO from contexts
    //   - If not found, throw exception
    //   - Update its modeValue property
    // Side Effects:
    //   - Modifies modeValue in ConfigContextDTO
    //   - Throws RuntimeException if mutation disabled or context missing
    // External functions: getInstance() indirectly through published contexts
    // ===============================================
    public function mutateModeValue(array $newModeValue): void{
        if (!$this->mutationEnabled) {
            throw new RuntimeException(
                "Mutation denied: 'ConfigContextDTO' can only be changed during the bootstrap phase"
            );
        }

        $configContext = $this->contexts[ConfigContextDTO::class] ?? null;

        if (!$configContext) {
            throw new RuntimeException("Cannot mutate modeValue — 'ConfigContextDTO' not found in ContextBus");
        }
        $configContext->modeValue = $newModeValue;
    }

    // ===============================================
    // Function: disableMutation
    // Inputs: none
    // Outputs: void
    // Purpose: Prevent any further changes to mutable contexts
    // Logic: Sets $mutationEnabled to false
    // Side Effects: Disables all future calls to mutateModeValue
    // ===============================================
    public function disableMutation(): void{
        $this->mutationEnabled = false;
    }
}