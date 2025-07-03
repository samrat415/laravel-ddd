
# Laravel DDD Toolkit 🧱

[![Total Downloads](https://img.shields.io/packagist/dt/samrat415/laravel-ddd?style=for-the-badge&color=brightgreen)](https://packagist.org/packages/samrat415/laravel-ddd)
> A Laravel package to scaffold and register Domain Driven Design (DDD) modules for APIs and Livewire frontends.

---

## Features

- Auto-registers `domains/`, `src/`, and `frontend/` PSR-4 namespaces
- Registers service providers automatically
- Artisan commands to setup folder structures and modules
- Supports portal-based domain APIs and frontend modules
- Optional interactive prompts for portal creation

---

## Installation

```bash
composer require samrat415/laravel-ddd
````

---

## Autoload

You do **not** need to manually add namespaces to composer.json — the package does it for you automatically.

---

## Commands

### 1. Install base folders and portals

```bash
php artisan ddd:install
```

* Creates base folders: `domains/`, `src/`, `frontend/` (if missing)
* Prompts to add portals for domains and frontend

---

### 2. Create a new domain module

```bash
php artisan ddd:domain {portal/module}
```

* Creates structure inside `domains/{portal}/{module}`, e.g.:

```
domains/Admin/Users/
├── Api/
├── Resources/
└── routes/v1.php
```

* If portal is omitted, prompts to select or create one.

---

### 3. Create a new src module

```bash
php artisan ddd:src {module}
```

* Creates structure inside `src/{module}`, e.g.:

```
src/Admin/
├── Controllers/
├── DTO/
├── Enums/
├── Livewire/       # only if livewire_support config enabled
├── Models/
├── Routes/web.php
├── Service/
└── Views/
```

* Does not ask for portal, just creates the module folders.

---

## Folder Overview

```
domains/      # API domains organized by portal
frontend/     # Livewire & Blade UI modules by portal
src/          # Core business logic and services
```

---

## License

MIT License © [samrat415](https://github.com/samrat415)

---

## Ongoing Updates

This package is actively evolving. Upcoming features include a Filament-like, but simpler, module generator that:

- Creates basic CRUD modules
- Follows Domain Driven Design (DDD) patterns
- Uses Livewire components
- Works with your existing database tables

Stay tuned for more improvements and new tools to speed up your Laravel DDD development!