<?php

namespace App\Src\Core\Debuggers;

use App\Src\Core\Debuggers\Helpers\FormatManager;
use App\Src\Core\Debuggers\Helpers\MessageManager;
use Illuminate\Console\Command;
use Log;
use App\Src\Core\DTOs\CliInputContextDTO;

// ===============================================
// Class: Debugger
// Purpose: Handles logging for both CLI and file output, with different levels of messages.
//          Singleton that uses MessageManager to format and print messages according to user options.
// Functions:
//   - boot(Command $commandInstance): Initialize debugger with CLI options
//   - info(string $message): Log an info message
//   - warning(string $message): Log a warning message
//   - error(string $message): Log an error message
//   - raw(string $message): Log a raw message without formatting
//   - header(string $message, string $level): Print a formatted header
// ===============================================
class Debugger
{
    private CliInputContextDTO $cliInputContextDTO; // Stores CLI input options relevant to debugging
    private Command $commandInstance;               // Current command instance
    private bool $cliLog;                           // Whether to log to CLI
    private bool $contextsLoaded = false;           // Tracks if CLI contexts are loaded
    private bool $fileLog;                          // Whether to log to file
    private static ?self $instance = null;          

    public function __construct(
        private MessageManager $messageManager
    ) {}

    // ===============================================
    // Function: getInstance
    // Inputs: none
    // Outputs: Debugger instance
    // Purpose: Returns the singleton instance, creating it if it does not exist
    // Logic: Lazy-loads MessageManager with FormatManager dependency
    // Side Effects: Creates new Debugger instance on first call
    // ===============================================
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(new MessageManager(new FormatManager));
        }
        return self::$instance;
    }

    // ===============================================
    // Function: loadContexts
    // Inputs: none
    // Outputs: void
    // Purpose: Loads CLI input contexts into Debugger for configuration
    // Logic:
    //   - Checks if contexts are already loaded
    //   - If not, retrieves CliInputContextDTO from ContextBus
    // Side Effects: Sets $cliInputContextDTO and $contextsLoaded
    // ===============================================
    private function loadContexts(): void{
        if ($this->contextsLoaded) return;

        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);
        $this->contextsLoaded = true;
    }

    // ===============================================
    // Function: boot
    // Inputs: 
    //   - Command $commandInstance: Current command being executed
    // Outputs: void
    // Purpose: Initializes debugger for the current CLI command
    // Logic:
    //   - Sets $commandInstance
    //   - Loads CLI contexts
    //   - Reads 'file-log' and 'cli-log' options from CLI input
    //   - Prints a header if either logging mode is enabled
    // Side Effects: Updates internal state of logging options
    // External functions: loadContexts(), MessageManager->logHeader()
    // ===============================================
    public function boot(Command $commandInstance): void
    {
        $this->commandInstance = $commandInstance;
        $this->loadContexts();

        $this->fileLog =  $this->cliInputContextDTO->getOption('file-log') ?? false;
        $this->cliLog =  $this->cliInputContextDTO->getOption('cli-log') ?? false;

        if($this->fileLog || $this->cliLog) $this->header('Debugger Started', 'huge');
    }

    // ===============================================
    // Function: info
    // Inputs: string $message
    // Outputs: void
    // Purpose: Log a message with 'info' level to CLI and/or file
    // Logic: Delegates to MessageManager->logMessage() with 'info' type
    // Side Effects: Writes output to CLI/file depending on options
    // External functions: MessageManager->logMessage()
    // ===============================================
    public function info(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'info', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    // ===============================================
    // Function: warning
    // Inputs: string $message
    // Outputs: void
    // Purpose: Log a warning message
    // Logic: Delegates to MessageManager->logMessage() with 'warning' type
    // Side Effects: Writes output to CLI/file depending on options
    // ===============================================
    public function warning(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'warning', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    // ===============================================
    // Function: error
    // Inputs: string $message
    // Outputs: void
    // Purpose: Log an error message
    // Logic: Delegates to MessageManager->logMessage() with 'error' type
    // Side Effects: Writes output to CLI/file depending on options
    // ===============================================
    public function error(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'error', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    // ===============================================
    // Function: raw
    // Inputs: string $message
    // Outputs: void
    // Purpose: Log a raw unformatted message
    // Logic: Delegates to MessageManager->logMessage() with 'raw' type
    // Side Effects: Writes output directly to CLI/file
    // ===============================================
    public function raw(string $message): void{
        $this->messageManager->logMessage($message, 'raw', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    // ===============================================
    // Function: header
    // Inputs: 
    //   - string $message: Message to display as header
    //   - string $level: Header size ('small', 'medium', 'huge')
    // Outputs: void
    // Purpose: Prints a formatted header
    // Logic: Delegates to MessageManager->logHeader()
    // Side Effects: Writes output to CLI/file
    // External functions: MessageManager->logHeader()
    // ===============================================
    public function header(string $message, string $level = 'medium'): void 
    { 
        $this->messageManager->logHeader($message, $level, $this->commandInstance, $this->cliLog, $this->fileLog);
    }
}
