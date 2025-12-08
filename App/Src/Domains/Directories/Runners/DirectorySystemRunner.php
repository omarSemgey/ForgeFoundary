<?php

namespace App\Src\Domains\Directories\Runners;

use App\Src\Domains\Directories\Generators\DirectoriesGenerator;
use App\Src\Domains\Directories\DTOs\DirectoryContextDTO;
use App\Src\Domains\Directories\Resolvers\DirectoryResolver;
use App\Src\Core\Helpers\SystemStateManager;

// ===============================================
// Class: DirectorySystemRunner
// Purpose: Orchestrates the "Directories" system within ForgeFoundary.
//          Coordinates validation, resolution, and generation of project directories.
//          Publishes directory context to the global ContextBus for other systems.
// Functions:
//   - __construct(): injects dependencies required to run the system
//   - run(): main entry point, executes validation, resolution, and generation in sequence
//   - validate(): placeholder for future validation logic
//   - resolve(): resolves directories context and publishes it globally
//   - generate(): generates directories according to the resolved context
//   - publishDTO(): publishes a DTO to the ContextBus
// ===============================================
class DirectorySystemRunner
{
    public function __construct(
        private DirectoryResolver $directoryResolver, 
        private DirectoriesGenerator $directoriesGenerator,
        private SystemStateManager $systemStateManager,
    ) {}
    
    // ===============================================
    // Function: run
    // Inputs: none
    // Outputs: void
    // Purpose: Main entry point for the directories system
    // Logic Walkthrough:
    //   1. Prints a debug header that directories system is starting
    //   2. Checks via SystemStateManager if the directories system is enabled; exits if not
    //   3. Calls validate() (currently placeholder)
    //   4. Calls resolve() to get directory context and publish it
    //   5. Calls generate() to create directories according to context
    //   6. Prints a debug header that directories system has finished
    // External Functions/Helpers Used:
    //   - Debugger()->header()
    //   - SystemStateManager->assertEnabled()
    //   - validate(), resolve(), generate() (internal)
    // Side Effects:
    //   - Generates directories on the filesystem
    //   - Publishes directory context to ContextBus
    // ===============================================
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

    // ===============================================
    // Function: validate
    // Inputs: none
    // Outputs: void
    // Purpose: placeholder for future validation of directories system
    // Logic Walkthrough: TODO
    // Side Effects: none
    // ===============================================
    private function validate(): void
    {
        // TODO: implement validation layer later
    }

    // ===============================================
    // Function: generate
    // Inputs: none
    // Outputs: void
    // Purpose: Executes the directory generator to create required directories
    // Logic Walkthrough:
    //   1. Prints a debug header that generation started
    //   2. Calls generateDirectories() on DirectoriesGenerator
    //   3. Prints a debug header that generation finished
    // External Functions/Helpers Used:
    //   - DirectoriesGenerator->generateDirectories()
    //   - Debugger()->header()
    // Side Effects: creates directories on the filesystem
    // ===============================================
    private function generate(): void
    {
        Debugger()->header('Directories Generator Started.', 'medium');
        $this->directoriesGenerator->generateDirectories();
        Debugger()->header('Directories Generator Finished.', 'medium');
    }

    // ===============================================
    // Function: resolve
    // Inputs: none
    // Outputs: void
    // Purpose: Resolves directories context and publishes it globally
    // Logic Walkthrough:
    //   1. Prints a debug header that resolver started
    //   2. Calls resolveDirectoriesContext() on DirectoryResolver
    //   3. Publishes the resolved context DTO via publishDTO()
    //   4. Prints a debug header that resolver finished
    // External Functions/Helpers Used:
    //   - DirectoryResolver->resolveDirectoriesContext()
    //   - publishDTO()
    //   - Debugger()->header()
    // Side Effects:
    //   - Publishes DirectoryContextDTO to ContextBus
    // ===============================================
    private function resolve(): void
    {
        Debugger()->header('Directories Resolver Started.', 'medium');
        $directoryContextDTO = $this->directoryResolver->resolveDirectoriesContext();
        $this->publishDTO(DirectoryContextDTO::class, $directoryContextDTO);
        Debugger()->header('Directories Resolver Finished.', 'medium');
    }

    // ===============================================
    // Function: publishDTO
    // Inputs:
    //   - string $dtoKey: class name or key to publish
    //   - object $dto: DTO instance to publish
    // Outputs: void
    // Purpose: Publishes the DTO to the global ContextBus
    // Logic Walkthrough:
    //   1. Prints debug header that publishing started
    //   2. Calls ContextBus()->publish() with the DTO key and object
    //   3. Prints info about published context
    //   4. Prints debug header that publishing finished
    // External Functions/Helpers Used:
    //   - ContextBus()->publish()
    //   - Debugger()->header()
    //   - Debugger()->info()
    // Side Effects:
    //   - Updates global context state
    // ===============================================
    private function publishDTO(string $dtoKey, object $dto): void
    {
        Debugger()->header('Directories Context Publisher Started.', 'medium');
        ContextBus()->publish($dtoKey, $dto);
        Debugger()->info("Published Context: '{$dtoKey}' to the global context bus");
        Debugger()->header('Directories Context Publisher Finished.', 'medium');
    }
}
