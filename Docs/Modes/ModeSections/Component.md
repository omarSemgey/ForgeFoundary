# Component

## Overview

The **Component** section is the **only required section** in a mode. It defines the core folder that contains all of your scaffolding logic and templates. Every mode must have a component defined, as it is the foundation for all other sections.

---

## Keys

```yaml
component_path: path/to/component
component_name: MyComponentName
```

### `component_path`

* Absolute value or Relative to where you run the tool.
* Typically points to the folder where your templates, directories, and units reside.
* Can use a mutator to make it flexible for CLI overrides.

### `component_name`

* The name of your component.
* Can follow a **naming convention** (see [Naming Conventions](Naming_Conventions.md)) for consistency across modes.
* Can also use a mutator for CLI overrides or reusable configuration.

---

This section is the foundation of your mode, so all other mode sections will rely on this component as their base folder.
