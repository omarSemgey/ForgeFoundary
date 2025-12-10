# Dumb Modes Command

## Overview

The `dumb-modes` command lists all **modes available** in ForgeFoundary. It scans your configured modes folder, retrieves metadata from each mode, and displays a structured view in the terminal. This is useful for quickly checking which modes you have installed and their basic configuration info.

Think of it as a **mode inventory command**.

---

## Usage

```bash
ForgeFoundary dumb-modes [options]
```

### Options

* `--config-name=` – Specify a main configuration file name.
* `--config-path=` – Set a custom path to the main configuration YAML.
* `--custom=*` – Pass additional custom CLI flags.
* `--cli-log` – Enable logging output in the terminal.
* `--file-log` – Save logs to a file.

---

## How it works

When executed, `dumb-modes`:

1. Loads your **main configuration** to locate the modes folder.
2. Scans each subdirectory in the modes folder for YAML mode files.
3. Parses each mode’s metadata (like name, description, or author) from the YAML file.
4. Displays all modes in a **tree-like structure**, including metadata if available.

> The command only reads and displays metadata — it **does not modify** any files or run scaffolding.

---

## Example

```bash
# List all available modes
ForgeFoundary dumb-modes
```

---

## Next Steps

After reviewing your modes:

1. Run the **dry-run** command to preview scaffolding for a specific mode:

```bash
ForgeFoundary dry-run --mode=MyCustomMode
```

2. Run the **scaffold** command to apply a mode and generate project files:

```bash
ForgeFoundary scaffold --mode=MyCustomMode
```

> For more information on creating or customizing modes, see [Creating Modes](../Creating_Modes.md) and the sections under `Docs/ModeSections/`.
