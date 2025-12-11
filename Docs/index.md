# ForgeFoundary

ForgeFoundary is a general-purpose scaffolding tool designed to help developers generate project structures, boilerplate code, and architectural patterns for any programming language or framework.

---

## Key Features

- **Multi-language support:** Scaffold projects in any language by defining your own modes.
- **Customizable modes:** Define directory structures, templates, naming conventions, and scaffolding rules.
- **CLI-driven workflow:** Easily scaffold components, directories, and units from the command line.
- **Template engine support:** Mustache, Twig, or Blade-style templates.
- **Pre- and post-scaffold hooks:** Run custom commands automatically before or after scaffolding.
- **Portable & framework-agnostic:** Not tied to any specific language or framework.

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