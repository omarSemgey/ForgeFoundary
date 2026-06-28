# ForgeFoundary

ForgeFoundary is a CLI scaffolding tool that generates complete project structures (files, folders, and boilerplate code) from configurable YAML templates.

Instead of manually creating directories, files, and boilerplate code, ForgeFoundary generates them from reusable YAML-based templates and configurable modes, helping teams maintain consistent project structures while reducing repetitive work.

---

## Why ForgeFoundary?

Creating new features often involves repeating the same boilerplate:

* Creating directories
* Adding multiple related files
* Following naming conventions
* Copying template code
* Remembering project structure

ForgeFoundary automates these repetitive tasks through configurable modes, allowing projects to generate consistent scaffolding with a single command.

---

## Quick Example

Instead of manually creating:

```text
Controllers/
└── UserController.php

Services/
└── UserService.php

Repositories/
└── UserRepository.php

Tests/
└── UserServiceTest.php
```

Generate everything with a single command:

```bash
ForgeFoundary make service User
```

The generated files, templates, naming conventions, and directory structure are entirely defined by your selected YAML mode, allowing the same command to work across different languages, frameworks, and project architectures.

---

## Quick Start
```bash
ForgeFoundary dry-run
```
---

## Problem It Solves

Developers repeatedly rebuild the same project structure when starting new features or projects:

- Controllers, services, repositories, tests
- Naming conventions
- Boilerplate setup
- Repeated file creation

ForgeFoundary removes this repetition by generating consistent, reusable project structures from configuration instead of manual setup.

---

## Key Features

- Multi-language support: Scaffold projects in any language by defining your own modes.
- Customizable modes: Each mode defines directory structures, templates, naming conventions, and scaffolding rules.
- CLI-driven workflow: Easily scaffold components, directories, and units from the command line.
- Template engine support: Use Mustache, Twig, or Blade-style templates.
- Pre- and post-scaffold hooks: Run custom commands automatically before or after scaffolding.
- Portable and framework-agnostic: Not tied to any specific framework or programming style.

---

## How It Works

### ForgeFoundary reads configuration from YAML mode files, which define:

- Component paths and names
- Directory structures and units
- Template files, placeholders, and extensions
- Naming conventions (PascalCase, camelCase, etc.)
- CLI flags and overrides for flexible scaffolding

---

## Architecture

ForgeFoundary is built around a modular architecture:

- CLI command parser
- YAML mode loader
- Template rendering engine
- Placeholder resolver
- File generation engine
- Hook execution system
- Naming convention engine

---

## Philosophy

ForgeFoundary is designed for developers who value automation, consistency, and flexibility in project setup. By separating scaffolding logic into configurable modes, it ensures repeatable and predictable project structures, no matter the language, framework, or team.

---

## Why PHP?

ForgeFoundary is implemented in PHP because of its strong ecosystem, Composer integration, and cross-platform compatibility, allowing the tool to be distributed easily while remaining language-agnostic.

---

## Installation Options

### Requirements

Before installing ForgeFoundary, make sure you have the following installed:

- **PHP** (version 8.5 or higher)
  [Download PHP](https://www.php.net/downloads)
- **Composer** (PHP dependency manager)
  [Download Composer](https://getcomposer.org/download/)

> ForgeFoundary depends on PHP and Composer to run and manage dependencies.

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

#### **Docker Environment Constraints**
Because Docker runs in a secure, isolated "sandbox," there are specific behaviors you need to be aware of:

- Host Dependency Limitation: Post-process hooks (like npm install or local system scripts) will fail because the container cannot "see" your host's installed binaries. Only tools included in the image (PHP, Composer, Git) will function.

- Path Isolation: The container only has access to the directory you "mount" into it. Absolute paths from your host machine will not work. You must use relative paths (e.g., ./output) to ensure files are written to your project folder.

- Permissions (Linux Users): Files created via Docker may be owned by root. Use the -u flag (detailed below) to avoid permission issues.

#### **AUR / yay (Arch Linux)**

```bash
yay -S forgefoundary
```

---

## Documentation

ForgeFoundary includes a full documentation system that explains every feature in depth, including CLI commands, YAML configuration, template behavior, and scaffolding logic.

 [Docs](https://omarsemgey.github.io/ForgeFoundary/Manual/).

The documentation is designed as a complete reference guide. It walks through ForgeFoundary step-by-step, from basic usage to advanced configuration, including:

- Every CLI command and option
- Full YAML mode structure (file-by-file breakdown)
- Template engine behavior and placeholder system
- Hook system (pre/post execution flow)
- Naming conventions and resolution rules
- Edge cases and known limitations
- Real usage examples and expected outputs

It is structured so you can follow it sequentially, starting from a simple scaffold and progressing toward building complex, production-ready project structures using reusable configurations.
