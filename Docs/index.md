# ForgeFoundary

ForgeFoundary is a flexible, framework-agnostic scaffolding tool for any programming language or architecture.

---

## Key Features

- **Multi-language support:** Scaffold projects in any language by defining your own modes instead of relying on built-in presets.
- **Customizable modes:** Control directory structures, templates, naming conventions, and scaffolding rules from simple configuration files.
- **CLI-driven workflow:** Easily scaffold components, directories, and units from the command line.
- **Template engine support:** Use Mustache, Twig, or Blade-style templates depending on your workflow.
- **Pre- and post-scaffold hooks:** Run custom commands automatically before or after scaffolding.
- **Portable & framework-agnostic:** ForgeFoundary does not assume any framework and can be used across different projects.

---

## Quick Start

```bash
# Scaffold a new project
ForgeFoundary scaffold --mode=laravel-ddd
```

---

## How It Works

ForgeFoundary reads YAML mode files to define:

* Component paths and names
* Directory structures and units
* Template files, placeholders, and extensions
* Naming conventions (PascalCase, camelCase, etc.)
* CLI flags and overrides for flexible scaffolding

---

## Philosophy

ForgeFoundary is designed for developers who value **automation, consistency, and flexibility** in project setup. Modes separate scaffolding logic from code, ensuring **repeatable and predictable project structures**.

---

[Explore Documentation →](Manual.md)