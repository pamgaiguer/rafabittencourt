=== Plugin Name ===
Contributors: schimmi, John Nunemaker
Tags: search, ajax, sidebar, widget
Requires at least: 2.0.2
Tested up to: 2.5
Stable tag: 1.4.7

Spotlight (tm) like live search with an ajax popup

== Description ==

This plugin adds Spotlight (tm) like live search with an ajax popup to
the default search box. See the screenshot.

It can be used as a widget or directly as a PHP call in the theme.

The Live Search Plugin is heavily based on John Nunemaker's 
[Addicted To Live Search](http://www.wp-plugins-db.org/plugin/addicted-to-live-search/).

= Changelog =

* 1.4.7 Fix by Tony for "With mouseover on result in IE, there is no background image or color".
* 1.4.6 Added livesearchpopup_resultsbox(width) to put the popup below a search box of your choice, e.g. from a theme.
* 1.4.5 Do not initialize if no livesearchpopup_results is found. In contrast to the original live search this plugin does not make sense without the popup anyway. This fixes a JavaScript bug in IE7.
* 1.4.4 Use wpurl instead of siteurl to allow installations of Wordpress at different addresses (thanks to Draco)
* 1.4.3 Use &lt;?php instead of &lt;? in live-search-popup.php
* 1.4.2 Force update script to update the plugin.
* 1.4.1 Switched to the_post instead of the deprecated wp_start().
* 1.4 Finally permalinks work together with this plugin. In addition a "Show all" links was added at the bottom for the case that there are more posts than shown in the popup (thanks to Marco Luthe).
* 1.3.4 Moved the placeholder and autocomplete attributes of the search box to JavaScript as they are not XHTML valid (thanks to Marco Luthe)
* 1.3.3 Added some print commands in front of get_bloginfos (thanks to upekshapriya who noticed that)
* 1.3.2 Force update script to update the plugin.
* 1.3.1 Relative paths in css to allow installation which are not at the domain root.
* 1.3 Fixed behaviour for change and blur events of the search box. This fixes the "click-through" bug.
* 1.2.1 Updated to reflect that it works with 2.3 as well.
* 1.2 Bugfixes
* 1.1 Bugfixes
* 1.0 First release

== Installation ==

1. Upload the entire folder named live-search-popup to wp-contents/plugins in
   your Wordpress installation (IMPORTANT: Do not change its name).
2. Login to your Wordpress administration and activate the plugin.
3. Remove the original search field (either in the theme or in the
   widget configuration screen).
4. Two possibilities:
* Add the live-search-popup widget to the sidebar
* Or add `<?php livesearchpopup_searchbox() ?>` at the place in the
   theme you want the search box to appear.
* Or put `<?php livesearchpopup_resultsbox("100%") ?>` below the
   search box of a theme, e.g. just below the `<input type="text"
   name="s" id="s"..../>` line. The `"100%"` is the popup width. Use
   e.g. `"150px"` for a fixed width of 150 pixels.

The search box is in a `<div class="livesearchpopup">...</div>`. So to place
the search nox more precisely just set the css properties of
`.livesearchpopup`.

== Frequently Asked Questions ==

== Screenshots ==

1. The Live Search Popup in action on [1stein.org](http://1stein.org)
