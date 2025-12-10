# Mutators

## Overview

The **Mutators** section allows you to create reusable values in your mode configuration and link them to CLI flags. This provides a **clean way to change configuration values via the command line** without editing the YAML directly.

Mutators use **YAML anchors and references**, which also makes your config DRY (Don’t Repeat Yourself) and easy to maintain.

---

## Example

```yaml
component_name: *component_name_mutator YourComponentName # assign a mutant

cli_flags: 
  component_name: *component_name_mutator # assign a CLI flag linked to the mutator

component_name: *component_name_mutator # reference the mutator as a key value
```

### How it works

1. Define a **mutator** using `*mutator_name` syntax.
2. Optionally assign a **CLI flag** in `cli_flags` and link it to the mutator.
3. Reference the mutator anywhere in the mode to reuse its value.

---

## Guidelines

* Naming convention: it’s recommended to name mutators like `keyName_mutator` for clarity, though any valid YAML name works.
* Mutators are **optional** but highly useful for keeping large configs clean.
* You can use mutators **like regular YAML anchors**, not only for CLI flags, to avoid repeating values.

---

## Usage

* When a CLI flag linked to a mutator is used, ForgeFoundary **overrides the value** in the configuration dynamically.
* This makes it easy to **customize a scaffolding run** without touching your YAML file.