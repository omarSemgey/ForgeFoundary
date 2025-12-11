# Templates

## Overview

The **Templates** section defines how your mode handles **file templates**. This section is optional, but it’s a core part of flexible scaffolding. It allows you to define **default template behavior**, **per-template overrides**, and the template engines you want to use.

> ⚠️ Currently supported template engines: **Mustache**, **Twig**, and **Blade**.
> ⚠️ Units rely on directories being created. If `directories_enabled` is `false`, units will be skipped during scaffolding.

---

## Keys

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

* Sets default properties for all templates in your mode.
* Example:

```yaml
templates:
  defaults:
    file_name: defaultFileName
    file_extension: php
    placeholders:
      component_name: *component_name_mutator
```

*Placeholders* can reference mutators or static values.

### `overrides`

* Allows you to override defaults for specific templates.
* Example:

```yaml
templates:
  overrides:
    Template.mustache:
      file_extension: js
      file_name: CustomTemplateName.js
      placeholders:
        customPlaceholder: overrideCustomPlaceholderValue
```

*Overrides* are useful when you need specific templates to differ from the defaults while keeping your main configuration clean.

---

This section is optional, but it gives you powerful control over **how templates are generated and customized per mode**.