<?php

namespace App\Src\Commands;

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
    // ===============================================
    // Command signature and description
    // signature: command name and CLI options
    // description: short summary displayed in artisan list
    // ===============================================
    protected $signature = 'create-mode {--mode-name=} {--cli-log} {--file-log} {--log-file-name=} {--log-file-path=}';
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
    public function handle(): void
    {
        define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
        $this->bootstrapper->boot($this);
        $this->loadContext();

        Debugger()->header('Forge Foundary Create Mode Command Started.', 'huge');

        $this->generateMode();
     
        Debugger()->header('Forge Foundary Create Mode Command Finished.', 'huge');
        
        Debugger()->header('Debugger Finished.', 'huge');
    }

    // ===============================================
    // Function: generateMode
    // Inputs: none (uses context DTOs)
    // Outputs: void
    // Purpose: Creates the directory structure and YAML file for a new mode
    // Logic Walkthrough:
    //   1. Reads "mode-name" option from CLI input context
    //   2. Throws RuntimeException if no mode name provided
    //   3. Resolves full paths for the mode folder, YAML file, and templates folder
    //   4. Reads default mode template content from TOOL_BASE_PATH/Core/Templates/mode-template.yaml
    //   5. Creates the mode directory and templates directory (0755 permissions)
    //   6. Writes mode YAML file using File::put
    //   7. Logs info using Debugger
    // External Functions/Helpers Used:
    //   - $this->pathManager->normalizeSlashes()
    //   - file_get_contents()
    //   - $this->files->makeDirectory()
    //   - File::put()
    //   - Debugger()->info()
    // Side Effects: creates files/directories on disk
    // ===============================================
    private function generateMode(): void{
        $modeName = $this->cliInputContextDTO->getOption("mode-name");

        if(!$modeName){
            throw new RuntimeException("No mode name given");
        }

        $modesPath = $this->configContextDTO->modesPath;
        $modePath = $this->pathManager->normalizeSlashes($modesPath . "/" . $modeName);
        $modeFilePath = $this->pathManager->normalizeSlashes($modePath . "/" . $modeName . ".yaml");
        $modeTemplatesPath = $this->pathManager->normalizeSlashes($modePath . "/Templates");
        $modeTemplatePath = $this->pathManager->normalizeSlashes(TOOL_BASE_PATH . "/Core/Templates/mode-template.yaml");
        $modeTemplateValue = file_get_contents($modeTemplatePath);

        $this->files->makeDirectory($modePath, 0755, true);
        $this->files->makeDirectory($modeTemplatesPath, 0755, true);
        
        File::put($modeFilePath, $modeTemplateValue);

        Debugger()->info("Created mode '{$modeName}' at path '{$modePath}");
    }
}
