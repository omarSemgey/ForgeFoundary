# ForgeFoundary Configuration

ForgeFoundary relies on a configuration file and a user-editable **Modes directory** to define project scaffolding behavior.

---

## Configuration File

The main configuration file is: **forgefoundary.yaml**


**Location:** user-specific config directory (created on first run).

**Contents example:**

```yaml
# ==============================================
# Forge Foundary Configuration (v1.0)
# ==============================================

modes_path: Modes       # Absolute path OR relative to the user config directory
mode: laravel-ddd       # Default selected mode
```

### Fields

| Field        | Description                                                                                                                                   |
| ------------ | --------------------------------------------------------------------------------------------------------------------------------------------- |
| `modes_path` | The path to the **Modes** directory. Can be absolute or relative to the user config directory. This is where all editable project modes live. |
| `mode`       | The currently selected scaffolding mode. ForgeFoundary will use this mode by default when generating projects.                                |


---

## Modes Directory

The **Modes directory** contains all scaffolding modes. Each mode is a folder with templates, configuration.

**Default locations based on OS:**

| OS      | Default Modes Path                                                                                            |
| ------- | ------------------------------------------------------------------------------------------------------------- |
| Linux   | `$XDG_CONFIG_HOME/forgefoundary/Modes` <br>or `~/.config/forgefoundary/Modes` if `XDG_CONFIG_HOME` is not set |
| macOS   | `~/Library/Application Support/ForgeFoundary/Modes`                                                           |
| Windows | `%USERPROFILE%\AppData\Local\ForgeFoundary\Modes`                                                             |


> On first run, ForgeFoundary copies the default modes shipped with the tool to your user-specific **Modes directory**, so you can edit or add new modes without touching the installation folder.

---

## Changing the Default Mode

To change which mode is used by default:

1. Open your `forgefoundary.yaml` file.
2. Modify the `mode` field to the desired mode name.
3. Save the file. ForgeFoundary will now use this mode for all commands.

---

## Notes

* You can define `modes_path` as an **absolute path** if you want to store modes elsewhere.
* All edits to modes and templates are **user-local** and will not be overwritten during updates.
* This separation ensures you can update ForgeFoundary without losing your custom modes.
* You need to run the tool at least once for forgefoundary.yaml and Modes directroy to be created