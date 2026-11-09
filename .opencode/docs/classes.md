# Classes — Fleet Manager

> Reference for work on PHP classes: base class, main class, post types,
> custom DB tables, options, shortcodes, and the vendored iworks frameworks.
> Read only when the task involves these areas.

## Class hierarchy

```
iworks                                      (shared base, includes/iworks.php)
└── iworks_fleet                            (main class, instantiated in bootstrap)
    ├── objects['db']                       → iworks_fleet_db
    ├── objects['github']                   → iworks_fleet_github
    ├── objects['post_type_boat']           → iworks_fleet_posttypes_boat
    ├── objects['post_type_person']         → iworks_fleet_posttypes_person
    ├── objects['post_type_ranking']        → iworks_fleet_posttypes_ranking
    └── objects['post_type_result']         → iworks_fleet_posttypes_result
iworks_fleet_posttypes                      (shared post-type base, extends iworks)
└── iworks_fleet_posttypes_{boat,person,ranking,result}
iworks_fleet_integration                    (abstract integration base)
└── iworks_fleet_integration_og             (OpenGraph integration, loaded on demand)
```

## Base class (includes/iworks.php)

`iworks` — minimal shared foundation: `$dev` (`''` or `'.min'` depending on
`WP_DEBUG`), `$meta_prefix`, `$base` (`__DIR__`), `$dir`, `$version`.
All fleet classes extend it.

## Main class (class-iworks-fleet.php)

`iworks_fleet extends iworks` — instantiated once in `fleet.php`.

- `__construct()` — instantiates feature classes from
  `includes/iworks/fleet/class-iworks-fleet-{name}.php` (`db`, `github`)
  and the four post-type classes from `fleet/posttypes/` (ranking only when
  the `load_ranking` option is on). Instances are kept in `$this->objects`.
- Key methods: `action_init_load_post_types()`, `stats()` (shortcodes
  `fleet_stats`), `admin_enqueue_scripts()` (fleet admin assets; screen ID
  regex `iworks_fleet_`), `register_styles()` / `enqueue_styles()`,
  `register_boat_number()` (attachment taxonomy `boat_number`),
  `register_theme_directory()` / `after_setup_theme()` (registers
  `integration/themes/` as an extra theme directory),
  `action_plugins_loaded()` (OG integration when `iWorks_OpenGraph` exists;
  CSV download streaming), `get_regatta_select_by_year_and_serie_id()`,
  `db_install()`, `plugin_row_meta()`.
- Fires `do_action( 'fleet/loaded' )` when done.

## Shared post-type base (fleet/class-iworks-posttypes.php)

`iworks_fleet_posttypes extends iworks` — all four post-type classes extend it.

- Declarative `$fields` arrays per type → meta boxes
  (`get_meta_box_content()`) and a generic sanitizer `save_post_meta_fields()`
  (select / select2 / date / checkbox / url / email).
- Transient cache helpers with `dingy-` prefixed keys (bypassed under WP_DEBUG).
- MNA country-code lookup, Google geocoding for location terms.
- Template loading `get_template( $file, $group )` →
  `assets/templates/<group>/<file>.php` via `load_template()`.
- OpenGraph twitter label/data injection, dashboard glance items.

## Post types (fleet/posttypes/)

All CPTs are non-hierarchical, public, `capability_type: page`,
archives `fleet-boats`, `fleet-persons`, etc.; all admin menus are nested
under the Person menu (`get_show_in_menu()`).

