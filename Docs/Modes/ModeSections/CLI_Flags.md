# CLI Flags

## Overview

CLI flags let you **modify configuration values without touching the YAML** by linking them to **mutators** (YAML anchors). This is an optional feature that gives you more flexibility during scaffolding runs.

---

## Example

```yaml
component_name: *component_name_mutator YourComponentName # assign a mutator

cli_flags: 
  component_name: *component_name_mutator # assign a CLI flag linked to the mutator

component_name: *component_name_mutator # reference the mutator as the key value
```

### Usage in the terminal

```bash
# Set a single value using the custom CLI flag
ForgeFoundary scaffold --custom=component_name=CustomComponentName

# Multiple flags in one command
ForgeFoundary scaffold --custom=flag1=value1 --custom=flag2=value2

# Or using comma-separated format
ForgeFoundary scaffold --custom=flag1=value1,flag2=value2
```

---

## Notes

* You **don’t need to stick with the key name** in the YAML when declaring CLI flags; you can name flags anything:

```yaml
CustomCLIFlag: *customMutator
```

* Currently, **normal CLI flag syntax** (e.g., `--component_name=value`) is **not supported** due to technical limitations. You must use `--custom=`.
* For more on mutators and how they work, see [Mutators](Mutators.md).