<?php

namespace App\Src\Core\Helpers;

class UserResourcesManager
{
    private string $installDir;
    private string $userDir;

    public function __construct(string $installDir)
    {
        $this->installDir = rtrim($installDir, DIRECTORY_SEPARATOR);
    }
    
    public function getUserDir(): string
    {
        return $this->userDir;
    }

    public function handleUserResources(): void
    {
        $os = PHP_OS_FAMILY;
        switch ($os) {
            case 'Linux':
                $this->handleLinux();
                break;
            case 'Darwin':
                $this->handleMac();
                break;
            case 'Windows':
                $this->handleWindows();
                break;
            default:
                throw new \Exception("Unsupported OS: $os");
        }
    }

    private function handleLinux(): void
    {
        $configDir = getenv('XDG_CONFIG_HOME') ?: getenv('HOME') . '/.config';
        $this->userDir = $configDir . '/forgefoundary';
        $this->handlePaths();
    }

    private function handleMac(): void
    {
        $this->userDir = getenv('HOME') . '/Library/Application Support/ForgeFoundary';
    }

    private function handleWindows(): void
    {
        $this->userDir = getenv('USERPROFILE') . '\AppData\Local\ForgeFoundary';
        $this->handlePaths();
    }

    private function handlePaths(): void
    {
        $modesPath = $this->userDir . '/Modes';
        $configPath = $this->userDir . '/Configs/ForgeFoundary.yaml';
        $configDir = dirname($configPath);

        if (!is_dir($modesPath)) {
            mkdir($modesPath, 0755, true);
            $this->copyDirectory($this->installDir . '/App/Src/Modes', $modesPath);
        }

        if (!file_exists($configPath)) {
            echo $configPath;
            mkdir($configDir, 0755, true);
            copy($this->installDir . '/App/Src/Configs/ForgeFoundary.yaml', $configPath);
        }
    }

    private function copyDirectory(string $src, string $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;

            $srcPath = $src . DIRECTORY_SEPARATOR . $file;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
        closedir($dir);
    }
}
