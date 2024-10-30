=== Plugin Name ===
Contributors: BraveNewCode, duanestorey, dalemugford
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=paypal%40bravenewcode%2ecom&item_name=BuddyPress%20Geo%20Beer%20Fund&no_shipping=1&tax=0&currency_code=CAD&lc=CA&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: buddypress, geo, bravenewcode, automattic, google, search, location, directory
Requires at least: 2.8.5
Tested up to: 2.8.5
Stable tag: 1.0.4

BuddyPress Geo is a plugin that adds a searchable member directory based on
each user's location.  

== Description ==

BuddyPress Geo allows people on a BuddyPress site to search for other users
(or businesses) within a certain distance.  It leverages the Google location
API to convert ordinary, real-world addresses (i.e., Vancouver BC, Canada)
into latitude and longitude.  This functionality allows for both
location-based member directories or possibly even location-based business
directories.

This is a very early beta, so I imagine there will still be a few bugs.  It currently requires either the trunk version of BuddyPress or version 1.12.

This project was sponsored by Automattic.

Please visit http://www.bravenewcode.com/buddypress-geo/ for a full description & updates on the BuddyPress Geo plugin.

== Changelog ==

= Version 1.0.4 =

* Added code to make sure BuddyPress was loaded before calling bp-specific functions

= Version 1.0.3 =

* Added ability to delete database table and restore it from the admin panel
* Fixed bug involving a full location table rebuild
* Fixed end condition for table rebuild

= Version 1.0.2 =

* Fixed stray miles string

= Version 1.0.1 =

* Updated verbiage

= Version 1.0.0 =

* Fixed queries to be compatible with HyperDB when the user table is on a different database
* First beta

== Installation ==

= WordPress 2.8.5 and above = 

* Install the plugin into the plugin directory, and activate as site-wide
* Make sure you have a field that represents each user's location.  If not, add one.
* Add a textbox field called Latitude to the same group as the location field
* Add a textbox field called Longitude to the same group as the location field
* From the administration panel, configure the plugin options, specifying the Group and Field names for the location profile information
* Copy the theme files from buddypress-geo/directories/geo into your BuddyPress theme (directories/geo)
* In the administration panel, rebuild the location indicies

== Frequently Asked Questions ==

None so far!

== Screenshots ==

1. BuddyPress Geo Administration Panel
