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

- **Data foundation done, wiring in progress**: Fase 1 (models, migrations, factories, seeders for `guru`, `kelas`, `siswa`, `nilai`, `jadwal`, `mata_pelajaran`, `tahun_ajaran`, `kelas_siswa`) is committed. `dashboard`, `data-siswa`, `input-nilai`, and `rekap-nilai` now render real DB data; `laporan` still renders hardcoded `@php` arrays. `nilai_akhir` (0.7×formatif + 0.3×sumatif) and `status_kelulusan` (KKM 75) are computed in `Nilai::saving`; `Nilai::predikat()` (A–E) is display-only for rekap/laporan; `Nilai::hitungRanking()` (standard competition: 1, 2, 2, 4) is shared by the rekap component and CSV export.
- Auth is live: username/password login via `Auth::attempt`, `auth` middleware on all feature routes, `guest` middleware on `/login`, `POST /logout`. Feature routes are grouped under `auth`; `dashboard` is served by `DashboardController`, the rest are still closures. Names: `dashboard`, `data-siswa`, `input-nilai`, `rekap-nilai`, `rekap-nilai.export` (CSV stream via `RekapExportController`), `laporan`, `login`, `logout`.
- Login is a Livewire 4 **full-page component**: `resources/views/pages/auth/⚡login.blade.php` (the `⚡` filename prefix is the full-page component convention), routed via `Route::livewire('/login', 'pages::auth.login')`. Referenced in tests as `Livewire::test('pages::auth.login')`.
- Interactive tables use Livewire **single-file components** in `resources/views/components/` (e.g. `⚡siswa-table.blade.php`, `⚡input-nilai-table.blade.php` with `#[Computed]` properties, `#[Url]` query-synced filters, `WithPagination`). Computed properties are accessed as `$this->propertyName` in the SFC markup (not bare `$propertyName`). `php artisan make:livewire` defaults to this SFC format. Embed with `<livewire:component-name />`.
- CRUD pattern (`⚡siswa-table.blade.php`): modal form via `<x-modal>` + `$this->dispatch('open-modal', id: '...')` (Modal/ConfirmDialog listeners accept string or `{id|name}` detail); delete confirmation via `<x-confirm-dialog on-confirm="methodName">`. Never put `@if` or `{!! !!}` echoes inside a component tag's attribute area — Blade mis-compiles them; wrap whole tags in `@if` instead.
- Layout split (easy to get wrong): the main app shell is `resources/views/layout/app.blade.php` (**singular** `layout/`) and feature pages `@extends('layout.app')`; `resources/views/layouts/` (plural) holds the `app`/`guest` layouts used by auth pages.
- `resources/css/app.css` defines a Tailwind v4 `@theme` design system (semantic colors, radii, shadows) plus typography utilities, but existing views style with hardcoded hex arbitrary values (`#15803d` green accent, `#f3f6fa` canvas, `#dfe6ef` borders). Match the sibling files.
- **Never add named tokens to the `--spacing-*` namespace in `@theme`** (e.g. `--spacing-lg: 24px`). Tailwind v4 resolves `max-w-*`/`min-w-*` against the spacing namespace first, so `max-w-2xl` would become 48px instead of 42rem — this silently shrank every modal/toast to a sliver. The 4px grid already works via Tailwind's default `--spacing: 0.25rem` (p-1=4px, p-4=16px). Also avoid `*/` sequences inside CSS comments (they close the comment early and break `npm run build`).

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
