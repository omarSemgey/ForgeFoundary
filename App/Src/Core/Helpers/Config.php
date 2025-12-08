<?php

namespace App\Src\Core\Helpers;

// ===============================================
// Class: Config
// Purpose: Singleton configuration manager for ForgeFoundary.
//          Stores and retrieves hierarchical configuration arrays.
// Functions:
//   - getInstance(): retrieves the singleton instance
//   - set(string $key, array $Value): stores a configuration array under a given key
//   - get(string $key, mixed $default = null): retrieves a configuration value using dot notation
// ===============================================
class Config
{
    private static ?self $instance = null;

    // Stores all configuration arrays
    private array $configs = [];

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
    // Function: set
    // Inputs:
    //   - string $key: Key under which configuration will be stored
    //   - array $Value: The configuration array to store
    // Outputs: void
    // Purpose: Saves configuration array under a key
    // Logic: Stores $Value in $configs keyed by $key
    // Side Effects: Modifies internal $configs array
    // ===============================================
    public function set(string $key, array $Value): void
    {
        $this->configs[$key] = $Value;
    }

    // ===============================================
    // Function: get
    // Inputs:
    //   - string $key: Dot-notated key to retrieve configuration
    //   - mixed $default: Default value if key does not exist
    // Outputs: mixed: The retrieved configuration or $default
    // Purpose: Retrieve a nested configuration value safely
    // Logic Walkthrough:
    //   1. Splits the key by '.' to get segments
    //   2. Iterates through each segment, drilling into $configs
    //   3. If a segment is missing, returns $default
    //   4. Returns the final value if all segments exist
    // External Functions/Helpers Used: none
    // Side Effects: None
    // ===============================================
    public function get(string $key, mixed $default = null): mixed {
        $segments = explode('.', $key);
        $value = $this->configs;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}
