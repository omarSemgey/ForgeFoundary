# Templates

## Overview

The **Templates** section defines how your mode handles **file templates**. Templates are the building blocks that generate actual files in your projects. This section is optional, but it is a core part of flexible scaffolding.

Templates allow you to define:

* **Default template behavior** (file names, paths, extensions, placeholders)
* **Per-template overrides**
* **Template engine usage** (e.g., Mustache, Twig, Blade)
* **Placeholders**, including metadata placeholders
* **Multiple file paths for a single template**, enabling one template to generate multiple files

> ⚠️ Supported template engines: **Mustache**, **Twig**, **Blade**

> ⚠️ Templates rely on directories being created. If `directories_enabled` is `false`, templates may be skipped.

---

## Templates Environment Keys

```yaml
templates_path:          # Relative path to the Templates folder in your mode
templates_enabled:       # true/false to enable template processing
templates_require_existing_dirs:  # If true, only generate templates for directories that exist
template_engine_extensions: []    # Array of file extensions to treat as templates
```

### `templates_path`

* Path to your templates folder, relative to the scaffolder’s App/Src folder.
* Example: `Modes/MyMode/Templates/`

### `templates_enabled`

* Boolean flag to enable template processing.
* Example: `true`

### `templates_require_existing_dirs`

* Boolean flag; if `true`, templates will only generate files for directories that already exist.

### `template_engine_extensions`

* List the file extensions to be processed as templates.
* Example:

```yaml
template_engine_extensions:
  - mustache
  - twig
  - blade.php
```

---

## Templates Configuration

Templates can be defined with **defaults** and **overrides**:

```yaml
templates:
  defaults: {}
  overrides: {}
```

### `defaults`

* Applies default metadata and placeholders to all templates in the mode.
* Can include:

  ```yaml
  templates:
    defaults:
      file_name: defaultFileName
      file_extension: php
      file_paths: ["Models"]
      placeholders:
        component_name: *component_name_mutator
  ```

### `overrides`

* Allows specific templates to deviate from defaults.
* Each override must reference the full template filename.
* Example:

```yaml
templates:
  overrides:
    model.mustache:
      file_name: CustomModel
      file_extension: php
      file_paths:
        - "Models"
        - "Repositories"
      placeholders:
        domain_name: ExampleDomain
```

*Overrides replace the defaults for the specified fields. If a field is not overridden, the defaults are used.*

---

## Template YAML Metadata

Each template file can include a **YAML front matter section** at the top. This serves as the template's **file metadata** and allows defining **additional placeholders**.

Example:

```yaml
---
file_name: "MyFile_{{placeholder}}.php"
file_paths: ["Services", "Repositories"]
file_extension: php
---
```

### Key fields

* **`file_name`** – The name of the file to generate. Can reference placeholders.
* **`file_paths`** – Array of paths where this template will be generated. Paths are relative to the component or mode. You can define multiple paths to generate the same template in different locations.
* **`file_extension`** – The file extension to use.

> A file **must have a file name, a file extension and at least one file path** for ForgeFoundary to generate it. This can be defined either in the template file itself or in the mode configuration.

### Hierarchy of value resolution:
When ForgeFoundary generates a template, it determines values in this order:

Mode configuration overrides (templates.overrides.template_name) → highest priority

Template YAML section → overrides defaults from the mode configuration

Mode configuration defaults (templates.defaults) → lowest priority

⚠️ This hierarchy applies to both placeholders and metadata (like file_name, file_extension). For example, if file_name exists in the mode override, it will replace any file_name defined in the template YAML.

---

## Placeholders

**Placeholders** are variables that you reference inside templates. Their format depends on the template engine (e.g., `{{placeholder}}` for Mustache).

*Placeholders exist in three places:*

1. **Template metadata YAML**
   You can both define placeholders or use them in your template's YAML section Example:

```yaml
---
file_name: "{{MyFileNamePlaceholder}}"
custom_placeholder: "Hello"
---
```

2. **Template body**
   You can reference placeholders in the template content. Example (Mustache):

```mustache
class {{file_name}} {
    echo {{custom_placeholder}};
}
```

3. **Global or YAML-defined placeholders**
   These are defined in your mode configuration (`templates.defaults.placeholders`) or as overrides (`templates.overrides.template_name.placeholders`). Example:

```yaml
templates:
  defaults:
    placeholders:
      MyFileNamePlaceholder: MyFileName
```
> ⚠️ Note: File paths, unlike the other file metadata like file_extension and file_name, cannot be used as placeholders.

> ⚠️ Hierarchy reminder: Mode overrides > Template YAML > Mode defaults.

---

## Multiple File Paths

A single template can generate files in multiple directories. Example:

```yaml
file_paths:
  - "Services"
  - "Repositories"
```

* This will generate one file name in each path.
* Useful for templates that need to exist in multiple locations (e.g., DTOs, Repositories, or Service classes).

> The **file name remains the same** for all generated paths unless overridden with placeholders.

---

## Summary

* Templates are **metadata-driven** through YAML front matter.
* They support **placeholders** in both metadata and template content.
* Defaults and overrides allow centralized configuration or fine-grained per-template control.
* Templates can generate **multiple files at different paths** but with a single base name.
* Supported engines: **Mustache, Twig, Blade**.

This approach provides **maximum flexibility** while keeping template definitions **clear, modular, and reusable**.