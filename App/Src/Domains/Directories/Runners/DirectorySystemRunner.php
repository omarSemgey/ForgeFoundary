<?php

namespace App\Src\Domains\Directories\Runners;

use App\Src\Domains\Directories\Generators\DirectoriesGenerator;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use App\Src\Domains\Directories\Resolvers\DirectoryResolver;
use App\Src\Core\Helpers\SystemStateManager;

class DirectorySystemRunner
{
    public function __construct(
        private DirectoryResolver $directoryResolver, 
        private DirectoriesGenerator $directoriesGenerator,
        private SystemStateManager $systemStateManager,
        ) {}
        
    public function run(): void
    {
        Debugger()->header('Directories System Runner Started.', 'big');
        if(!$this->systemStateManager->assertEnabled('directories', 'Directories')){
            return;
        };
        $this->validate();
        $this->resolve();
        $this->generate();
        Debugger()->header('Directories System Runner Finished.', 'big');
    }

    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    private function generate(): void{
        Debugger()->header('Directories Generator Started.', 'medium');
        $this->directoriesGenerator->generateDirectories();
        Debugger()->header('Directories Generator Finished.', 'medium');
    }
    private function resolve(): void
    {
        Debugger()->header('Directories Resolver Started.', 'medium');
        $directoryContextDTO = $this->directoryResolver->resolveDirectoriesContext();
        $this->publishDTO(DirectoryContextDTO::class, $directoryContextDTO);
        Debugger()->header('Directories Resolver Finished.', 'medium');
    }

    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Directories Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Directories Context Publisher Finished.', 'medium');
    }
}