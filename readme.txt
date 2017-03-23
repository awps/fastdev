=== FastDev ===
Donate link: http://paypal.me/zerowp
Contributors: _smartik_
Tags: dev, developers, debug, analyze, inspect, test, security, options
Requires at least: 4.4
Tested up to: 4.7.3
Stable tag: 1.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helpful information for developers and regular users.


== Description ==

You, as a developer often need to get different information from DB. This plugin will become a real time saver for you when creating the next plugin or theme. If you are not a developer, you may need to get general info about the WP installation, server info, php or mysql info, all this is included.

* Get information about current WordPress installation.
* Get information about current server.
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
