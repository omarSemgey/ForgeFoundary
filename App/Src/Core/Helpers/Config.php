<?php

namespace App\Src\Core\Helpers;

class Config
{
    private static ?self $instance = null;

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

    public function set(string $key, array $Value): void
    {
        $this->configs[$key] = $Value;
    }

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