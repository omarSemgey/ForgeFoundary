# Dry Run Command

## Overview

The `dry-run` command allows you to **simulate a full ForgeFoundary scaffolding** without creating or modifying any real files. It’s perfect for **previewing your mode configuration** and seeing the generated project structure safely.

Think of it as a “practice run” before committing your scaffolding to disk.

---

## Usage

```bash
ForgeFoundary dry-run --mode=ModeName [options]
```

### Options

* `--mode=` – Specify which mode to simulate.
* `--modes-path=` – Optionally set a custom path to your modes folder.
* `--config-name=` – Select a specific main configuration file name.
* `--config-path=` – Set a custom path to the main configuration YAML.
* `--custom=*` – Pass additional custom CLI flags declared in the mode.
* `--cli-log` – Enable logging output directly in the terminal.
* `--file-log` – Save logs to a file.

---

## How it works

When executed, `dry-run`:

1. Loads your **tool and mode configuration**.

2. Creates a **temporary folder** to simulate all scaffolding operations.

3. Overrides paths (like components) to ensure nothing is written to your actual project.

4. Runs all systems in order:

   * Components
   * Directories
   * Units
   * Templates
   * Commands

5. Renders a **tree view** of the generated structure.

6. Cleans up by **deleting the temporary folder** after the run.

> The command gives a safe, real-time preview of your project scaffolding without affecting your actual files.

---

## Example

```bash
# Simulate scaffolding for the default mode
ForgeFoundary dry-run

# Simulate a custom mode with CLI logging
ForgeFoundary dry-run --mode=MyCustomMode --cli-log

# Simulate using additional custom flags
ForgeFoundary dry-run --mode=WebApp --custom=component_path=/tmp/temp_components
```

---

## Next Steps

After reviewing a dry run:

1. Update your mode’s YAML configuration or templates as needed.
2. Once satisfied, run the **real scaffolding** with:

```bash
ForgeFoundary scaffold --mode=MyCustomMode
```

> For more information on creating modes and customizing your scaffolding, see [Creating Modes](../Creating_Modes.md) and the sections under `Docs/ModeSections/`.