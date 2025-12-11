## Enabling or Disabling Sections

Each optional section in a mode can be **enabled or disabled** using the `*_enabled` boolean key. By default, most sections are enabled (`true`), but you can turn off any section if you don’t want it processed during scaffolding.

### Example

```yaml
cli_flags_enabled: true         # Set to false to disable CLI flags entirely
commands_enabled: true          # Set to false to skip running pre/post commands
directories_enabled: true       # Set to false to skip creating directories
units_enabled: true             # Set to false to skip creating units
templates_enabled: true         # Set to false to skip creating templates
naming_conventions_enabled: true # Set to false to skip applying naming conventions
```

### Notes

* Setting a section to `false` completely **skips that system** during the scaffolding process.
* Even if the section is defined, ForgeFoundary will ignore it if the corresponding `*_enabled` flag is `false`.
* Disabling **directories** has a cascading effect:

  * `units` and `templates` will **not run** if `directories_enabled` is `false`, since they rely on the folder structure to exist.
* This gives you control to **reuse modes without running certain systems**, or to **temporarily disable parts** of a mode for testing.

> For example, if `cli_flags_enabled` is `false`, the CLI flags section will be ignored, even if you have mutators and flags defined.