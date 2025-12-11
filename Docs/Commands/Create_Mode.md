# Create Mode Command

## Overview

The `create-mode` command allows you to **quickly generate a new mode skeleton** in ForgeFoundary. A mode defines how your project should be scaffolded, including directories, templates, units, components, commands, CLI flags, and naming conventions.

With the latest update, you can now **choose from four mode types** that provide different levels of detail for your new mode:

* **Minimal** – Only mode metadata, component path & name, empty directories array, and basic template paths.
* **Moderate** – Metadata, component, templates, units sections included, but without nested keys.
* **Extended** – Includes metadata, component, templates, units, commands, CLI flags, and naming conventions with mutators.
* **Full** – All keys, including nested keys and mutators; the most complete starting template.

This command is the **starting point** for building custom scaffolding logic that suits your workflow.

---

## Usage

```bash
ForgeFoundary create-mode --mode-name=MyCustomMode --mode-type=[minimal|moderate|extended|full] [options]
```

### Options

* `--mode-name=` – **Required.** The name of the new mode you want to create.
* `--mode-type=` – **Optional.** The type of mode template to use (`minimal`, `moderate`, `extended`, `full`). Defaults to `full` if not specified.
* `--cli-log` – Enable logging output directly in the terminal while generating the mode.
* `--file-log` – Save logs to a file while generating the mode.

---

## How it works

When executed, `create-mode`:

1. Loads your **tool configuration** and CLI context.
2. Resolves paths for the new mode folder and its YAML configuration.
3. Determines which **mode template** to use (`minimal`, `moderate`, `extended`, or `full`).
4. Creates the **mode directory** and a **Templates folder** inside it.
5. Copies the selected **YAML template** into the new mode folder, ready to be edited.
6. Logs messages confirming the creation of the mode.

> Think of this command as the scaffolding for your scaffolding—everything else you define in the mode will be based on this initial setup.

---

## Examples

```bash
# Create a new minimal mode
ForgeFoundary create-mode --mode-name=SimpleMode --mode-type=minimal

# Create a full-featured mode with CLI logging enabled
ForgeFoundary create-mode --mode-name=AdvancedMode --mode-type=full --cli-log

# Create a moderate mode
ForgeFoundary create-mode --mode-name=ModerateMode
```

---

## Next Steps

After creating a mode:

1. Navigate to your new mode folder:

```bash
cd Modes/MyCustomMode
```

2. Edit the YAML file to define your **directories, templates, units, components, commands, CLI flags, and naming conventions** according to your workflow.

3. Add or customize templates in the `Templates/` folder.

4. Run the mode using:

```bash
ForgeFoundary scaffold --mode=MyCustomMode
```