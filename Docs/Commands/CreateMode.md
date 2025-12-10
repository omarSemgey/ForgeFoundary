# Create Mode Command

## Overview

The `create-mode` command allows you to **quickly generate a new mode skeleton** in ForgeFoundary. A mode defines how your project should be scaffolded, including directories, templates, units, components, commands, CLI flags, and naming conventions.

This command is the **starting point** for building custom scaffolding logic that suits your workflow.

---

## Usage

```bash
ForgeFoundary create-mode --mode-name=MyCustomMode [options]
```

### Options

* `--mode-name=` – Required. The name of the new mode you want to create.
* `--cli-log` – Enable logging output directly in the terminal while generating the mode.
* `--file-log` – Save logs to a file while generating the mode.

---

## How it works

When executed, `create-mode`:

1. Loads your **tool configuration** and CLI context.
2. Resolves paths for the new mode folder and its YAML configuration.
3. Creates the **mode directory** and a **Templates folder** inside it.
4. Copies a **default YAML template** into the new mode folder, ready to be edited.
5. Logs messages confirming the creation of the mode.

> Think of this command as the scaffolding for your scaffolding—everything else you define in the mode will be based on this initial setup.

---

## Example

```bash
# Create a new mode called "LaravelDDDSkeleton"
ForgeFoundary create-mode --mode-name=LaravelDDDSkeleton

# Create a new mode with CLI logging enabled
ForgeFoundary create-mode --mode-name=PythonAPI --cli-log
```

---

## Next Steps

After creating a mode:

1. Navigate to your new mode folder:

   ```bash
   cd Modes/MyCustomMode
   ```

2. Edit the YAML file to define your **directories, templates, units, components, commands, CLI flags, and naming conventions**.

3. Add or customize templates in the `Templates/` folder.

4. Run the mode using:

   ```bash
   ForgeFoundary scaffold --mode=MyCustomMode
   ```

> For more advanced usage and to explore all mode sections, see the [Creating Modes](Docs/Creating_Modes).
> You can also learn about using **Mutators** for sharing data across multiple configuration keys in [Mutators.md](Docs/ModeSections/Mutators.md).