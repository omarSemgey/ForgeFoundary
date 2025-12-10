# Directories Section

## Overview

The **Directories** section allows you to define all the folders you want ForgeFoundary to create during scaffolding. This section is **optional**, but useful for ensuring your project has the exact folder structure you need.

---

## Keys

```yaml
directories: []
```

## Explanation

* `directories` – An array of folder paths relative to your scaffolding root. ForgeFoundary will create each directory listed here automatically.

## Example:

```yaml
directories:
  - src/Controllers
  - src/Models
  - tests
  - public/assets
```


> The names of directories can also follow a **naming convention** defined in the [Naming Convention section](Naming_Conventions.md) to keep your project structure consistent.
