# Laravel DDD Mode – Documentation

## Overview

The **Laravel DDD Mode** for ForgeFoundary scaffolds domain-driven Laravel projects.
It automatically generates:

* Domain directories
* Class templates (Controllers, Models, Services, etc.)
* Naming conventions for files, directories, and templates

This mode is designed for a **clean Laravel DDD architecture**, keeping entities, services, requests, and routes separated and standardized.

---

## Directories

The following directories are generated for each domain:

| Directory       | Purpose                                          | Example                                       |
| --------------- | ------------------------------------------------ | --------------------------------------------- |
| `Controllers`   | Handles HTTP requests and endpoints              | `CommentController.php`                       |
| `DTOs`          | Data transfer objects for structured data        | `CommentDTO.php`                              |
| `Factories`     | Model factories for generating fake data         | `CommentFactory.php`                          |
| `Migrations`    | Database table definitions                       | `2025_12_12_000000_create_comments_table.php` |
| `Models`        | Eloquent models representing entities            | `Comment.php`                                 |
| `Policies`      | Authorization logic                              | `CommentPolicy.php`                           |
| `Providers`     | Domain-specific service providers                | `CommentServiceProvider.php`                  |
| `Repositories`  | Data access layer                                | `CommentRepository.php`                       |
| `Requests`      | Form request validation classes                  | `CommentStoreRequest.php`                     |
| `Routes/Api`    | API route definitions                            | `Routes/Api.php`                              |
| `Seeders`       | Seeders for generating fake data in the database | `CommentSeeder.php`                           |
| `Services`      | Domain-specific business logic                   | `CommentService.php`                          |
| `Tests/Feature` | Feature tests                                    | `Feature/...`                                 |
| `Tests/Unit`    | Unit tests                                       | `Unit/...`                                    |

---

## Templates

Templates are stored in:

```
App/Src/Modes/laravel-ddd/Templates/
```

Here’s the table without the Singular/Plural column:

| Template                   | Output Example                                | Notes                                |
| -------------------------- | --------------------------------------------- | ------------------------------------ |
| `controller.mustache`      | `CommentController.php`                       | One controller per entity            |
| `dto.mustache`             | `CommentDTO.php`                              | DTO for single entity                |
| `model.mustache`           | `Comment.php`                                 | Represents a table row               |
| `policy.mustache`          | `CommentPolicy.php`                           | Authorization logic for entity       |
| `service.mustache`         | `CommentService.php`                          | Domain business logic                |
| `serviceprovider.mustache` | `CommentServiceProvider.php`                  | Registers services                   |
| `repository.mustache`      | `CommentRepository.php`                       | Data access layer                    |
| `storerequest.mustache`    | `CommentStoreRequest.php`                     | Validates create requests            |
| `updaterequest.mustache`   | `CommentUpdateRequest.php`                    | Validates update requests            |
| `factories.mustache`       | `CommentFactory.php`                          | Generates fake model data            |
| `seeders.mustache`         | `CommentSeeder.php`                           | Seeds multiple database rows         |
| `routes.mustache`          | `Api.php`                                     | Holds API routes for the domain      |

---

## Usage

### Dry-run (preview)

```bash
ForgeFoundary dry-run --mode=laravel-ddd
```

* Shows what files and directories would be generated.
* Applies all naming conventions.
* Does not write to disk.

### Scaffold domain

```bash
ForgeFoundary scaffold DomainName --mode=laravel-ddd
```

* Creates the full domain folder structure.
* Generates templates with correct names and conventions.
* Creates directories, seeders, migrations, requests, routes, etc.

### CLI Overrides

You can override defaults per command:

```bash
ForgeFoundary scaffold DomainName \
  --custom=--component-path="app/Domains" \
  --custom=--component-name="Comment"
```