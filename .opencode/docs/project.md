# Project mechanics — Fleet Manager

> Loading flow, assets, translations, options framework, dependencies, general conventions.
> Read only when the task involves these areas.

## Overview

The "Fleet Manager" WordPress plugin (iWorks): manages sailboats, sailors,
regattas and results. Source files contain **literal placeholders**
(`PLUGIN_VERSION`, `BUILDTIME`, `PLUGIN_TILL_YEAR`, …) — they are replaced by
the bun bundler at build time (see `build.md`), never in the source.

The version lives only in `package.json` (`"version"`); the main file header
and the version constant both use the `PLUGIN_VERSION` placeholder.

## Directory structure

```
/
├── fleet.php                      # Plugin bootstrap (header, constants, activation)
├── etc/
│   └── options.php                # Options page configuration (iworks_options format)
├── includes/
│   ├── iworks.php                 # Shared iworks base class
│   └── iworks/
│       ├── class-iworks-fleet.php # Main plugin class
│       ├── fleet/                 # Feature classes (db, github, posttypes base)
│       │   ├── posttypes/         # One class per post type
│       │   └── integration/       # OpenGraph integration
│       ├── options/               # Vendored iworks options framework (git submodule)
│       └── rate/                  # Vendored iworks rate framework (git submodule)
├── assets/
│   ├── sass/{admin,frontend}/     # SCSS sources
│   ├── scripts/admin/src/         # Admin JS sources (concat order matters)
│   ├── scripts/admin/fleet*.js    # Compiled bundles (built)
│   ├── externals/                 # Bundled select2 + jQuery-UI datepicker
│   ├── templates/shortcode/       # Shortcode templates (crew, medals, results)
│   └── images/flags/              # Country flag SVGs
├── integration/themes/            # twentytwenty-5o5-child theme templates
├── languages/                     # fleet.pot (+ .po/.mo)
├── bundler/                       # Bun build system — see build.md
├── package.json                   # Version + release metadata (single source of truth)
└── readme.txt                     # WordPress.org readme
```

## Loading flow

1. WordPress loads `fleet.php` (textdomain `fleet`):
   - defines `IWORKS_FLEET_VERSION` (`'PLUGIN_VERSION'`) and
     `IWORKS_FLEET_PREFIX` (`'iworks_fleet_'`);
   - loads `etc/options.php`, the vendored `iworks_options` class and the
     main class; activation hook runs options activation + `db_install()`;
   - registers with the iWorks "rate" framework (`iworks-register-plugin` action).
2. `iworks_fleet::__construct()` instantiates `db`, `github` and the four
   post-type classes (ranking gated by the `load_ranking` option).
3. On `plugins_loaded`: OpenGraph integration when `iWorks_OpenGraph` exists,
   CSV download streaming, then `do_action( 'fleet/loaded' )`.

## Constants

- `IWORKS_FLEET_VERSION` — plugin version (`'PLUGIN_VERSION'` placeholder).
- `IWORKS_FLEET_PREFIX` — `iworks_fleet_` prefix for options and meta.
- Global: `$iworks_fleet_options` (options object), `$iworks_fleet` (main instance).

## Assets layer

- **CSS**: `assets/sass/{admin,frontend}/*.scss` → compiled to
  `assets/styles/{admin,frontend}/*.css` → concatenated to
  `assets/styles/fleet-admin.css` and `assets/styles/fleet.css` (+ `.min.css`).
- **JS**: `assets/scripts/admin/src/*.js` (fixed concat order:
  datepicker, boat, person, result, select2) →
  `assets/scripts/admin/fleet.js` → `.min.js`. Only enqueued on fleet admin
  screens; deps: jquery-ui-datepicker + select2 from `assets/externals/`;
  localized as `iworks_fleet` (`messages`, `nonces`, `user_id`).
- **Debug/dev mode**: `WP_DEBUG` → `$this->dev = ''` (unminified files);
  transient cache (`dingy-` prefix) bypassed under WP_DEBUG.
- Compiled files are build outputs — never edit them, edit the sources and rebuild.

## Translations (i18n)

- Textdomain: `fleet`; `languages/fleet.pot`; locales `pl_PL`, `uk`.
- `bun run make-pot` runs wp-cli `i18n make-pot` and `i18n update-po` for every
  existing `.po` file. `.mo` files are compiled only in `bun run build:github`.
- Special per-post-type JSON translations (e.g. `fleet-pl_PL-boat.json`) exist.

## Caching

Transient-based cache with `dingy-` prefixed keys
(`iworks_fleet_posttypes::cache()` helpers); disabled under `WP_DEBUG`.

## PHP dependencies

- No Composer. Requires (from `package.json`, replaced into headers at build):
  WordPress >= 5.0, PHP >= 8.0.
- Vendored frameworks live in `includes/iworks/` as git submodules:
  `options/` (settings pages) and `rate/` (rate-it notices). Both are
  separate git repos — treat them as vendor code: do not refactor their
  internals; update with `bun run update-libs`.

## General conventions

- Formatting: tabs for PHP/JS/SCSS, LF, final newline.
- Every PHP file: `if ( ! defined( 'WPINC' ) ) { die; }` (fleet style) plus a
  `class_exists` guard where applicable.
- File naming: kebab-case, `class-iworks-fleet-{name}.php`;
  class names: `iworks_fleet_{name}` (snake_case, lowercase).
- Hooks: `fleet/loaded`, `iworks_fleet_*` filters (meta values, series,
  post-type args, taxonomy registration); AJAX actions
  `iworks_fleet_persons_list`, `iworks_fleet_upload_races` (nonce'd).
- Never read the vendored frameworks' internals from feature classes.

## Known pitfalls (do not fix without approval)

- Source placeholders (`PLUGIN_VERSION`, `BUILDTIME`, `PLUGIN_TILL_YEAR`, …)
  are intentional — do not replace them with real values in the repo.
- Baked-in typos in meta/option names (`iworks_fleet_boot_owner_id`,
  `boad_add_social_media`) — renaming breaks stored data (see `classes.md`).
- The Grunt-era compiled `assets/styles/admin/post-type-person.css` had no
  SCSS source; the admin CSS bundle concat picks up whatever sits in
  `assets/styles/admin/`.
- `release/github/*` and `release/wporg/*` are build snapshots (gitignored) —
  never edit them directly.
