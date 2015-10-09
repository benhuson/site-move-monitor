=== Site Move Monitor ===
Contributors: husobj
Tags: workflow, monitor, admin
Requires at least: 3.9
Tested up to: 4.3.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WordPress plugin that monitors whether a site has been moved and shows an alert in the admin bar.

== Description ==

This is useful for detecting if a site has moved from a staging environment to a live environment (or vice versa) to remind you to check any settings or things you need to do when moving a site. For example; allowing access to search engines, removing any password or IP restrictions, deactivating debugging plugins etc.

= How does Site Move Monitor detect if a site has moved? =

Site Move Monitor checks the following information to detect if there are changes to your site's hosting configuration:

* IP Address
* File Path
* URL
* Database
* Database Host
* Database Table Prefix

= Support =

Site Move Monitor requires no setup. Just install it and activate it.

Whenever you are logged into the site as an administrator you will see SIte Move Monitor in the admin bar at the top of the page. If the plugin detects that your site may have moved it will highlight red and display a list of changed configurations on rollover.

If you click Site Move Monitor in the admin bar it will take you to an admin page showing you an overview of your current configuration and what has changed.

If the site has moved it will continue to show the alerts until you press the "Update the Current Configuration" button.

= Bugs, Suggestions =

Development of this plugin is hosted in a public repository on [Github](https://github.com/benhuson/site-move-monitor). If you find a bug or have a suggestion to make this plugin better, please [create a new issue](https://github.com/benhuson/site-move-monitor/issues).

== Installation ==

1. Download
1. Unzip the package and upload to your /wp-content/plugins/ directory.
1. Log into WordPress and navigate to the "Plugins" panel.
1. Activate the plugin.

== Frequently Asked Questions ==

No FAQs yet.

== Screenshots ==

No screenshots yet.

== Changelog ==

= 0.1 =
* Initial Release

== Upgrade Notice ==

= 0.1 =
* Initial Release
