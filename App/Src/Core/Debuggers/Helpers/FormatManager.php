<?php

namespace App\Src\Core\Debuggers\Helpers;

class FormatManager
{
    private const COLORS = [
        'info' => 'gray',
        'warning' => 'yellow',
        'error' => 'red',
        'raw' => 'white',
    ];

    private const HEADER_LEVELS = [
        'huge' => '=====',
        'big' => '====',
        'medium' => '===',
        'small' => '==',
    ];

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

    public function resolveHeaderFormat(string $header, string $level, bool $fileDebug = false): string {
        $prefix = $this->resolveHeaderPrefix($level);
        if($fileDebug){
            return "[{$prefix} {$header} {$prefix}]";
        }

        return "\n[{$prefix} {$header} {$prefix}]";
    }

    private function resolveMessageColors(string $type): string
    {
        if (!isset(self::COLORS[$type])) {
            Debugger()->error("Unknown message type '{$type}' used in Debugger. Falling back to 'info'");
            $type = 'info';
        }
        return self::COLORS[$type];
    }

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

    private function resolveHeaderPrefix(string $level): string{
        if (!isset(self::HEADER_LEVELS[$level])) {
            Debugger()->error("Unknown header level '{$level}' used in Debugger. Falling back to 'medium'");
            $level = 'medium';
        }
        return self::HEADER_LEVELS[$level];
    }
}