# Dumb Mode Command

## Overview

The `dumb-mode` command allows you to **inspect the configuration values** of a single ForgeFoundary mode. It outputs the mode’s settings in a clear, structured format so you can understand its directories, templates, units, naming conventions, commands, and CLI flags without editing the files directly.

You can view the mode either as a **tree view** or as **raw YAML**.

---

## Usage

```bash
ForgeFoundary dumb-mode --mode=ModeName [options]
```

### Options

* `--mode=` – Specify which mode to inspect.
* `--modes-path=` – Optionally set a custom path to your modes folder.
* `--raw-yaml` – Output the configuration as raw YAML instead of a tree view.
* `--config-name=` – Specify a main configuration file name.
* `--config-path=` – Set a custom path to the main configuration YAML.
* `--custom=*` – Pass additional CLI flags.
* `--cli-log` – Enable logging output in the terminal.
* `--file-log` – Save logs to a file.

---

## How it works

When executed, `dumb-mode`:

1. Loads your **tool configuration** and **CLI input context**.
2. Locates the specified mode’s YAML configuration file.
3. Reads the mode’s configuration into memory.
4. Outputs the configuration either as:

   * **Tree view** – nested directories, templates, and units displayed hierarchically.
   * **Raw YAML** – full YAML content, useful for copying or editing.
5. Adds headers and footers for readability in the terminal.

> This command does **not modify any files** or run scaffolding — it only displays the mode’s configuration.

---

## Example

```bash
# Display a mode in tree view
ForgeFoundary dumb-mode --mode=MyCustomMode

# Display a mode as raw YAML
ForgeFoundary dumb-mode --mode=MyCustomMode --raw-yaml

# Using a custom modes folder
ForgeFoundary dumb-mode --mode=MyCustomMode --modes-path=/path/to/modes
```

---

## Next Steps

After reviewing a mode with `dumb-mode`:

* Use `dry-run` to simulate scaffolding with this mode.
* Use `scaffold` to apply the mode and generate project files.