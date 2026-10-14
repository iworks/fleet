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

= 2.6.1 (2026-10-14) =
* Added "location" to the JSON-LD.
* Added the ability to display the data source.
* Added the flag of Singapore.
* Changed the default slug for events to Y-m-<title>.
* Convert the source of results into a link if it is a valid URL.
* Corrected the class loading and initialization.
* **Dependencies**: Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.1.1 and the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.3.2.
* Improved the recognition of country codes.

= 2.6.0 (2025-12-09) =
* Added JSON-LD integration with the [Simple SEO Improvements](https://wordpress.org/plugins/simple-seo-improvements/) plugin for the Person schema.
* Added JSON-LD integration with the [Simple SEO Improvements](https://wordpress.org/plugins/simple-seo-improvements/) plugin for the Result schema.
* Added escaping functions for safer output handling.
* Fixed an XSS vulnerability in the admin panel affecting editing of the plugin’s custom post types.
* Improved localization by adding translator notes.
* Refactored database-related logic into a separate class.
* Removed the call to `load_plugin_textdomain()` as text domains are now loaded automatically.
* Renamed the file `posttypes.php` to `class-iworks-posttypes.php` for consistency with naming conventions.
* Replaced the `date()` function with `gmdate()` to ensure the formatted time is always in UTC and unaffected by the server or runtime timezone configuration.
* Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 3.0.9.

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

For changelog entries older than 2.2.0, see [CHANGLOG.md](https://github.com/iworks/fleet/blob/master/CHANGLOG.md).

== Contributing ==

Want to help improve Fleet Manager?

* **Report Bugs:** Open a topic in the plugin forum. Verified bugs are tracked on GitHub.
* **Suggest Features:** Share your ideas in the forum to start a discussion.
* **Contribute Code:** Fork the [GitHub repository](https://github.com/iworks/fleet) and submit pull requests. See the contributing guide for details.

== GitHub ==

The Fleet Manager plugin is available on [GitHub](https://github.com/iworks/fleet).
