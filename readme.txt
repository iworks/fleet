=== Fleet Manager ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=simple-revision-control&utm_medium=readme-donate
Tags: PLUGIN_TAGS
Requires at least: PLUGIN_REQUIRES_WORDPRESS
Tested up to: PLUGIN_TESTED_WORDPRESS
Stable tag: PLUGIN_VERSION
Requires PHP: PLUGIN_REQUIRES_PHP
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

PLUGIN_TAGLINE

== Description ==

PLUGIN_DESCRIPTION

== Features ==

* **Regatta Management:** Create and manage sailing events, regattas, and competitions.
* **Sailor & Boat Records:** Store detailed information about sailors and boats.
* **Results Display:** Show race results, including medals and rankings.
* **Statistics:** Visualize sailor performance over time.
* **Custom Shortcodes:** Display regatta lists by country or year, show boat galleries, and more.
* **Export Tools:** Export results for boats and sailors.
* **Compatibility:** Works with popular WordPress themes and includes PWA support.
* **Localization:** Translated into multiple languages.

== Installation ==

**There are three ways to install Fleet Manager:**

=== 1. Via WordPress Admin ===

1. Go to **Plugins > Add New** in your WordPress dashboard.
2. Search for **Fleet**.
3. Click **Install Now** and then **Activate**.
4. The **Fleet** menu will appear in your admin sidebar.

=== 2. Manual Upload ===

1. Download the plugin `.zip` file from the plugin page.
2. Go to **Plugins > Add New** in your dashboard.
3. Click **Upload Plugin**, select the `.zip` file, and install.
4. Activate the plugin.

=== 3. FTP Upload ===

1. Upload the `fleet` folder to `/wp-content/plugins/` directory.
2. Activate the plugin via the **Plugins** menu.
3. The **Fleet** menu will appear in your admin sidebar.

== Usage ==

* Use the **Fleet** menu to add boats, sailors, regattas, and results.
* Utilize available shortcodes to display regatta lists, results, and statistics on your site.
* Customize plugin settings as needed for your club or event.

== Shortcodes ==

* `[fleet_regattas_list_years]` – List regattas by year.
* `[fleet_regattas_list_countries]` – List regattas by country.
* `[boat]` – Show boat link, data, or gallery.
* See documentation for more available shortcodes and parameters.

== Blocks ==

* **Expenses:** Manage and display regatta or fleet-related expenses.

== Frequently Asked Questions ==

= Can I import/export data? =
Yes, Fleet Manager supports CSV export for boats, sailors, and results.

= Is it possible to customize fields? =
The plugin offers filters and hooks for developers to extend functionality.

= How do I report a bug or suggest a feature? =
Please use the plugin support forum or submit issues/pull requests on GitHub.

== Changelog ==

