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

## Installation Scripts

ForgeFoundary provides two scripts to install the tool, depending on your operating system.

### 1. Unix / Linux / macOS

Script: `install.sh`

**How to run:**

```bash
chmod +x install.sh
./install.sh
```

The script will:

1. Ask for the installation directory (default: `~/ForgeFoundary`).
2. Clone the ForgeFoundary repository to the specified folder.
3. Install PHP dependencies using Composer.
4. Attempt to make the `ForgeFoundary` command available globally (requires write access to `/usr/local/bin`).

If the script cannot link globally, you can manually add the install directory to your `PATH`.

---

### 2. Windows

Script: `install.ps1`

**How to run:**

Open PowerShell with administrative rights and execute:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
.\install.ps1
```

The script will:

1. Ask for the installation directory (default: `$HOME\ForgeFoundary`).
2. Clone the ForgeFoundary repository to the specified folder.
3. Install PHP dependencies using Composer.
4. Optionally, guide you to make `ForgeFoundary` accessible globally.

---

## Next Steps

After installation, you can run the tool using:

```bash
ForgeFoundary dry-run
```

For further instructions, mode documentation, and advanced usage, refer to [Docs/Docs.md](Docs/Docs.md).