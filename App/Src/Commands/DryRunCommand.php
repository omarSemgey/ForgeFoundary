<?php

namespace App\Src\Commands;

use App\Src\Commands\Traits\HandlesUserErrors;
use App\Src\Domains\Commands\Runners\CommandSystemRunner;
use App\Src\Domains\Components\Runners\ComponentSystemRunner;
use App\Src\Domains\Directories\Runners\DirectorySystemRunner;
use App\Src\Domains\Templates\Runners\TemplateSystemRunner;
use App\Src\Domains\Units\Runners\UnitSystemRunner;
use Illuminate\Console\Command;
use App\Src\Core\Bootstrappers\Bootstrapper;
use App\Src\Core\Helpers\TreeManager;
use Illuminate\Support\Facades\File;
use App\Src\Core\Helpers\PathManager;

// ===============================================
// Class: DryRunCommand
// Purpose: Executes a "dry run" of ForgeFoundary to simulate
//          all scaffolding operations without modifying real project files.
// Functions:
//   - __construct(): injects all required system runners and helpers
//   - handle(): main command execution flow for dry run
//   - overrideComponentPath(): temporarily overrides component_path option
// ===============================================
class DryRunCommand extends Command
{
    use HandlesUserErrors;
    // Signature defines all options available for this command
    protected $signature = 'dry-run {--mode=} {--modes-path=} {--config-name=} {--config-path=} {--custom=*} {--cli-log} {--file-log}';
    protected $description = 'Dry run the ForgeFoundary command';

    // Keys used for mapping dry run config overrides
    private const DRY_RUN_COMMAND_CONFIG_KEYS = [
        "component_path" => "component_path"
    ];

    public function __construct(
        private Bootstrapper $bootstrapper,
        private TreeManager $treeManager,
        private ComponentSystemRunner $componentSystemRunner,   
        private DirectorySystemRunner $directorySystemRunner,
        private UnitSystemRunner $unitSystemRunner, 
        private TemplateSystemRunner $templateSystemRunner,
        private CommandSystemRunner $commandSystemRunner,
        private PathManager $pathManager,
    )
    {
        parent::__construct();
    }

    // ===============================================
    // Function: handle
    // Inputs: none (CLI options read internally)
    // Outputs: void
    // Purpose: Executes the dry run process, simulating all scaffolding
    // Logic Walkthrough:
    //   1. Defines TOOL_BASE_PATH constant to reference tool location
    //   2. Creates a temporary directory for the dry run
    //   3. Overrides the component path to use temporary folder
    //   4. Boots core systems via Bootstrapper
    //   5. Logs start message via Debugger
    //   6. Runs all system runners (components, directories, units, templates)
    //   7. Executes command system runner (simulation mode)
    //   8. Renders tree of simulated structure
    //   9. Logs completion messages
    //  10. Deletes temporary dry run folder
    // External Functions/Helpers Used:
    //   - PathManager->resolveToolPath()
    //   - File::makeDirectory() / File::deleteDirectory()
    //   - Bootstrapper->boot()
    //   - Debugger() helper
    //   - run() methods of injected system runners
    //   - TreeManager->renderTree()
    // Side Effects:
    //   - Creates and deletes a temporary folder
    //   - Generates in-memory dry run output
    // ===============================================
    public function handle(): int
    {
        return $this->runWithUserFriendlyErrors(function() {
            define('TOOL_BASE_PATH', $this->pathManager->resolveToolPath(__DIR__));
    
            $tempPath = sys_get_temp_dir() . '/forge_dry_run_' . uniqid();
            File::makeDirectory($tempPath, 0755, true);
    
            $this->overrideComponentPath($tempPath);
        
            $this->bootstrapper->boot($this);
         
            Debugger()->header('Forge Foundary Command Started.', 'huge');
    
            $this->componentSystemRunner->run();
            $this->directorySystemRunner->run();
            $this->unitSystemRunner->run();
            $this->templateSystemRunner->run();
            $this->commandSystemRunner->execute(false);
    
            $this->treeManager->renderTree($this);
    
            Debugger()->header('Forge Foundary Command Finished.', 'huge');
            
            Debugger()->header('Debugger Finished.', 'huge');
            File::deleteDirectory($tempPath);
        });
    }

    // ===============================================
    // Function: overrideComponentPath
    // Inputs:
    //   - string $path: temporary path to override component_path
    // Outputs: void
    // Purpose: Ensures that the dry run uses a temporary folder
    //          instead of the real project folder
    // Logic Walkthrough:
    //   1. Reads any existing 'custom' CLI options
    //   2. Checks if a component_path override already exists
    //   3. Updates existing override or appends new override
    //   4. Updates the CLI input option to reflect new path
    // External Functions/Helpers Used:
    //   - $this->input->setOption()
    // Side Effects:
    //   - Modifies CLI input options for the current command
    // ===============================================
    private function overrideComponentPath(string $path): void
    {
        $customs = $this->option('custom') ?? [];

        $keyPrefix = self::DRY_RUN_COMMAND_CONFIG_KEYS["component_path"] . "=";

        $foundIndex = null;
        foreach ($customs as $i => $value) {
            if (str_starts_with($value, $keyPrefix)) {
                $foundIndex = $i;
                break;
            }
        }

        if ($foundIndex !== null) {
            $customs[$foundIndex] = $keyPrefix . $path;
        } else {
            $customs[] = $keyPrefix . $path;
        }

        $this->input->setOption('custom', $customs);
    }
}
