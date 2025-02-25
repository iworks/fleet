=== PLUGIN_TITLE ===
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

The **Fleet Manager** plugin for WordPress is designed to manage sailboats, sailors, regattas, and their results. It allows users to track boats, record sailor statistics, and manage competition results in an organized and accessible way. Key features include:

1. **Regatta Management**: Allows the creation and management of sailing events and competitions.
1. **Sailor and Boat**: Records detailed information about sailors and boats.
1. **Results**: Displays race results, supporting features like medal display and ranking integration.

= GitHub =

The Simple Consent Mode plugin is available also on [GitHub - Fleet](https://github.com/iworks/fleet).

= See room for improvement? =

Great! There are several ways you can get involved to help make PLUGIN_TITLE better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](https://wordpress.org/support/plugin/fleet/) in the plugin forum. Once a developer can verify the bug by reproducing it, they will create an official bug report in [GitHub](PLUGIN_GITHUB_WEBSITE) where the bug will be worked on.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](https://wordpress.org/support/plugin/fleet/) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.
3. **Issue Pull Requests:** If you're a developer, the easiest way to get involved is to help out on [issues already reported](PLUGIN_GITHUB_WEBSITE/issues) in GitHub. Be sure to check out the [contributing guide](PLUGIN_GITHUB_WEBSITE/blob/master/contributing.md) for developers.

Thank you for wanting to make PLUGIN_TITLE better for everyone!

== Installation ==

There are 3 ways to install this plugin:

= The super-easy way =

1. Navigate to WPA > the Plugins and click the `Add New` button.
1. Search for `Fleet`.
1. Click to install.
1. Activate the plugin.
1. A new menu `Fleet` will appear in your Admin.

= The easy way =

1. Download the plugin (.zip file) on the right column of this page
1. Navigate to WPA > the Plugins and click the `Add New` button.
1. Select button `Upload Plugin`.
1. Upload the .zip file you just downloaded.
1. Activate the plugin.
1. A new menu `Fleet` will appear in your Admin.

= The old and reliable way (FTP) =

1. Upload `simple-consent-mode` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new menu `Fleet` will appear in your Admin.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 2.3.6 (2025-02-25) =
* Ability to upload SVG file has been removed due to Stored Cross-Site Scripting vulnerability. We recommended to install [Safe SVG](https://wordpress.org/plugins/safe-svg/) to allow upload SVG files.
* The build process has been improved.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.6.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.2.3.

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
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.2.0.
* *The ranking for the whole crew has been added.*

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
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.2.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.9.

= 2.1.5 (2023-06-30) =

* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.8.5.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.2.
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
* Updated iWorks Rate to 2.1.1.
* Changed some strings to improve translations.

= 2.0.9 (2022-07-22) =
* Changed series names to avoid translations issues.
* Updated iWorks Options to 2.8.3.

= 2.0.8 (2022-03-23) =
* Added icons for custom posts types to allow show it in [PWA — easy way to Progressive Web App](https://wordpress.org/plugins/iworks-pwa/) plugin as PWA Shortcodes.

= 2.0.7 (2022-03-18) =
* Added filter country + year.

= 2.0.6 (2022-02-18) =
* Updated iWorks Options to 2.8.1.
* Updated iWorks Rate to 2.1.0.

= 2.0.5 (2022-01-20) =
* Updated iWorks Options to 2.8.0.
* Updated iWorks Rate to 2.0.6.

= 2.0.4 (2021-11-16) =
* Added filter `suppress_filter_pre_get_posts_limit_to_year` to turn off year limitation.
* Added World Sailing Sailor ID.
* Fixed export CSV files.
* Fixed taxonomies admin pages when are turned off.
* Fixed trophies if there was no place.

= 2.0.3 (2021-05-28) =
* Updated iWorks Rate to 1.0.3.
* Improved filter `iworks_fleet_result_serie_regatta_list` by new config options: `output` - default html works like it was, and `raw` to return array of data instead of HTML string.

= 2.0.2 (2021-05-16) =
* Fixed race code status.
* Renamed directory `vendor` into `includes`.
* Updated iWorks Options to 2.6.9.
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
* Updated iWorks Options to 2.6.7

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
* Updated iWorks Options to 2.6.6

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

== Upgrade Notice ==