| CPT | Class | Notes |
| --- | ----- | ----- |
| `iworks_fleet_boat` | `..._posttypes_boat` (2030 lines) | meta `boat_*` (nation, build_year, hull_number, name, colors, location, dates), `social_*`; complex meta: crew array + "current" (`iworks_fleet_boat_crew`), owners (`iworks_fleet_boot_owner_id` / owners with date_from/to, first/current). Taxonomies: `iworks_fleet_location` (hierarchical, shared, with Google-geo term meta), `iworks_fleet_boat_manufacturer` (hull), `iworks_fleet_sails_manufacturer`, `iworks_fleet_mast_manufacturer` (last two opt-in via `boat_taxonomies` option). `the_content` filter builds the boat details table (inline SVG hull colored from `boat_color_*` meta), crews, owners, gallery from attachments tagged `boat_number`. |
| `iworks_fleet_person` | `..._posttypes_person` | meta `personal_*` (sailor_id, full_name, nickname, nation, birth/death dates, sex), `social_*`, `contact_*`; reverse meta `iworks_fleet_boat_boat` (boats sailed/owned). Taxonomies: `iworks_fleet_club`, `iworks_fleet_location`. `the_content` filter: social, boats tables, places chart, trophy, tagged articles. |
| `iworks_fleet_result` | `..._posttypes_result` (4533 lines) | regattas/events. Taxonomy `iworks_fleet_serie` (shared with ranking). Meta `result_*` (location, city, organizer, secretary, arbiter, date_start/end, number_of_races/competitors, wind, columns, data_source). Races live in the custom tables (CSV upload via AJAX `iworks_fleet_upload_races` — delete + reinsert). Shortcodes: `fleet_regattas_list`, `fleet_ranking`, `fleet_regattas_list_years`, `fleet_regattas_list_countries`; CSV download links when the `*_show_download_link` options are on (CSV streamed in `plugins_loaded` request check, `Content-Type: text/csv`). |
| `iworks_fleet_ranking` | `..._posttypes_ranking` | opt-in via the `load_ranking` option; meta `ranking_*` (type results/medals, use_boat/use_helm/use_crew, year, serie, tie, no_discard, drop, drop_after, sex). Shortcode `iworks_fleet_ranking` takes a ranking post `id`; admin-only error notices via `assets/templates/shortcode/ranking-{missing,wrong}-id.php`. |

## Custom DB tables (fleet/class-iworks-fleet-db.php)

Registered on `$wpdb` as `$wpdb->fleet_regatta` and `$wpdb->fleet_regatta_race`;
versioned migrations (dbDelta + ALTERs) gated by the `fleet_db_version` option,
run on `shutdown`.

- `fleet_regatta` — post_regata_id, year, boat_id, helm_id, crew_id,
  helm_name, crew_name, place, points, country, date, ranking
  (one row per helm/crew per regatta).
- `fleet_regatta_race` — post_regata_id, regata_id, number, code, place,
  points, discard.

## Shortcodes & templates

| Shortcode | Where |
| --- | ----- |
| `fleet_stats` | main class |
| `fleet_boats_list`, `boat` | boat post-type class |
| `fleet_regattas_list`, `fleet_ranking`, `fleet_regattas_list_years`, `fleet_regattas_list_countries` | result post-type class |
| `iworks_fleet_ranking` | ranking post-type class |

Shortcode groups (`crew`, `medals`, `results`) each render through
`assets/templates/shortcode/{group}/output/{header,table,footer}.php`.

## GitHub self-updater (fleet/class-iworks-fleet-github.php)

Updates the plugin from the GitHub repository: `plugin_popup` /
`modify_transient` hooks. **Excluded from the WordPress.org release ZIP**
(wporg releases must not self-update from GitHub).

## Vendored frameworks (includes/iworks/)

- `options/` — `iworks_options`: settings pages, option storage and upgrade
  routines. Configured from `etc/options.php`; option names get the
  `IWORKS_FLEET_PREFIX` (`iworks_fleet_`) prefix. Separate git repo (submodule) —
  treat as vendor, do not refactor.
- `rate/` — rate-it / "enjoying this plugin?" admin notices and templates,
  wired via the `IWORKS_RATE_TEXTDOMAIN` placeholder. Separate git repo (submodule) — vendor.

## Options layer (etc/options.php + iworks_options)

- Nearly all frontend rendering is toggled by options: `boat_show_owners`,
  `person_show_trophy`, `load_frontend_css`, `results_show_points`,
  `results_serie_show`, `*_show_download_link`, `country` (applies a
  meta_query to archives), `load_ranking`, `boat_taxonomies`, etc.
- Groups for countries/colors/states double as data sources for selects.

## Known typos / pitfalls (do not fix without approval)

- Meta names with baked-in typos: `iworks_fleet_boot_owner_id` (owners),
  option `boad_add_social_media` — renaming breaks stored data.
- Docblock copy-paste `iworks_opi_polls_client_db` in the db class.
- Hardcoded `POL` strip in the media-by-boat-number query.
- A commented-out `class-iworks-fleet-blocks.php` include in the main class.
- The theme integration dir `integration/themes/twentytwenty-5o5-child/`
  includes a `5o5-archive.php` whose registration is currently commented out.
