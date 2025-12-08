<?php

namespace App\Src\Domains\Directories\DTOs;

// ===============================================
// Class: DirectoryContextDTO
// Purpose: Data Transfer Object (DTO) to encapsulate the context 
//          for directories during scaffolding.
// Functions:
//   - __construct(): Initializes the DTO with directories array
// ===============================================
final class DirectoryContextDTO
{
   public function __construct(
        public array $directories
    ) {}
}
