=== MP3-jPlayer ===
Author URI: http://www.sjward.org
Plugin URI: http://www.sjward.org/jplayer-for-wordpress
Contributors: simon.ward
Tags: mp3, audio, mp3 player, music player, audio player, jplayer, playlist, jquery, shortcode, widget, css, posts, page, sidebar 
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 1.7.3

Add mp3 audio players to posts, pages, and sidebars. HTML5 / Flash. Uses jPlayer.

== Description ==

- Flexible multi-player plugin.
- Playlist and single-file players.
- Pop-out player.
- Individual control of height, width, volume, download etc.
- Customise the colour scheme on the settings page.
- Uses a single instance of [jPlayer by Happyworm](http://jplayer.org/)
- Good compatibility across browsers/platforms. Works on iPhone 4, iPad. Uses HTML 5 or Flash if necessary.
- Editable player designs via CSS.

[View Demo here](http://sjward.org/jplayer-for-wordpress)

<br />
This plugin lets you add mp3 players to your site using shortcodes, widgets, and template tags. There's useful stuff on the settings page such as default folder setting, mp3 file lists, and plenty of shortcode parameters to control things like width, height, autoplay, volume etc. 

You can play entire folders with one simple command, or the library, or make playlists track by track, randomise them, add titles and captions (or use the library ones), set playlists for download, hide your urls.

Widgets and tags can automatically pick up your track lists from posts/pages, or have their own playlists.

As only the one instance of jPlayer is created there's no loss of performance or speed however many players you put on a page.


<br />
<br />
**Shortcodes**

[mp3j] and [mp3t] add single-track players

<br />
eg. Play a url:
<code>[mp3j track="www.site.com/tune.mp3"]</code>

<br />
eg. Play a library or default folder mp3:
<code>[mp3j track="myfile.mp3"]</code>

<br />
eg. Play track 30 from custom-fields playlist/folder:
<code>[mp3j track="30"]</code>

<br />
eg Play incrementally from custom-fields playlist/folder:
<code>[mp3j]</code>

<br /><br />

[mp3-jplayer] adds playlist players

eg. Play files, url's, folders:
<code>[mp3-jplayer tracks="file.mp3, url, FEED:/myfolder"]</code>

<br />
eg. Play custom fields and shuffle them:
<code>[mp3-jplayer shuffle="y"]</code>

<br />
eg. Play 7 random library mp3's:
<code>[mp3-jplayer pick="7" tracks="FEED:LIB"]</code>
<br />

Other examples:

<code>[mp3t vol="70" loop="y" track="myfile.mp3"]</code>

<code>[mp3-jplayer width="30%" height="80px" autoplay="y" tracks="FEED:DF"]</code>

<code>[mp3j flip="y"]</code>

<br />
Please see the help on the plugin's settings page for more info and a full list of parameters.


== Installation ==

Install using WordPress:

1. Log in and go to 'plugins' -> 'Add New'.
3. Search for 'mp3-jplayer' and hit the 'Install now' link in the results, Wordpress will install it.
4. Activate the plugin.

Install manually:

1. Download the zip file and unzip it. 
2. Open the unzipped folder and upload the entire contents (1 folder and it's files and subfolders) to your `/wp-content/plugins` directory on the server.
3. Activate the plugin through the WordPress 'Plugins' menu.


== Frequently Asked Questions ==

= Supported file formats? =
Just mp3 files.

= Theme requirements? =
Themes need the wp_head() and wp_footer() calls in them.

= Mp3 encoding? =
Mp3's should be constant bit-rate (CBR) encoded at sample rates 44.1kHz, 22.05 kHz, 11.025 kHz, though variable bit-rate (VBR) files seem to work ok.

= Player says connecting but never plays? =
Check the filename spelling and the path/uri are correct. Remove any accented letters from mp3 filenames (and re-upload if they're from the library). Check the mp3 encoding (see above).

= Header and footer players? =
Use widget areas (if available), or use the mp3j_addscripts() and mp3j_put() functions in template files. See help in the plugin for an example.

= Player appears but something is broken? =
Any number of reasons but the most commonly seen problem is poor use of a CDN or jQuery in theme files, check the page source for repeated scripts (including jquery-ui).

= Report bugs/issues? =
Either on the forum at Wordpress, or [here](http://sjward.org/contact).


== Screenshots ==

1. Players example 1
2. Players example 2
3. Popout player example 
4. Players example 3
5. Players example 4
6. Admin settings page
7. Colour settings
8. Other options


== Changelog ==

= 1.7.3 =
* Stopped files of audio/mpeg MIME type other than mp3 from showing on the player's library file list on the settings page. They won't appear in playlists when using 'FEED:LIB' now.  
* Corrected graphics error introduced last update on the popout button, thanks to Peter for reporting.

= 1.7.2 =
* Fixed bug in the case where sidebars_widgets array was not defined (was throwing a php warning), thanks to Craig for reporting.
* Fixed bug on search pages where full post content was being used (players in posts were breaking unless a player widget was present), thanks to Marco for reporting.
* Fixed loop parameter in single players (wasn't responding to 'n' or '0'). Thanks to George for reporting.
* Corrected the template tag handling so that it can auto pick-up mp3's from post fields on index/archive/search pages. 
* Fixed the 'text' player's colour pickup for the popout, and refined it's layout a little.
* Changed from using depreciated wp user-levels to capabilities for options page setup (was throwing a wp_debug warning).
* Corrected typos in the plugin help (invasion of capitalised L's).

= 1.7.1 =
* Fixed widgets on search pages, and added 'search' as an include/exclude value for the page filter. Thanks to Flavio for reporting.
* Fixed pick-up of default colours when using template tags, and the indicator on single players.

= 1.7 =
* Added multiple players ability, backwards compatible (see notes below).
* Added single-file players.
* Added pop-out.
* Added colour picker to settings.
* Added player width and height settings, captions (or titles) will word-wrap.
* Added shortcodes widget.
* Updated jQuery UI and fixed script enqueuing.
* Fixed page filter for widget, added index and archive options.
* Changed ul transport to div (for better stability across themes).
* General improvements and bug fixes.
* NOTE 1: File extensions must be used (previously it was optional).
* NOTE 2: Shortcodes are needed to add players within the content (previously it was optional). 
* NOTE 3: CSS has changed (id's changed to classes, most renamed), old sheets won't work without modification.

= 1.4.3 =
* Fixed player buttons for Modularity Lite and Portfolio Press themes (they were disappearing / misaligned when player was in sidebar), thanks to Nate, Jeppe, and Nicklas for the reports.
* Fixed the bug in stylesheet loading when using the mp3j_addscripts() template tag (style was not being loaded in some cases), thanks to biggordonlips for reporting. 

= 1.4.2 =
* Fixed error in the scripts handling for the widget, thanks to Kathy for reporting.
* Fixed the non-showing library captions when using widget modes 2/3 to play library files.
* Fixed (hopefully) the mis-aligned buttons that were still happening in some themes.

= 1.4.1 =
* Added a repeat play option on settings page.
* Fixed text-player buttons css in Opera.
* Fixed initial-volume setting error where only the slider was being set and not the volume. Thanks to Darkwave for reporting.

= 1.4.0 =
* Added a widget.
* Improvements to admin including library and default folder mp3 lists, custom stylesheet setting, and some new options.  
* Added new shortcode attributes shuffle, slice, id. New values for list
* Added a way to play whole folders, the entire library, to grab the tracks from another page.
* Added a simpler text-only player style that adopts theme link colours.
* Improved admin help.
* Some minor bug fixes.
* Some minor css improvements and fixes.

= 1.3.4 =
* Added template tags.
* Added new shortcode attributes play and list, and added more values for pos.
* Added new default position options on settings page
* Added a smaller player option

= 1.3.3 =
* Fixed the CSS that caused player to display poorly in some themes.

= 1.3.2 =
* Added the shortcode [mp3-jplayer] and attributes: pos (left, right, none), dload (true, false) which over-ride the admin-panel position and download settings on that post/page. Eg. [mp3-jplayer pos="right" dload="true"]
* Tweaked transport button graphic a wee bit.

= 1.3.1 =
* Fixed image rollover on buttons when wordpress not installed in root of site.

= 1.3.0 =
* First release on Wordpress.org
* Updated jquery.jplayer.min.js to version 1.2.0 (including the new .swf file). The plugin should now work on the iPad.
* Fixed admin side broken display of the uploads folder path that occured when a path had been specified but didn't yet exist.
* Fixed the broken link to the (new) media settings page when running in Wordpress 3.
* Changed the 'Use my media library titles...' option logic to allow any titles or captions to independently over-ride the library by default. The option is now 'Always use my media library titles...' which when ticked will give preference to library titles/captions over those in the custom fields.
* Modified the css for compatibility with Internet Explorer 6. The player should now display almost the same in IE6 as in other browsers.

= 1.2.12 = 
* Added play order setting, a 'download mp3' link option, show/hide playlist and option, a connecting state, a new style.  
* The 'Default folder' option can now be a remote uri to a folder, if it is then it doesn't get filtered from the playists when 'allow remote' is unticked. 

= 1.2.0 =
* Added playing of media library mp3's in the same way as from the default folder (ie. by entering just a filename). User does not have to specify where the tracks reside (recognises library file, default folder file, and local or remote uri's). 
* Added filter option to remove off-site mp3's from the playlists.
* The plugin now clears out it's settings from the database by default upon deactivation. This can be changed from the settings page.
* It's no longer necessary to include the file extension when writing filenames.

= 1.1.0 =
* Added captions, player status info, a-z sort option, basic player positioning, detecting of urls/default folder
* Fixed bug where using unescaped double quotes in a title broke the playlist, quotes are now escaped automatically and can be used.

= 1.0 =
* First release
