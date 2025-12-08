<?php

namespace App\Src\Core\Debuggers;

use App\Src\Core\Debuggers\Helpers\FormatManager;
use App\Src\Core\Debuggers\Helpers\MessageManager;
use Illuminate\Console\Command;
use Log;
use App\Src\Core\DTOs\CliInputContextDTO;

class Debugger
{
    private CliInputContextDTO $cliInputContextDTO; 
    
    private Command $commandInstance;
    
    private bool $cliLog;

    private bool $contextsLoaded = false;

    private bool $fileLog;

    private static ?self $instance = null;

    public function __construct(
        private MessageManager $messageManager
    ) {}

    private function __clone() {}
    
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(new MessageManager(new FormatManager));
        }

        return self::$instance;
    }

    private function loadContexts(): void{
        if ($this->contextsLoaded) return;

        $this->cliInputContextDTO = ContextBus()->get(CliInputContextDTO::class);

        $this->contextsLoaded = true;
    }
    
    public function boot(Command $commandInstance): void
    {
        $this->commandInstance = $commandInstance;
        
        $this->loadContexts();
     
        $this->fileLog =  $this->cliInputContextDTO->getOption('file-log') ?? false;
        $this->cliLog =  $this->cliInputContextDTO->getOption('cli-log') ?? false;

        if($this->fileLog || $this->cliLog) $this->header('Debugger Started', 'huge');
    }

    public function info(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'info', $this->commandInstance, $this->cliLog, $this->fileLog);
    }
    
    public function warning(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'warning', $this->commandInstance, $this->cliLog, $this->fileLog);
    }
    
    public function error(string $message): void 
    { 
        $this->messageManager->logMessage($message, 'error', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    public function raw(string $message): void{
        $this->messageManager->logMessage($message, 'raw', $this->commandInstance, $this->cliLog, $this->fileLog);
    }

    public function header(string $message, string $level = 'medium'): void 
    { 
        $this->messageManager->logHeader($message, $level, $this->commandInstance, $this->cliLog, $this->fileLog);
    }

}