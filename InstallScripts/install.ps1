#!/usr/bin/env pwsh

Write-Host "======================================"
Write-Host "        ForgeFoundary Installer        "
Write-Host "======================================"

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "Error: PHP is not installed or not in PATH. Please install PHP >=8.2 and try again."
    exit 1
}

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "Error: Composer is not installed or not in PATH. Please install Composer and try again."
    exit 1
}

$defaultDir = "$HOME\ForgeFoundary"
$installDir = Read-Host "Where should ForgeFoundary be installed? (default: $defaultDir)"
if ([string]::IsNullOrWhiteSpace($installDir)) {
    $installDir = $defaultDir
}

$installDir = [Environment]::ExpandEnvironmentVariables($installDir)

Write-Host "Cloning repository into $installDir..."
git clone https://github.com/omarsemgey/ForgeFoundary.git $installDir

Set-Location $installDir

Write-Host "Running composer install..."
composer install

$binPath = "$installDir"
$currentUserPath = [Environment]::GetEnvironmentVariable("Path", "User")

if (-not $currentUserPath.Split(";") -contains $binPath) {
    [Environment]::SetEnvironmentVariable("Path", "$currentUserPath;$binPath", "User")
    Write-Host "Added $binPath to user PATH. You may need to restart your terminal."
} else {
    Write-Host "$binPath is already in your PATH."
}

Write-Host "Installation complete! You can now run 'ForgeFoundary' from PowerShell or CMD if PATH is set."