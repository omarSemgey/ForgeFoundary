# Creating Modes with ForgeFoundary

ForgeFoundary is a **mode-based scaffolder**, meaning that every project structure, template set, and workflow is defined inside a **mode**. Modes are fully customizable and allow you to scaffold projects in any programming language, framework, or workflow.

---

## How Modes Work

Each mode is a self-contained folder inside the `Modes/` directory. A mode defines:

* **Directories** to create
* **Templates** to generate
* **Units** of functionality
* **CLI flags and commands**
* **Naming conventions**

This modular design lets you define multiple modes for different project types, all coexisting inside the same ForgeFoundary installation.

---

## Creating a New Mode

To create a new mode skeleton:

```bash
ForgeFoundary create-mode MyNewMode
```

This generates a folder in `Modes/MyNewMode` with a ready-to-configure structure. From here, you can define every aspect of your scaffolding logic.

---

## Mode Structure Overview

Each mode is split into multiple sections. Every section has its own dedicated documentation inside `Docs/ModeSections/`.

### 1. Mode Metadata

Holds general information about the mode: name, version, description, language, and author.

**Docs:** [Mode Metadata](ModeSections/Mode_Metadata.md)

### 2. Mutators

Define reusable data snippets to share values across multiple configuration keys.

**Docs:** [Mutators](ModeSections/Mutators.md)

### 3. Component

Defines the main folder for your generated architecture and the name of the component.

**Docs:** [Component](ModeSections/Component.md)

### 4. Templates

Contains template definitions, default file behavior, and placeholder management.

**Docs:** [Templates](ModeSections/Templates.md)

### 5. Directories

Lists directories to generate within the component. Can include subdirectories and units mapping.

**Docs:** [Directories](ModeSections/Directories.md)

### 6. Units

Fundamental units of scaffolding (e.g., CRUD, Auth, Logic). Each unit can link to specific directories and templates.

**Docs:** [Units](ModeSections/Units.md)

### 7. Naming Conventions

Defines rules for file, directory, component, and unit naming. Supports defaults and overrides.

**Docs:** [Naming Conventions](ModeSections/Naming_Conventions.md)

### 8. Commands

Pre- and post-scaffold hooks for custom logic or automation.

**Docs:** [Commands](ModeSections/Commands.md)

### 9. CLI Flags

Maps mode configuration to command-line options, allowing flexible runtime overrides.

**Docs:** [CLI Flags](ModeSections/CLI_Flags.md)

---

> Each section is fully configurable via YAML, letting you create a mode that exactly fits your workflow.

For a complete guide on building modes, see the section-specific docs in `Docs/ModeSections/`.
