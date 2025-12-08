<?php
namespace App\Src\Core\Debuggers\Helpers;

use Illuminate\Console\Command;
use Log;

class MessageManager
{
    private const LOG_TYPES = [
        'info' => 'info',
        'warning' => 'warning',
        'error' => 'error',
        'raw' => 'info',
    ];

    public function __construct(
        private FormatManager $formatManager,
    ){}

    public function logMessage(string $message, string $logType, Command $commandInstance, bool $cliLog, bool $fileLog): void
    {
        if ($cliLog) {
            $FormattedMessage = $this->formatManager->resolveMessageFormat($message, $logType, false);
            $this->logToCli($FormattedMessage, $commandInstance);
        }

        if ($fileLog){
            $FormattedMessage = $this->formatManager->resolveMessageFormat($message, $logType, true);
            $this->LogToFile(self::LOG_TYPES[$logType], $FormattedMessage);
        }
    }

    public function logHeader(string $header, string $level, Command $commandInstance, bool $cliLog, bool $fileLog): void
    {
        $formattedHeader = $this->formatManager->resolveHeaderFormat($header, $level, $fileLog);
        if ($cliLog) {
            $this->logToCli($formattedHeader, $commandInstance);
        }
        if ($fileLog) {
            $this->logToFile("notice",$formattedHeader);
        }
    }

    private function logToCli(string $message, Command $commandInstance): void{
        $commandInstance->line($message);
    }

    private function logToFile(string $logType, string $message): void{
        Log::log($logType, $message);
    }
}
