<?php

namespace App\Src\Commands;

use App\Src\Commands\Traits\HandlesUserErrors;
use App\Src\Domains\Configs\DTOs\ConfigContextDTO;
use Illuminate\Console\Command;
use RuntimeException;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\DTOs\CliInputContextDTO;
use App\Src\Core\Helpers\PathManager;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;

// ==================================================
// Class: CreateModeCommand
// Purpose: Implements the "create-mode" CLI command 
//          for ForgeFoundary. Allows the user to create 
//          a new mode scaffold with a YAML file and 
//          corresponding templates folder.
// Functions:
//   - __construct(): Injects dependencies
//   - handle(): Entry point for the command, bootstraps systems and triggers mode generation
//   - loadContext(): Loads ConfigContextDTO and CliInputContextDTO from ContextBus
//   - generateMode(): Handles actual creation of mode directory, YAML file, and templates
// ==================================================
class CreateModeCommand extends Command
{
    use HandlesUserErrors;
    // ===============================================
    // Command signature and description
    // signature: command name and CLI options
    // description: short summary displayed in artisan list
    // ===============================================
    protected $signature = 'create-mode {--mode-name=} {--mode-type} {--cli-log} {--file-log}';
    protected $description = 'Create a new mode for the ForgeFoundary command';

    public function __construct(
        private Bootstrapper $bootstrapper,
        private PathManager $pathManager,
        private Filesystem $files,
    )
    {
        parent::__construct();
    }

    // Context DTOs
    private ConfigContextDTO $configContextDTO;
    private CliInputContextDTO $cliInputContextDTO;

    private const MODE_TYPES = [
        "full" => "full", 
        "extended" => "extended", 
        "moderate" => "moderate", 
        "minimum" => "minimum", 
    ];

    // ===============================================
    // Function: loadContext
    // Inputs: none
    // Outputs: none
    // Purpose: Loads configuration and CLI input DTOs from ContextBus for use in command
    // Logic Walkthrough:
    //   1. Retrieves ConfigContextDTO from ContextBus
    //   2. Logs info using Debugger
    //   3. Retrieves CliInputContextDTO from ContextBus
    //   4. Logs info using Debugger
    // External Functions/Helpers Used:
    //   - ContextBus()->get()
    //   - Debugger()->info()
    // Side Effects: modifies private properties $configContextDTO and $cliInputContextDTO
    // ===============================================
    private function loadContext(): void{
        $this->configContextDTO = ContextBus()->get(ConfigContextDTO::class);
        Debugger()->info("Loaded context: 'ConfigContextDTO' from the context bus");
        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        Debugger()->info("Loaded context: 'CliInputContextDTO' from the context bus");
    }

    // ===============================================
    // Function: handle
    // Inputs: none (called by artisan automatically)
    // Outputs: void
    // Purpose: Entry point for the CLI command
    // Logic Walkthrough:
    //   1. Defines TOOL_BASE_PATH using PathManager
    //   2. Boots core systems using Bootstrapper
    //   3. Loads configuration and CLI input context
    //   4. Logs start of Create Mode command
    //   5. Calls generateMode() to create mode files/folders
    //   6. Logs finish of Create Mode command
    // External Functions/Helpers Used:
    //   - define()
    //   - Bootstrapper->boot()
    //   - loadContext()
    //   - Debugger()->header()
    //   - generateMode()
    // Side Effects: initializes context and runs bootstrapping logic
    // ===============================================
    public function handle(): int
    {
        return $this->runWithUserFriendlyErrors(function() {
            define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
            $this->bootstrapper->boot($this);
            $this->loadContext();
    
            Debugger()->header('Forge Foundary Create Mode Command Started.', 'huge');
    
            $this->generateMode();
         
            Debugger()->header('Forge Foundary Create Mode Command Finished.', 'huge');
            
            Debugger()->header('Debugger Finished.', 'huge');
        });
    }

    // ===============================================
    // Function: generateMode
    // Inputs:
    //   - None explicitly (relies on $this->cliInputContextDTO and $this->configContextDTO)
    // Outputs:
    //   - None (void), but creates directories and files on disk
    //
    // Purpose:
    //   Generates a new mode scaffold for ForgeFoundary. This includes:
        //     1. Creating the mode directory
        //     2. Creating a Templates subdirectory
        //     3. Copying the selected mode template YAML into the mode directory
        //
        // Logic Walkthrough:
        //   1. Retrieve the mode name from CLI input via $this->cliInputContextDTO->getOption("mode-name")
        //   2. Retrieve the mode type from CLI input and normalize it using MODE_TYPES constant
        //   3. Throw a RuntimeException if no mode name is provided
        //   4. Determine the paths:
    //        - $modesPath: base folder for all modes from config
    //        - $modePath: normalized path for this new mode
    //        - $modeFilePath: full path for the YAML mode file
    //        - $modeTemplatesPath: folder for mode templates
    //        - $modeTemplatePath: source path to the selected template file in TOOL_BASE_PATH
    //   5. Read the content of the template file
    //   6. Create directories:
    //        - $modePath
    //        - $modeTemplatesPath
    //   7. Write the template YAML content into $modeFilePath
    //   8. Log information that mode creation succeeded using Debugger
    //
    // External Functions / Helpers Used:
    //   - $this->cliInputContextDTO->getOption(): retrieves CLI options
    //   - $this->pathManager->normalizeSlashes(): normalizes file system paths
    //   - $this->files->makeDirectory(): creates directories on disk
    //   - File::put(): writes content to a file
    //   - file_get_contents(): reads content from a file
    //   - Debugger()->info(): logs information to the CLI
    //
    // Side Effects:
    //   - Creates new directories on the filesystem
    //   - Writes a new YAML mode file
    //   - Logs to Debugger
    // ===============================================
    private function generateMode(): void{
        $modeName = $this->cliInputContextDTO->getOption("mode-name");
        $modeType = $this->cliInputContextDTO->getOption("mode-type");
        $modeType = self::MODE_TYPES[strtolower($modeType)];
        Debugger()->info("Mode type selected: '{$modeType}'");
        
        if(!$modeName){
            throw new RuntimeException("No mode name given");
        }

        $modesPath = $this->configContextDTO->modesPath;
        $modePath = $this->pathManager->normalizeSlashes($modesPath . "/" . $modeName);

        $modeFilePath = $this->pathManager->normalizeSlashes($modePath . "/" . $modeName . ".yaml");

        $modeTemplatesPath = $this->pathManager->normalizeSlashes($modePath . "/Templates"); 

        $modeTypePath = $this->pathManager->normalizeSlashes(TOOL_BASE_PATH . "/Core/ModeTypes/{$modeType}.yaml");
        
        $modeTemplateValue = file_get_contents($modeTypePath);

        $this->files->makeDirectory($modePath, 0755, true);
        $this->files->makeDirectory($modeTemplatesPath, 0755, true);
        
        File::put($modeFilePath, $modeTemplateValue);

        Debugger()->info("Created mode '{$modeName}' at path '{$modePath}");
    }
}
