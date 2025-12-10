# Documentation Map

This file serves as a central map for all documentation in the `Docs/` folder.  

## Getting Started

- [Installation Instructions](Installation.md) – Describes requirements (PHP & Composer) and provides installation scripts for Unix and Windows.
- [Getting Started](Getting_Started.md) – Overview of ForgeFoundary, first steps, and initial setup.
- [Creating Modes](Creating_Modes.md) – Guide for creating custom scaffolding modes.

---

## Commands Documentation

- [Scaffold Command](Commands/Scaffold.md) – Main scaffolding command to generate project structure.
- [Create Mode Command](Commands/CreateMode.md) – Command to create a new mode scaffold.
- [Dry Run Command](Commands/DryRun.md) – Simulate scaffolding without changing real files.
- [Dumb Mode Command](Commands/DumbMode.md) – Dump configuration values for a single mode.
- [Dumb Modes Command](Commands/DumbModes.md) – List all available modes and their metadata.

- [Commands Reference](Commands.md) – Overview of available commands and their purposes.

---

## Mode Sections

Each mode in ForgeFoundary can include optional and required sections to define its behavior. These sections are documented individually:

- [Mode Metadata](ModeSections/Mode_Metadata.md) – Basic metadata for a mode (name, version, author, description, language).
- [Mutators](ModeSections/Mutators.md) – Define reusable anchors for values and link them to CLI flags.
- [CLI Flags](ModeSections/CLI_Flags.md) – Optional feature to modify values via terminal using mutators.
- [Component](ModeSections/Component.md) – Core section defining the component path and name. **Required**.
- [Directories](ModeSections/Directories.md) – Optional folder structure definitions.
- [Commands](ModeSections/Commands.md) – Optional pre/post scaffolding commands.
- [Templates](ModeSections/Templates.md) – Optional templates section with defaults, overrides, and supported engines.
- [Units](ModeSections/Units.md) – Optional units system for advanced scaffolding logic.
- [Naming Conventions](ModeSections/Naming_Conventions.md) – Optional section to enforce naming styles and apply exceptions.
- [Enabling or Disabling Sections](ModeSections/Enabling_or_Disabling_Sections.md) – Control which sections are processed during scaffolding.

---

> This map will continue to grow as more documentation is added, providing a single reference point for all ForgeFoundary features.
