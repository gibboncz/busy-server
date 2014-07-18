=== Plugin Name ===
Contributors: gibbon.cz
Tags: wordpress, server, load, busy, load balancing, overload, protection
Requires at least: 3.4.2
Tested up to: 3.9.1
Stable tag: 0.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

When the server load is higher than specified, show an error message instead of loading the page. 

== Description ==

Even though servers do have their "protection" against overload throwing a 503 error, at the time when this happen, server can be really overloaded already.
This plugin checks the actual CPU load and if higher than specified (by default 2 per every core), displays an error message.

Very simple checking is used and by no means is this a replacement to proper load balancing. Can be useful though, especially when your server system is undergoing a tuning or heavy maintenance, is under weak attack, etc. and you don't want to disable the site completely.

Currently only Linux servers are supported ( = tested), other UNIX systems should work as well.

Roadmap:

- Cache detection (if page is cached, serve it regardless the load)

- Support for Win servers

- Test server compatibility

- Storing settings outside of database for speed improvement

Found a bug or have a feature request ? Please report here at the <a href="http://wordpress.org/plugins/busy-server/" >plugin page</a>.

== Installation ==

1. Go to 'Plugins' -> 'Add New' in the admin area of your site.
1. Search for 'Busy Server'.
2. Click 'Install'.

OR Manually:

1. Upload `server-busy.php` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 0.1 =
Initial release

= 0.2 =
Custom error message and maximum load settings

= 0.2.1 = 
Fixed default values

= 0.2.2 = 
Added proper 503 HTTP headers
