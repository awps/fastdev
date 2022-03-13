=== FastDev ===
Donate link: http://paypal.me/zerowp
Contributors: _smartik_
Tags: dev, developers, debug, analyze, inspect, test, security, options
Requires at least: 4.4
Tested up to: 5.9.0
Stable tag: 1.7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helpful information for developers and regular users.


== Description ==

You, as a developer often need to get different information from DB. This plugin will become a real time saver for you when creating the next plugin or theme. If you are not a developer, you may need to get general info about the WP installation, server info, php or mysql info, all this is included.

* Get information about current WordPress installation.
* Get information about current server.
* Access to top level admin pages directly from front-end admin bar.
* Inspect all WordPress Options and expand any of them for futher information.
* Get a list of all available hooks and info about each.
* View all available PHP classes and expand the source.
* View all available PHP functions, expand the source and follow the direct link to WP Codex.
* Get the users meta for a particular user by login.
* Get detailed PHP info.
* A list of all registered PHP constats.
* MySQL configuration info.
* A list of all registered widgets.

Icon attribution: http://www.flaticon.com/free-icon/robot_189740


== Installation ==

* Like any other WordPress plugin.
* Drop `fastdev` to wp-content/plugins/.
* More info here: http://codex.wordpress.org/Managing_Plugins#Installing_Plugins


== Screenshots ==

1. General info tab
2. WP Options info
3. A single option unserialized with the controls that have been introduced in version 1.1
4. A list of all hooks
5. A list of all loaded PHP classes
6. A class source extended with separated info
7. All available user functions
8. Everything in searchable.
9. Details about a function
10. Get meta details by username
11. Enabled mime types

== Changelog ==

= 1.8.0 =
* Update PHP versions, DB explorer, remove Testing and JSON parser

= 1.7.2 =
* Replace `SCRIPTS_DEBUG` with `SCRIPT_DEBUG` in the main page report
* WP Compatibility Update

= 1.7.1 =
* WP Compatibility Update

= 1.7.0 =
* Improvement: User meta menu item from admin bar now redirects to the user meta directly if it's accessed from a page which returns true to `is_author()`;
* New: Added "Post Meta" tab.
* New: The new post meta menu item from the admin bar now redirects to the post meta directly if it's accessed from a page that returns true to `is_singular()` or `is_page()`;
* Bug fix: Other bug fixes and minor changes.

= 1.5.0 =
* Bug fix: Get site language from WPLANG options not from WPLANG constant.
* Improvement: Better and more info on "Site info" page.
* New: Now it's possible to allow the view of "Site Info" and "PHP Info" pages by sharing a temporary generated link.

= 1.4.0 =
* Added sidebars debug. Get all sidebars and active widgets with their options.

= 1.3.1 =
* Bug fix: When accessing a single hook under Fastdev->Hook, it does not return its contents if the hookname contains uppercase letters, and or characters other than `[a-z_-]`. This is because the hook name is accessed from page url, and is not decoded.

= 1.3 =
* Disable syntax highlighting if the string length exceeds 50k characters. Highlighting large block of data may crash the browser.
* Allow to edit the key name of an option directly from admin.
* Trim large block of data from "WP Options" page, and add a button that expands the code.
* Added top level admin menus to admin bar on frontend: Plugins, Users, Settings and all public CPT.

= 1.2.6 =
* New: Display the current site id in admin bar.
* New: Display the current object id in admin bar.

= 1.2.5 =
* Bug fix: Some files missing because they were not transfered in last update(v1.2.4).

= 1.2.4 =
* Improvement: Code renderer performance.
* New: Fastdev menu and its tabs are now accessible from admin bar.
* New: Display active conditional tags in admin bar. Credits to QM.
* New: Display the total time required to render the pag, in admin bar.

= 1.2.3 =
* Fix: Fatal error because file names are case sensitive in Linux but not in Windows.
* Readme update.

= 1.2.2 =
* Wrong installation instructions.
* Readme update.

= 1.2 =
* User meta details page improvements.
* Included an autoloader for PHP classes.
* Code cleaning of trash.
* Added localization template .pot and support for translation.
* The license explicitly set to GPL-2.0+


= 1.1 =
* Possibility to refresh an option via AJAX(manually or automatically when the page is active).
* Possibility to delete an option. For advanced users.
* Added mime type list. Show what mime types can be used for file uploading and if they are enabled.
* Added function reference to https://developer.wordpress.org
* Use `htmlspecialchars` to properly display the source code in `fd_code` function.
* Added `autofocus` to search field.
