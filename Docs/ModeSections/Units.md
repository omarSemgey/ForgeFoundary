# Units

## Overview

The **Units** section is an optional but powerful part of your mode. Think of **units as modular boxes** that organize your project structure in ways that simple directories cannot. Units can represent common scaffolding patterns like `Crud`, `Auth`, or custom logic blocks.

Units are often used in combination with the **Units Map** section, which links units to specific directories, allowing you to control exactly where each unit is applied.

> ⚠️ Units rely on directories being created. If `directories_enabled` is `false`, units will be skipped during scaffolding.

---

## Keys

```yaml
units_enabled: true
units:
  - Crud
  - Auth
  - Logic
```

### Explanation

* `units_enabled` – Boolean flag to enable or disable unit processing.
* `units` – A list of unit names available in this mode. These are the scaffolding building blocks you can assign to directories using `units_map`.

---

## Units Map

The **Units Map** section defines **how units are linked to directories**. There are two modes:

* `mode: directories` – Map each directory to the units it should receive.
* `mode: units` – Map each unit to the directories it should affect.

```yaml
units_map:
  mode: directories   # or 'units'
```

---

### Directories Mode

```yaml
units_map:
  mode: directories

  directories:
    units_created_by_default: true
    overrides: {}
```

**Keys:**

* `units_created_by_default` – Determines if every directory should automatically receive all units listed in `units`.
* `overrides` – Specify unit assignments per directory. Use an array of unit names or `"*"` to assign all units.

**Examples:**

```yaml
units:
  - Crud
  - Auth
  - Logic
units_map:
  mode: directories

  directories:
    units_created_by_default: true
    overrides: 
      Models: []
      Policies: 
        - Crud
        - Logic
```

* Every directory gets all units by default.
* `Models` receives no units.
* `Policies` receives only `Crud` and `Logic`.

```yaml
units:
  - Crud
  - Auth
  - Logic
units_map:
  mode: directories

  directories:
    units_created_by_default: false
    overrides: 
      Models: [Crud]
      Policies: ["*"]
```

* No directory receives units by default.
* `Models` gets only `Crud`.
* `Policies` gets all units.

---

### Units Mode

```yaml
units_map:
  mode: units

  units: 
    units_created_by_default: true
    overrides: 
      Crud: [Controllers]
      Logic: []
```

**Keys:**

* `units_created_by_default` – Determines if all directories receive this unit automatically.
* `overrides` – Assigns specific directories to individual units. Use `"*"` to assign the unit to every directory.

**Examples:**

```yaml
units:
  - Crud
  - Auth
  - Logic
units_map:
  mode: units

  units: 
    units_created_by_default: false
    overrides: 
      Crud: [Controllers]
      Logic: ["*"]
```

* Every directory receives `Logic`.
* `Controllers` additionally gets `Crud`.
* No directory receives `Auth`.

---

### Notes

* Units can be reused across multiple directories or customized per directory using the **overrides**.
* Units help maintain modularity and reduce repetitive configuration, especially in large projects.