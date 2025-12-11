# Getting Started with ForgeFoundary

## What is ForgeFoundary?

ForgeFoundary is a general-purpose programming scaffolder designed to help developers quickly set up project structures, templates, and reusable components. Unlike framework-specific tools, ForgeFoundary is **mode-based**, meaning you can create custom scaffolding "modes" for any programming language, framework, or project type.

It’s for developers who want **flexible, repeatable project scaffolding** without being tied to a specific ecosystem.

---

## Who is this for?

* Developers interested in **defining their own project structures** and automating repetitive setups.
* Anyone curious about creating **custom modes** for any programming workflow.

> ⚠️ You will need **PHP 8.2+** and **Composer** installed before using ForgeFoundary.

---

## After Installation

Once you’ve followed the instructions in [Installation Instructions](Installation.md) and successfully installed ForgeFoundary:

1. Verify the installation:

```bash
   ForgeFoundary list
```

   This will display available commands and options.

2. Test a scaffold run using the dry-run command:

```bash
   ForgeFoundary dry-run
```

   This simulates scaffolding without creating files, letting you preview your setup.

---

## Creating Custom Modes

### How to create a mode:

1. Generate a new mode skeleton:

```bash
   ForgeFoundary create-mode MyCustomMode
```

   This creates a folder in `Modes/` with all necessary files ready for configuration.

2. Edit your mode configuration YAML:

   * Define directories, templates, units, and naming conventions.
   * Optionally add CLI flags or pre/post commands.

3. Run your mode:

```bash
   ForgeFoundary scaffold --mode=MyCustomMode
```

   This will scaffold the structure based on your configuration.