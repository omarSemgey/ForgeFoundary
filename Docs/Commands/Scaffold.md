# Scaffold Command

## Overview

The `scaffold` command is the **main command** in ForgeFoundary. It generates the project structure according to a selected **mode**, creating directories, templates, units, and components automatically. This is the command you will use most often to apply the scaffolding defined in your modes.

## Usage

```bash
ForgeFoundary scaffold --mode=ModeName [options]
```

### Options

* `--mode=` – Specify which mode to run.
* `--modes-path=` – Optionally set a custom path to your modes folder.
* `--tree-view` – Render a visual tree of the generated project structure.
* `--config-name=` – Select a specific configuration file name for the mode(Main config not mode).
* `--config-path=` – Set a custom path to the mode’s configuration YAML(Main config not mode).
* `--custom=*` – Pass additional custom cli flags declared in the mode.
* `--cli-log` – Enable logging output directly in the terminal.
* `--file-log` – Save logs to a file.

---

## How it works

When executed, `scaffold`:

1. Loads your **tool and mode configuration**.
2. Boots all necessary systems for scaffolding.
3. Runs the following **in order**:

   * Pre-Scaffold Commands
   * Components
   * Directories
   * Units
   * Templates
   * Post-Scaffold Commands
4. Outputs logs and reports.
5. Optionally, displays a **tree view** of the generated structure if `--tree-view` is enabled.

> Think of this command as the central engine that applies all the rules and templates you’ve defined in your mode.

---

## Example

```bash
# Run the default mode defined in the main config file
ForgeFoundary scaffold

# Run a custom mode and render tree view
ForgeFoundary scaffold --mode=MyCustomMode --tree-view
```

---

For more information on **creating modes** and configuring your scaffolding structure, see [Creating Modes](Creating_Modes.md) and explore the sections under `Docs/ModeSections/`.