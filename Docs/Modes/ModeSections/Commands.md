# Commands

## Overview

The **Commands** section lets you run custom commands **before or after scaffolding**. This is optional but useful for automating tasks like installing dependencies, generating autoloads, or running scripts right after scaffolding completes.

---

## Example

```yaml
commands: 
  # Pre-scaffold hooks
  before: []   

  # Post-scaffold hooks
  after:
    - composer dump-autoload
```

### How it works

* `before` – List of commands to run **before scaffolding** starts.
* `after` – List of commands to run **after scaffolding** finishes.

> Each command runs in the terminal context, so you can use any shell command that works on your system.