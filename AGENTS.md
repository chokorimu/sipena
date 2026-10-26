# SIPENA

School administration dashboard (Sistem Penilaian) for MTs Negeri Ambon — student data, grade entry, grade recap, and reports. Laravel 13 + Livewire 4 + Tailwind CSS 4, MySQL, UI text in Indonesian.

## Stack & environment

- PHP 8.5 runtime (`composer.json` requires `^8.3`), Laravel 13, Livewire 4, Tailwind CSS 4 via Vite, blade-ui-kit/blade-heroicons.
- Database is MySQL (`local-sipena-db`, root, no password — see `.env`). Local dev uses `database` drivers for session/cache/queue, so run `php artisan migrate` after fresh setup.
- Tests run against a **separate MySQL database** `local-sipena-db_testing` (configured in `phpunit.xml`) — create it once with `CREATE DATABASE local-sipena-db_testing` if missing. Never point tests at the dev database; `RefreshDatabase` wipes it.
- Note: `php artisan db:show` fails on this MariaDB install (`performance_schema.session_status` missing) — harmless quirk, use `database-query` instead.

## Commands

- Setup: `composer setup` (install, key:generate, migrate, npm install, npm run build).
- Dev: `composer dev` (or `npm run dev` alongside `php artisan serve`).
- Build assets: `npm run build` — required after frontend changes or Vite manifest errors appear.
- Tests: `php artisan test --compact`; single test: `php artisan test --filter=testName` or pass a file path; `vendor/bin/phpunit` accepts the same args.
- Format PHP: `vendor/bin/pint --dirty --format agent` — run before finalizing any PHP change.
- Artisan: always pass `--no-interaction`.

## Architecture & current state

- **UI prototype stage**: the five feature pages (`dashboard`, `data-siswa`, `input-nilai`, `rekap-nilai`, `laporan`) render hardcoded `@php` arrays inside the Blade templates. Buttons, filters, search, and pagination are non-functional. No `Siswa`/`Guru`/`Nilai`/`Kelas`/`Mapel` models or migrations exist yet — only Laravel defaults (users, cache, jobs).
- Routes are plain closures returning views in `routes/web.php`; names: `dashboard`, `data-siswa`, `input-nilai`, `rekap-nilai`, `laporan`, `login`.
- Login is a Livewire 4 **full-page component**: `resources/views/pages/auth/⚡login.blade.php` (the `⚡` filename prefix is the full-page component convention), routed via `Route::livewire('/login', 'pages::auth.login')`. Auth logic is a stub.
- Layout split (easy to get wrong): the main app shell is `resources/views/layout/app.blade.php` (**singular** `layout/`) and feature pages `@extends('layout.app')`; `resources/views/layouts/` (plural) holds the `app`/`guest` layouts used by auth pages.
- `resources/css/app.css` defines a Tailwind v4 `@theme` design system (semantic colors, 4px spacing scale, radii, shadows) plus typography utilities, but existing views style with hardcoded hex arbitrary values (`#15803d` green accent, `#f3f6fa` canvas, `#dfe6ef` borders). Match the sibling files.

## Conventions

- Keep all user-facing UI text in Indonesian.
- ~36 custom Blade components live in `app/View/Components/` with views in `resources/views/components/` (Button, Card, CardStat, DataTable, Modal, Dropdown, Toast, Sidebar, Topbar, Tabs, form controls, etc.). Reuse them before writing new markup.
- Icons: `<x-heroicon-m-*>` (mini/solid) and `<x-heroicon-o-*>` (outline).
- PHP style: curly braces always, constructor property promotion, explicit return types and param type hints, TitleCase enum keys, PHPDoc over inline comments.
- Create files with `php artisan make:*`; when creating models, also create factories/seeders.
- Do not add new top-level directories or change composer/npm dependencies without approval.
- Do not create documentation files unless explicitly requested.

## Tooling

- Laravel Boost MCP is enabled (`opencode.json` runs `php artisan boost:mcp`). Prefer Boost tools (`database-query`, `database-schema`, `get-absolute-url`, `browser-logs`, `search-docs`) over shell equivalents.
- Domain skills in `.agents/skills/` (laravel-best-practices, testing-best-practices, tailwindcss-development, deploying-to-cloud, infer-conventions) — activate via the skill tool when working in those areas.
- Deploy target is Laravel Cloud (`boost.json` has `cloud: true`); use the deploying-to-cloud skill for deploy work.
- No CI workflows or pre-commit hooks exist — Pint and PHPUnit are the only local gates.
