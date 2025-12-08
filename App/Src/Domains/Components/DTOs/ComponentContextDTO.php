<?php

namespace App\Src\Domains\Components\DTOs;

// ===============================================
// Class: ComponentContextDTO
// Purpose: Data Transfer Object (DTO) representing the basic context of a component in ForgeFoundary.
//          Holds the component's name and the path where it will be created or exists.
// Functions:
//   - __construct(): initializes the component name and path
// ===============================================
final class ComponentContextDTO
{
    public function __construct(
        public string $componentName,
        public string $componentPath,
    ) {}
}
