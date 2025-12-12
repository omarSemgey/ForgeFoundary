# Naming Conventions

ForgeFoundary supports **automatic naming transformations** for components, directories, templates, and placeholders. Naming conventions are applied in **the order they are listed**.

## Structure

Each section of the naming conventions configuration has the following structure:

```yaml
section_name:
  defaults: [array of naming conventions]
  overrides:
    key_to_override: [array of naming conventions]
```

* **defaults** – the naming conventions applied to all items in this section by default.
* **overrides** – specific keys (like a template filename or a placeholder name) that use custom naming conventions instead of the defaults. The defaults **do not apply** to overridden items.

> Naming conventions are applied sequentially, from left to right in the array.

### Example Sections

```yaml
component:
  defaults: [pascal_case]

directories:
  defaults: [pascal_case]

templates:
  defaults: [singular, pascal_case]
  overrides:
    model.mustache: [singular, pascal_case]
    routes.mustache: [kebab_case]

templates_placeholders:
  overrides:
    domain_name: [snake_case]
```

* `component` – transforms component names.
* `directories` – transforms directory names.
* `templates` – transforms the generated file names based on templates.
* `templates_placeholders` – transforms placeholder values used inside templates.

---

## How It Works

1. When generating a value (component, directory, template, or placeholder), ForgeFoundary checks for an **override** first.
2. If an override exists, only the override’s naming conventions are applied.
3. If no override exists, the **defaults** are applied in order.
4. Naming conventions are applied **sequentially**; for example: `[singular, snake_case]` will first singularize, then convert to `snake_case`.

---

## Available Naming Conventions

| Key                    | Description                               |
| ---------------------- | ----------------------------------------- |
| `plural`               | Converts value to plural form.            |
| `singular`             | Converts value to singular form.          |
| `camel_case`           | Converts value to `camelCase`.            |
| `pascal_case`          | Converts value to `PascalCase`.           |
| `snake_case`           | Converts value to `snake_case`.           |
| `kebab_case`           | Converts value to `kebab-case`.           |
| `upper_snake_case`     | Converts value to `UPPER_SNAKE_CASE`.     |
| `dot_case`             | Converts value to `dot.case`.             |
| `studly_case`          | Converts value to `StudlyCase`.           |
| `title_case`           | Converts value to `Title Case`.           |
| `sentence_case`        | Converts value to `Sentence case`.        |
| `screaming_kebab_case` | Converts value to `SCREAMING-KEBAB-CASE`. |
| `slash_case`           | Converts value to `slash/case`.           |
| `backslash_case`       | Converts value to `backslash\case`.       |
| `dot_kebab_case`       | Converts value to `dot-kebab-case`.       |
| `flat_case`            | Converts value to `flatcase`.             |
| `train_case`           | Converts value to `Train-Case`.           |
