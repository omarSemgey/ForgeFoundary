# Naming Conventions

## Overview

The **Naming Convention** section allows you to **enforce consistent naming styles** (e.g., camelCase, PascalCase) across your mode’s components, directories, units, templates, and template metadata. This helps keep your project organized and readable.

You can define **global rules** using `defaults` and **exceptions** using `overrides`.

---

## Keys

```yaml
naming_conventions_enabled: true
naming_conventions:
  camel_case:
    component: true
    directories:
      defaults: true
      overrides: []
    units:
      defaults: true
      overrides: []
    templates:
      defaults: true
      overrides: []
```

### Explanation

* `naming_conventions_enabled` – Boolean flag to enable or disable naming convention processing.
* `naming_conventions` – Object containing different naming styles (camel_case, pascal_case, snake_case, etc.).
* Each style contains:

  * `component` – Whether components should follow this style.
  * `directories` – `defaults` applies the style globally; `overrides` lists directories that differ.
  * `units` – Same as directories but for units.
  * `templates` – Same as directories but for templates.

---

## How Overrides Work

* **When `defaults: true`** – All items follow the naming convention except the ones listed in `overrides`.
* **When `defaults: false`** – Items in `overrides` **do** follow the convention, while everything else ignores it.
* The first naming convention to match an element will be applied; subsequent conventions are ignored.

---

## Examples

```yaml
naming_conventions:
  camel_case:
    component: true
    directories:
      defaults: false
      overrides: 
        - Controllers
    units:
      defaults: true
      overrides: []
    templates:
      defaults: true
      overrides: []

  pascal_case:
    component: false
    directories:
      defaults: true
      overrides: []
    units:
      defaults: true
      overrides: []
    templates:
      defaults: false 
      overrides: 
        - Template.mustache
```

**Interpretation:**

* Components follow **camelCase**.
* All directories follow **PascalCase** except `Controllers`, which uses camelCase.
* Units use **defaults: true** for camelCase but are not overridden, so they remain camelCase.
* Templates mostly follow camelCase, except `Template.mustache`, which uses PascalCase.
* The first convention applied to an element (camelCase or PascalCase) is the one that takes effect.

---

### Notes

* Naming conventions are optional, but they help maintain a consistent style in larger projects.
* Overrides give precise control, allowing exceptions to global rules.
* Units and templates can be excluded from naming conventions if desired.