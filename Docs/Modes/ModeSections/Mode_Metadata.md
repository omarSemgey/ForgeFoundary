# Mode Metadata

## Overview

The **Mode Metadata** section defines basic information about your mode. It helps you organize your modes and is used by the `dumb-mode` and `dumb-modes` commands to display mode details in the terminal.

This section is optional but highly recommended for clarity and maintainability.

---

## Metadata Keys

```yaml
mode_metadata:
  name: 
  version: 
  description: >
    
  language: 
  author: 
```

### Key Details

* **name** – The human-readable name of the mode.
* **version** – The current version of your mode (e.g., `1.0.0`).
* **description** – A short description of what the mode does. Use the `>` YAML syntax to allow multi-line descriptions.
* **language** – The primary programming language or framework this mode targets.
* **author** – Your name or the person who created the mode.

---

## Usage

* The metadata is **automatically displayed** when running:

```bash
ForgeFoundary dumb-modes
ForgeFoundary dumb-mode --mode=YourModeName
```

* It helps **organize multiple modes** in large projects, making it easier to understand what each mode does at a glance.

---

## Notes

* Keep metadata concise but informative.
* You can omit keys if they are not relevant, but `name` is recommended.
* You can even add more metadata keys if you want to.