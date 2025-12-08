<?php
namespace App\Src\Core\Debuggers\Helpers;

use Illuminate\Console\Command;
use Log;

// ======================================================
// Class: MessageManager
// Purpose: Handles logging messages to both CLI and file.
//          Uses FormatManager to format messages and headers.
// Functions:
//   - logMessage(string $message, string $logType, Command $commandInstance, bool $cliLog, bool $fileLog)
//       Logs messages according to type and target.
//   - logHeader(string $header, string $level, Command $commandInstance, bool $cliLog, bool $fileLog)
//       Logs formatted headers.
// ======================================================
class MessageManager
{

    public function __construct(
        private FormatManager $formatManager,
    ){}

    // ======================================================
    // Function: logMessage
    // Inputs: 
    //   - string $message: The content to log
    //   - string $logType: Type of log (info, warning, error, raw)
    //   - Command $commandInstance: Current CLI command instance
    //   - bool $cliLog: Whether to log to CLI
    //   - bool $fileLog: Whether to log to file
    // Outputs: void
    // Purpose: Logs a message to CLI and/or file, formatting it according to log type
    // Logic:
    //   - If CLI logging enabled, format for CLI and send to logToCli()
    //   - If file logging enabled, format for file and send to logToFile()
    // External functions:
    //   - FormatManager->resolveMessageFormat()
    //   - logToCli()
    //   - logToFile()
    // Side Effects: Prints to CLI, writes to log file
    // ======================================================
    public function logMessage(string $message, string $logType, Command $commandInstance, bool $cliLog, bool $fileLog): void
    {
        if ($cliLog) {
            $FormattedMessage = $this->formatManager->resolveMessageFormat($message, $logType, false);
            $this->logToCli($FormattedMessage, $commandInstance);
        }

        if ($fileLog){
            $FormattedMessage = $this->formatManager->resolveMessageFormat($message, $logType, true);
            $this->logToFile($FormattedMessage);
        }
    }

    // ======================================================
    // Function: logHeader
    // Inputs:
    //   - string $header: Header text
    //   - string $level: Header level (small, medium, huge)
    //   - Command $commandInstance: Current CLI command instance
    //   - bool $cliLog: Whether to log to CLI
    //   - bool $fileLog: Whether to log to file
    // Outputs: void
    // Purpose: Logs a formatted header to CLI and/or file
    // Logic:
    //   - Formats header using FormatManager
    //   - Sends formatted header to logToCli() if CLI logging enabled
    //   - Sends formatted header to logToFile() as 'notice' if file logging enabled
    // External functions:
    //   - FormatManager->resolveHeaderFormat()
    //   - logToCli()
    //   - logToFile()
    // Side Effects: Prints to CLI, writes to log file
    // ======================================================
    public function logHeader(string $header, string $level, Command $commandInstance, bool $cliLog, bool $fileLog): void
    {
        $formattedHeader = $this->formatManager->resolveHeaderFormat($header, $level, $fileLog);
        if ($cliLog) {
            $this->logToCli($formattedHeader, $commandInstance);
        }
        if ($fileLog) {
            $this->logToFile($formattedHeader);
        }
    }

    // ======================================================
    // Function: logToCli
    // Inputs:
    //   - string $message: Formatted message
    //   - Command $commandInstance: Current CLI command
    // Outputs: void
    // Purpose: Sends message to CLI
    // Logic: Calls $commandInstance->line() with message
    // Side Effects: Prints to CLI
    // ======================================================
    private function logToCli(string $message, Command $commandInstance): void{
        $commandInstance->line($message);
    }

    // ======================================================
    // Function: logToFile
    // Inputs:
    //   - string $message: Formatted message
    // Outputs: void
    // Purpose: Sends message to log file
    // Side Effects: Writes to log file
    // ======================================================
    private function logToFile(string $message): void{
        $logFile = TOOL_BASE_PATH . '/Logs/debug.log'; 
        str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $logFile); // NOTE: change this and use path manager
        file_put_contents($logFile,  $message . PHP_EOL, FILE_APPEND);
    }
}