= 2.5.2 (2025-12-09) =
* Resolved an XSS vulnerability in the admin panel affecting the editing of custom post type entries within the plugin.
* Added JSON-LD integration with the [Simple SEO Improvements](https://wordpress.org/plugins/simple-seo-improvements/) plugin for the Person schema.
* Added JSON-LD integration with the [Simple SEO Improvements](https://wordpress.org/plugins/simple-seo-improvements/) plugin for the Result schema.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.1.0.
* Moved database-related functions to a separate class.

= 2.5.1 (2025-10-22) =
* Fixed issue with option object on ajax calls.
* Added function private `fputcsv` with default params.
* Added filter `iworks/fleet/result/fputcsv/data` to allow modify data before export.
* Added filter `iworks/fleet_result_fputcsv/delimiter` to allow modify delimiter.
* Added filter `iworks/fleet_result_fputcsv/enclosure` to allow modify enclosure.
* Added filter `iworks/fleet_result_fputcsv/escaped` to allow modify escaped.
* Added filter `iworks/fleet_result_fputcsv/eol` to allow modify EOL.

= 2.5.0 (2025-10-20) =
* Fixed issue with textdomain which was called incorrectly. [#1](https://github.com/iworks/fleet/issues/1)

= 2.4.0 (2025-10-16) =
* Added years submenu on country results page.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.0.8.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.3.1.

= 2.3.9 (2025-05-13) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.0.7.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.3.0.

= 2.3.8 (2025-05-07) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.0.0.
* Improved archive titles.

= 2.3.7 (2025-03-27) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.9.9.

= 2.3.6 (2025-02-25) =
* Ability to upload SVG file has been removed due to Stored Cross-Site Scripting vulnerability. We recommended to install [Safe SVG](https://wordpress.org/plugins/safe-svg/) to allow upload SVG files.
* The build process has been improved.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.9.6.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.2.3.

= 2.3.5 (2024-10-07) =
* Displaying a regatta without races has been improved.
* To import file the "Ranking" column has been added for handle different points then place.

= 2.3.4 (2024-09-21) =
* The ability to show a serie has been added.

= 2.3.3 (2024-09-20) =
* Hide future regatta.

= 2.3.2 (2024-09-19) =
* Handle a future date of a regatta.
* The EU flag has been added.

= 2.3.1 (2024-09-13) =
* The boat last update date has been added to show.

= 2.3.0 (2024-08-29) =
* Medals display have been improved.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.2.0.
* The ranking for the whole crew has been added.

= 2.2.2 (2024-08-02) =
* A boat link has been fixed.

= 2.2.1 (2024-07-24) =
* Few rankings adjustments have been added.

= 2.2.0 (2024-07-20) =
* The function `get_page_by_title()` has been replaced by the `WP_Query`.
* Rankings have been added.

= 2.1.7 (2024-06-21) =
* The "fleet_regattas_list_years" shortcode function params have been fixed. It has wrong order.

= 2.1.6 (2024-05-20) =
* The [OG — Better Share on Social Media Plugin](https://wordpress.org/plugins/og/) has been added.
* The class for single race has been added.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.9.2.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.9.

= 2.1.5 (2023-06-30) =

* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.5.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.2.
* The statistics HTML has been fixed.
* Unnecessary trailing slashes have been removed.

= 2.1.4 (2023-06-16) =

* The statistics module has been added.

= 2.1.3 (2023-06-15) =

* Division by zero issue has been removed.

= 2.1.2 (2023-06-05) =

* The added date will be saved from now on.
* Added the ability to show a graph with sailors' results.

= 2.1.1 (2023-02-06) =

* The special result row class has been added.

= 2.1.0 (2022-09-01) =
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.1.
* Changed some strings to improve translations.

= 2.0.9 (2022-07-22) =
* Changed series names to avoid translations issues.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.3.

= 2.0.8 (2022-03-23) =
* Added icons for custom posts types to allow show it in [PWA — easy way to Progressive Web App](https://wordpress.org/plugins/iworks-pwa/) plugin as PWA Shortcodes.

= 2.0.7 (2022-03-18) =
* Added filter country + year.

= 2.0.6 (2022-02-18) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.1.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.0.

= 2.0.5 (2022-01-20) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.0.
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.0.6.

= 2.0.4 (2021-11-16) =
* Added filter `suppress_filter_pre_get_posts_limit_to_year` to turn off year limitation.
* Added World Sailing Sailor ID.
* Fixed export CSV files.
* Fixed taxonomies admin pages when are turned off.
* Fixed trophies if there was no place.

= 2.0.3 (2021-05-28) =
* Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 1.0.3.
* Improved filter `iworks_fleet_result_serie_regatta_list` by new config options: `output` - default html works like it was, and `raw` to return array of data instead of HTML string.

= 2.0.2 (2021-05-16) =
* Fixed race code status.
* Renamed directory `vendor` into `includes`.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.6.9.
* Improved owner boat list.

= 2.0.1 (2021-05-06) =
* Added missing flags: ESP, NED, POR.
* Added params `title` and `flags` into `fleet_regattas_list_countries` shortcode.

= 2.0.0 (2021-05-05) =
* Added `$settings` param into `iworks_fleet_boat_get_by_owner_id` filter.
* Added ability to add base fleet styles.
* Added ability to add wide body class (compatibility with 2020 theme).
* Added ability to choose country or countries for whole plugin.
* Added ability to create custom column name for races.
* Added ability to export boat results.
* Added ability to export sailor results.
* Added ability to filter results by year.
* Added ability to show English version of regatta title.
* Added ability to show/hide boat country code.
* Added boats owners.
* Added child theme for TwentyTwenty Theme.
* Added filter `iworks_fleet_result_serie_regatta_list'.
* Added filter `iworks_fleet_result_skip_year_in_title` to avoid prefixing result title by year.
* Added rel="alternate nofollow" to CVS links.
* Added sailors nation and display flag.
* Added shortcode `fleet_regattas_list_countries` to produce results countries list.
* Added shortcode `fleet_regattas_list_years` to produce results years list.
* Added trophies list.
* Improved compatibility with TwentyTwenty Theme.
* Improved hull colors choose.
* Improved results importer.
* Improved "Social Media" section.
* Removed Endomondo integration.

= 1.2.9 (2020-06-17) =
* Added MNA Codes see: https://www.sailing.org/raceofficials/eventorganizers/mna_codes.php
* Remove "POL " default prefix.

= 1.2.8 (2020-06-09) =
* Added ranking-o-mat.

= 1.2.7 (2019-11-07) =
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.6.7.

= 1.2.6 (2019-11-04) =
* Fixed function for `the_title` filter - second argument should have default.
* Added shortcode "boat" to allow show boat link, data or gallery.
* Fixed taxonomies links in admin menu.

= 1.2.5 =
* Added ability to change tag link into person link.
* Added ability to show posts list on fleet person page.

= 1.2.4 =
* Added filter to adjust dates of results, to use on integrations.
* Added option to automagically add a feature image to a boat, based on image tags. By default, it is turned off.
* Changed default sort order in admin for persons.
* Changed slug for results archive from `fleet-results` to `results`.

= 1.2.3 =
* Allow to turn on/off boat hull taxonomy.
* Allow to turn on/off boat mast taxonomy.
* Allow to turn on/off boat sails taxonomy.
* Allow to turn on/off boat crew.
* Fixed problem with last regatta result by serie - it was doubled.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.6.6.

= 1.2.2 =
* Show regatta city instead area.
* Improved dates.
* Improved shortcode "dinghy_regattas_list" for all years.
* Added SVG file type to allowed mime types.
* Added serie thumbnail.

= 1.2.1 =

* Added sort order for "dinghy_regattas_list" shortcode.
* Added sort order in custom taxonomies "series".
* Remove year from table for "dinghy_regattas_list" shortcode.

= 1.2.0 =

* Added shortcode "dinghy_regattas_list".
* Added shortcode "dinghy_stats".
* Added shortcode "dinghy_boats_list".
* Added "Series" taxonomy for results.

= 1.1.1 =

* Added `country` field in regatta table.
* Added the boat list on a sailor page.
* Add year to a single result slug on save.

== Contributing ==

Want to help improve Fleet Manager?

* **Report Bugs:** Open a topic in the plugin forum. Verified bugs are tracked on GitHub.
* **Suggest Features:** Share your ideas in the forum to start a discussion.
* **Contribute Code:** Fork the [GitHub repository](https://github.com/iworks/fleet) and submit pull requests. See the contributing guide for details.

== GitHub ==

The Fleet Manager plugin is available on [GitHub](https://github.com/iworks/fleet).
