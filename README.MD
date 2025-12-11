# ForgeFoundary

ForgeFoundary is a general-purpose scaffolding tool designed to help developers generate project structures, boilerplate code, and architectural patterns for any programming language or framework. Built with portability and flexibility in mind, it allows users to define custom "modes" that specify how projects should be scaffolded.

## Key Features

- Multi-language support: Scaffold projects in any language by defining your own modes.
- Customizable modes: Each mode defines directory structures, templates, naming conventions, and scaffolding rules.
- CLI-driven workflow: Easily scaffold components, directories, and units from the command line.
- Template engine support: Use Mustache, Twig, or Blade-style templates.
- Pre- and post-scaffold hooks: Run custom commands automatically before or after scaffolding.
- Portable and framework-agnostic: Not tied to any specific framework or programming style.

## How It Works

### ForgeFoundary reads configuration from YAML mode files, which define:

- Component paths and names
- Directory structures and units
- Template files, placeholders, and extensions
- Naming conventions (PascalCase, camelCase, etc.)
- CLI flags and overrides for flexible scaffolding

## Philosophy

ForgeFoundary is designed for developers who value automation, consistency, and flexibility in project setup. By separating scaffolding logic into configurable modes, it ensures repeatable and predictable project structures, no matter the language, framework, or team.

## Further Instructions

For detailed instructions, examples, and a map of available documentation, refer to [Docs/Manual](Docs/Manual.md). This file provides an organized overview of all other documentation files included with ForgeFoundary.