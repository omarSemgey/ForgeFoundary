# Commands in ForgeFoundary

ForgeFoundary exposes a set of **commands** that allow you to interact with the tool, run scaffolding tasks, and manage modes. Commands are modular and extensible, and each has its own dedicated documentation in `Docs/Commands/`.

---

## Available Commands

* **ScaffoldCommand** – Executes the scaffolding process for a specified mode, generating directories, templates, and units.

  **Docs:** [ScaffoldCommand](Commands/Scaffold.md)

* **CreateModeCommand** – Generates a new mode skeleton for you to configure.

  **Docs:** [CreateModeCommand](Commands/CreateMode.md)

* **DryRunCommand** – Simulates scaffolding without creating files, so you can preview your setup.

  **Docs:** [DryRunCommand](Commands/DryRun.md)

* **DumbModesCommand** – Lists all available modes in a simple, readable format.

  **Docs:** [DumbModesCommand](Commands/DumbModes.md)

* **DumbModeCommand** – Shows details for a single mode, including directories, templates, and units.

  **Docs:** [DumbModeCommand](Commands/DumbMode.md)

---

For detailed usage and examples, see the individual command docs inside `Docs/Commands/`.
