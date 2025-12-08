<?php

namespace App\Src\Core\Debuggers\Helpers;

// ===============================================
// Class: FormatManager
// Purpose: Provides formatting for debugger messages and headers, including color coding,
//          header levels, and file/CLI specific formats.
// Functions:
//   - resolveMessageFormat(string $message, string $type, bool $fileDebug): string
//       Formats a message according to type and output destination
//   - resolveHeaderFormat(string $header, string $level, bool $fileDebug): string
//       Formats a header message with a level prefix
// Private functions:
//   - resolveMessageColors(string $type): string
//       Maps message type to a color; logs error if unknown
//   - resolveMessageContext(string $type): string
//       Creates a context string including timestamp, file, and method
//   - resolveHeaderPrefix(string $level): string
//       Maps header level to a visual prefix; logs error if unknown
// ===============================================
class FormatManager
{
    // Color mapping for different message types
    private const COLORS = [
        'info' => 'gray',
        'warning' => 'yellow',
        'error' => 'red',
        'raw' => 'white',
    ];

    // Header prefix mapping by level
    private const HEADER_LEVELS = [
        'huge' => '=====',
        'big' => '====',
        'medium' => '===',
        'small' => '==',
    ];

    // ===============================================
    // Function: resolveMessageFormat
    // Inputs:
    //   - string $message: The message text
    //   - string $type: Type of message ('info', 'warning', 'error', 'raw')
    //   - bool $fileDebug: Whether output is for file logging
    // Outputs: formatted string ready for CLI or file
    // Purpose: Formats a debugger message with proper colors, context, and wrapping
    // Logic:
    //   - Generate a context string with timestamp, file, method
    //   - Determine color based on message type
    //   - Word-wrap the message
    //   - If raw type, handle differently
    // Side Effects: Calls Debugger()->error() if unknown type
    // External functions: resolveMessageContext(), resolveMessageColors()
    // ===============================================
    public function resolveMessageFormat(string $message, string $type, bool $fileDebug = false): string 
    {
        $context = $this->resolveMessageContext($type);
        $color = $this->resolveMessageColors($type);
        $message = wordwrap($message, 100, "\n   ");
        
        if($fileDebug){
            return "⟶ {$context}\n   {$message}";
            if($type === 'raw') return "      {$message}";
        }

        if($type === 'raw') return "   {$message}";
        
        return "⟶ <fg={$color}>{$context}</>\n   {$message}";
    }

    // ===============================================
    // Function: resolveHeaderFormat
    // Inputs:
    //   - string $header: The header text
    //   - string $level: Header level ('small', 'medium', 'big', 'huge')
    //   - bool $fileDebug: Whether output is for file logging
    // Outputs: formatted header string
    // Purpose: Formats headers with level prefixes for CLI/file
    // Logic:
    //   - Map level to prefix using resolveHeaderPrefix
    //   - Wrap the header with prefix brackets
    // Side Effects: Calls Debugger()->error() if unknown level
    // External functions: resolveHeaderPrefix()
    // ===============================================
    public function resolveHeaderFormat(string $header, string $level, bool $fileDebug = false): string {
        $prefix = $this->resolveHeaderPrefix($level);
        if($fileDebug){
            return "[{$prefix} {$header} {$prefix}]";
        }

        return "\n[{$prefix} {$header} {$prefix}]";
    }

    // ===============================================
    // Function: resolveMessageColors
    // Inputs: string $type
    // Outputs: string (color)
    // Purpose: Maps a message type to a predefined color
    // Logic: Check COLORS map; fallback to 'info' if unknown
    // Side Effects: Calls Debugger()->error() if type unknown
    // ===============================================
    private function resolveMessageColors(string $type): string
    {
        if (!isset(self::COLORS[$type])) {
            Debugger()->error("Unknown message type '{$type}' used in Debugger. Falling back to 'info'");
            $type = 'info';
        }
        return self::COLORS[$type];
    }

    // ===============================================
    // Function: resolveMessageContext
    // Inputs: string $type
    // Outputs: string context with timestamp, file, method, and type
    // Purpose: Generates a message context for logs
    // Logic:
    //   - Inspect debug_backtrace
    //   - Find first frame not belonging to a Debug class
    //   - Return formatted string
    // Side Effects: None
    // ===============================================
    private function resolveMessageContext(string $type): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = 'unknown';
        $method = 'unknown';

        foreach ($trace as $frame) {
            if (!isset($frame['class']) || !str_contains($frame['class'], 'Debug')) {
                $file = basename($frame['file'] ?? 'unknown');
                $method = $frame['function'] ?? 'unknown';
                break;
            }
        }
        
        return ' ' . date('[H:i:s]')  . " [{$file}::{$method} - {$type}]";
    }

    // ===============================================
    // Function: resolveHeaderPrefix
    // Inputs: string $level
    // Outputs: string prefix
    // Purpose: Maps a header level to a visual prefix
    // Logic: Use HEADER_LEVELS map; fallback to 'medium' if unknown
    // Side Effects: Calls Debugger()->error() if level unknown
    // ===============================================
    private function resolveHeaderPrefix(string $level): string{
        if (!isset(self::HEADER_LEVELS[$level])) {
            Debugger()->error("Unknown header level '{$level}' used in Debugger. Falling back to 'medium'");
            $level = 'medium';
        }
        return self::HEADER_LEVELS[$level];
    }
}
