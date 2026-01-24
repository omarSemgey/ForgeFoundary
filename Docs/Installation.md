# ForgeFoundary Installation Guide

This document explains how to install ForgeFoundary on your system, including the requirements and the provided installation scripts.

---

## Requirements

Before installing ForgeFoundary, make sure you have the following installed:

- **PHP** (version 8.2 or higher)  
  [Download PHP](https://www.php.net/downloads)
- **Composer** (PHP dependency manager)  
  [Download Composer](https://getcomposer.org/download/)

> ForgeFoundary depends on PHP and Composer to run and manage dependencies.

---

## Installation Options
### Manual Installation (Directly from the installation script)

You can fetch the installation script directly from the repository and run it. This works on Unix/Linux/macOS and Windows.

#### **Unix / Linux / macOS**

```bash
curl -o install.sh https://raw.githubusercontent.com/omarSemgey/ForgeFoundary/main/InstallationScripts/install.sh
chmod +x install.sh
./install.sh
```

The script will:

1. Ask for the installation directory (default: ~/ForgeFoundary).

2. Clone the ForgeFoundary repository to the specified folder.

3. Install PHP dependencies using Composer.

4. Attempt to make the ForgeFoundary command available globally (requires write access to /usr/local/bin).

5. If the script cannot link globally, you can manually add the install directory to your PATH.

---

#### **Windows**

```powershell
Invoke-WebRequest -Uri "https://raw.githubusercontent.com/omarSemgey/ForgeFoundary/main/InstallationScripts/install.ps1" -OutFile "install.ps1"
Set-ExecutionPolicy Bypass -Scope Process -Force
.\install.ps1
```

The script will:

1. Ask for the installation directory (default: $HOME\ForgeFoundary).

2. Clone the ForgeFoundary repository to the specified folder.

3. Install PHP dependencies using Composer.

4. Optionally guide you to make ForgeFoundary accessible globally.

---

### Automatic Installation 

ForgeFoundary can also be installed using the following ways:

#### **Docker Installation**
The fastest way to run ForgeFoundary without installing PHP, Composer, or dependencies on your host machine.

1. Pull the Image
```Bash
docker pull omarsemgey/forgefoundary
```

2. Run Anywhere
Since Docker runs in a container, you must mount your current directory so the tool can see your project files:

```Bash
docker run --rm -it -v "$(pwd):/app" -w /app omarsemgey/forgefoundary [command]
```

3. (Optional) Set an Alias
To run it just by typing ForgeFoundary, add an alias to your shell profile:

**Linux/macOS (.bashrc or .zshrc):**

``alias ForgeFoundary='docker run --rm -it -v "$(pwd):/app" -w /app omarsemgey/forgefoundary'``

**Windows (PowerShell $PROFILE):** 

``function ForgeFoundary { docker run --rm -it -v "${PWD}:/app" -w /app omarsemgey/forgefoundary $args }``

#### **AUR / yay (Arch Linux)**

```bash
yay -S forgefoundary
```


---

## Next Steps

After installation, you can run the tool using:

```bash
ForgeFoundary dry-run
```