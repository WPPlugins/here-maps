=== HERE Maps ===
Contributors: marekkrysiuk, DeSmart
Tags: geo, location, maps, mapping, nokia, cross-browser, widget, places, nokia maps, address, here, here maps
Requires at least: 3.8
Tested up to: 4.2.2
Stable tag: 1.2.6

With HERE Maps you can easily add places and addresses into your Wordpress posts or pages.

== Description ==

= HERE Maps for Your Blog =

Official HERE Maps is powered by HERE APIs (https://developer.here.com/). Add it to your blog to share information about your favorite places and to display maps.

= Features =

* Easy to install: no need for additional configuration, adds a button to media insert/upload section
* Easy to use: search using address, choose a place from a list, edit location title and insert the address widget into the editor
* Three ready-to-use templates to customize your widget
* Use different map tiles including public transport and traffic information tiles
* All required data is stored in a shortcode, no additional tables needed.
* Shortcode ir backward compatibility with old plugin

== Installation ==
Just follow one of procedures described [here](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins). We recommend using [WordPress built-in installer](http://codex.wordpress.org/Administration_Panels#Add_New_Plugins). Remember to activate a plugin once it is installed.

== Screenshots ==

1. HERE Maps button in Upload/Insert section of Add New Post
2. Place search with suggestion list & Search results
3. Add pin to map
4. Customize your map
5. Set map type
6. Create custom area
7. Box template
8. Tooltip template

== Changelog ==

= 1.2.6 =
* Security fix: Prevent special characters to be injected in the URL for the og:url property 

= 1.2.5 =
* Fix: Plugin won't work correctly with HTTPS (including backward compatibility with old plugin)

= 1.2.4 =
* Fix: map shouldn't be automatically center if map has only one marker

= 1.2.3 =
* Fix: missing info about new version (1.2.2)

= 1.2.2 =
* Fix: Wrong if condition in "Center view port" function

= 1.2.1 =
* Fix: Function "Center view port" called with empty lists returned invalid points
* Fix: Notice messages from PHP in Debug mode

= 1.2.0 =
* Feature: custom graphics for markers
* Feature: custom area
* Feature: new themes for marker information
* Feature: simplified map type menu
* Refactoring JavaScript code

= 1.1.5 =
* Restore pixel ratio for HI-DPI devices
* Fix: Too small graphics for HI-DPI devices
* Fix: Removed fullscreen button in unsupported browsers

= 1.1.4 =
* Lower pixel ratio for HI-DPI devices
* Fix: height parameter doesn't work (backward compatible)

= 1.1.3 =
* Added some coments for default API configuration

= 1.1.2 =
* Bugfix: using wordpress functions for attach assets

= 1.1.1 = 
* Bugfix: removed short array syntax

= 1.1.0 =
* Remove depenencies 'wp-load' inside plugin code.
* Refactoring php code, remove useless files from package.

= 1.0.3 =
* Bugfix: escape HTML entity in description and title of marker

= 1.0.2 =
* Bugfix: change language labels (en_US)

= 1.0.1 =
* Bugfix: if website uses language which not supported then plugin will use english package

= 1.0.0 =
* Initial release. New features and new design.
