# ForgeFoundary

A flexible, framework-agnostic scaffolding tool for any programming language or architecture.

## Key Features

- Multi-language support: Scaffold projects in any language by defining your own modes.
- Customizable modes: Each mode defines directory structures, templates, naming conventions, and scaffolding rules.
- CLI-driven workflow: Easily scaffold components, directories, and units from the command line.
- Template engine support: Use Mustache, Twig, or Blade-style templates.
- Pre- and post-scaffold hooks: Run custom commands automatically before or after scaffolding.
- Portable and framework-agnostic: Not tied to any specific framework or programming style.

## How It Works

### ForgeFoundary reads configuration from YAML mode files, which define:

- Component paths and names
- Directory structures and units
- Template files, placeholders, and extensions
- Naming conventions (PascalCase, camelCase, etc.)
- CLI flags and overrides for flexible scaffolding

## Philosophy

ForgeFoundary is designed for developers who value automation, consistency, and flexibility in project setup. By separating scaffolding logic into configurable modes, it ensures repeatable and predictable project structures, no matter the language, framework, or team.

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

## Quick Start
```bash
ForgeFoundary dry-run
```

## Further Instructions

For detailed instructions, examples, and a map of available documentation, refer to the [Docs](https://omarsemgey.github.io/ForgeFoundary/Manual/). it provides an organized overview of all other documentation files included with ForgeFoundary.
