# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository layout

This repo root is **not** the Laravel application root. The Laravel app lives in `system/`:

```
/                       repo root (deploy artifact wrapper)
├── index.php           front controller — requires system/vendor/autoload.php and system/bootstrap/app.php
├── .htaccess            rewrites all requests to index.php
├── assets/              public web assets served directly (build output, uploads)
└── system/              the actual Laravel 13 application — composer.json, app/, routes/, etc.
```

All Composer/Artisan/npm work happens inside `system/`. Run commands from there, e.g. `cd system && php artisan ...`.

`system/CLAUDE.md` is auto-generated/maintained by Laravel Boost (`php artisan boost:update`) — don't hand-edit it; it already documents PHP/Pint/Pest conventions and Boost MCP tool usage in detail. This file covers what Boost's output doesn't: repo layout and the module/panel architecture.

## Common commands

Run from `system/`:

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan permission:sync

composer dev            # serve + queue:listen + vite dev, concurrently
npm run dev             # vite only
npm run build            # production asset build

php artisan test --compact                       # full suite
php artisan test --compact --filter=testName      # single test
php artisan test app/Modules/Products/Tests/Feature/ProductsModuleTest.php

vendor/bin/pint --dirty --format agent            # format changed PHP files (required after PHP edits)
```

Module lifecycle:

```bash
php artisan make:module Invoice --panels=admin [--type=crud|settings] [--api]
php artisan module:validate {Module?}
php artisan module:list
php artisan module:enable {Module} / module:disable {Module}
php artisan module:cache / module:cache:clear
php artisan permission:sync            # sync module-declared permissions into Spatie (alias: module:sync-permissions)
php artisan module:sync-packages --write   # sync module.json "packages" declarations into root composer.json
php artisan panel:list / make:panel {Name} [--custom-components]
```

## Architecture

This is a modular monolith admin-panel boilerplate. Two structural concepts, kept strictly separate:

- **Panels** (`app/Panels/{Admin,User}`) are shell layers only: auth, dashboard, profile, 2FA, topbar/sidebar chrome. Never put feature/CRUD logic here.
- **Modules** (`app/Modules/{Name}`) are self-contained feature layers, each with its own controllers, routes, views, lang files, tables, services, and tests. All CRUD/settings/content screens belong in a module, not a panel.

### Module anatomy

```
app/Modules/{Name}/
  module.json                          static metadata: name, alias, version, providers, requires, active, optional packages
  Module.php                           behavior descriptor: id(), permissions(), policies(), {panel}Navigation()
  Providers/{Name}ServiceProvider.php
  Models/                              Services/                Policies/
  Tables/{Name}Table.php               schema-driven table definition
  Http/Controllers/{Panel}/            Http/Requests/
  Routes/{panel}.php                   e.g. Routes/admin.php
  Resources/views/{panel}/             Resources/lang/{locale}/messages.php
  Database/{Migrations,Seeders}
  Tests/{Feature,Unit}/
```

- Module discovery/state is runtime-driven via `ModuleRegistry`. Enable/disable overrides live in `storage/app/module-state.json`; the resolved graph is cached in `bootstrap/cache/modules.php`. Precedence: `module.json` default → runtime override file → cached graph.
- Permissions and navigation for module features are declared in the module's `Module.php`, not in central config. `config/panels.php` and `config/permissions.php` are for shell-level concerns only (panel registration, guard/middleware/theme, role compatibility data).
- One active Composer manifest for the whole monolith: root `system/composer.json`. Do not create per-module `composer.json`. A module can *declare* vendor package needs via `module.json`'s `packages` key; `php artisan module:sync-packages --write` merges those into root composer.json.
- `php artisan make:module` only ever writes inside `app/Modules/{Name}` — it won't touch panels or shared config.
- Use kebab-case route names for custom actions (`toggle-status`, `bulk-toggle-status`).

### Tables (list pages)

Default approach for new CRUD modules is schema-driven, not hand-written Blade tables:

- Define the schema in `Tables/{Module}Table.php` using `TableDefinition`/`TableColumn`/`TableAction`/`TableBulkAction`/`TableFilters` (`App\Modules\Shared\Support\Tables\*`).
- Controller exposes it via `tableDefinition(Request $request): ?TableDefinition` (from `HasCrudActions`).
- Render with `<x-tables.resource :definition="$table" :items="$items" />`.
- Legacy manual rendering (`rowsView()` + `_table-rows.blade.php`) is still supported as an escape hatch for genuinely custom page shapes, but schema tables are the default for anything new. See `system/docs/datatable.md` for the full column/action API and escape hatches (custom header/row/cell views, custom filter partials).

### Panels config

`config/panels.php` is the panel registry — one array entry per panel controlling prefix, guard, middleware stack, theme, and shell navigation. Multi-guard auth: `admin` guard → `admins` table (`/admin/login`), `web` guard → `users` table (`/login`). Adding a panel = one config entry + `make:panel`; removing = delete the entry and the `app/Panels/{Name}` folder.

### Components

Shared Blade components live in `system/resources/views/components`; panel-specific overrides go in `resources/views/panels/{panel}/components/...`. Check `system/docs/components.md` for the full inventory (layouts, tables, forms, ui, navigation) before writing a new one.

### Public frontend theming (page builder)

Separate from panel shell theming above: the `Frontend` module (`app/Modules/Frontend`) renders public `Page`s through swappable theme skins declared in `config/frontend-themes.php`, each with its own layout views, CSS/JS, and settings schema. The current (and only) theme, `leadatlas`, is ported from `template-integration/demo-html` and owns every view it needs — layouts, nav, section renderers, fallback — with no dependency on `resources/views/frontend/shared/*`. Its homepage is a real `FrontendSection`-composed `Page`, using theme-specific `homepage_*` section types (hero, stages, features, voices, pricing, faq, cta) for content that doesn't fit the generic section types. See `system/docs/frontend-themes.md` for the render pipeline, section-type registry, and how to add a theme or section type.

### Canonical docs (in `system/docs/`)

- `developer-guide.md` — onboarding, daily workflow
- `modules.md` — module runtime, contracts, generator, lifecycle commands (canonical)
- `datatable.md` — schema table system, full column/action API
- `components.md` — Blade component reference
- `frontend-themes.md` — public frontend theme/page-builder system (render pipeline, theme + section registries)
- `app/Modules/README.md` — quick module ops summary
