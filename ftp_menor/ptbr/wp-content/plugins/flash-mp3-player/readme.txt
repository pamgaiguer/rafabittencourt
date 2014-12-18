=== Flash MP3 Player ===
Contributors: Charles
Donate link: http://sexywp.com/fmp
Tags: mp3 player, media player, player, sidebar, widget, audio, media, post
Requires at least: 2.7.1
Tested up to: 3.5.0 
Stable tag: 10.1.8

This plugin integrates a powerful music player into WordPress.

== Upgrade Notice ==
= 10.1.9 =
wp_enqueue_script and wp_register_script change their behavior. update the plugin. fix some little js problem
 which cause multi-instance of add button and update button on song edit panel.

= 10.1.8 =
URGENT: Update immediately. The 10.1.6 and 10.1.7 releases were compromised, and contained malicious code. - The WordPress team.

= 10.1.5 =
Tiny js bug fixed.

= 10.1.4 =
Nothing big changed.

= 10.1.3 =
Fix "song title cannot be too long" error.

= 10.1.2 =
Now the plugin allow user to use html special chars like & in song's link.

= 10.1.1 =
If you want to use the template tag, you should upgrade to this version.

= 10.1.0 =
Some little bugs fixed.

= 10.0.9 =
The Playlist Editor was redesigned, the song list now is sortable. Template tag supported.

== Changelog ==
= 10.1.8 =
 * Restores the code from 10.1.5. Version 10.1.8 was released by the WordPress team, because 10.1.6 and 10.1.7 were compromised and contained malicious code.

= 10.1.5 =
 * When add song, "add" button will duplicated bug fixed.

= 10.1.4 =
 * Some deprecated function calls updated.
 * Use swfobject_original as the script tag name of swfobject.js instead of swfobject because of the conflict with Facebook Walleria plugin.
 
= 10.1.3 =

 * Fix "song title cannot be too long" error.

= 10.1.2 =

 * Fixed a little bug, cannot use html special chars in link option.

= 10.1.1 =

 * The error of template tag not work fixed.

= 10.1.0 =

 * Playlist editor CSS bug fixed.
 * New plugin homepage created.

= 10.0.9 =

 * Playlist editor redesigned, the list now sortable
 * Add WordPress Mu support, by http://ixiezi.com
 * Add a template tag for theme developers

= 10.0.8 =

 * Use `<swfobject>` to load player to web page.
 * Use random default id
 * Correct a spelling mistake

= Old =
2009-6-3 20:41:02

* Bug fixed: the player always float on top layer of web pages, and break the lightbox.
* Bug fixed: fix the broken color picker in config editor.

2009-5-12 23:45:17

* Bug fixed: When using apostrophe in song's title, slashes will be added automatically.
* Bug fixed: When the name of its directory is not default one, the plugin will not work.

2009-4-30 11:11:06

Save the config file and playlist file formatted, now they are human readable.

2009-4-28 0:45:50

Fix the "when play single song cannot find playlist" bug

2009-4-27 9:51:22

Two stupid bugs have been fixed.

2009-4-26 12:40:39

Fix the problem of cannot load config file and playlist in Safari.

== Description ==

This plugin can display a highly customizable MP3 player on your sidebar, in a single post page or any other places on your blog pages.

Now, this plugin use JW MP3 player v2.3 as its core. 

Features:

* Shuffle the play list.
* Display an album cover when playing a song.
* Change the color scheme.
* Set the custom background image.
* Multiple configuration files and play lists.
* Insert to other place of your page with shortcode or template tag.

The new version dose NOT support PHP 4.

== Installation ==

1. Upload the directory `flash-mp3-player` to the `/wp-content/plugins/` directory.
1. Make sure the write permission of `wp-content` is 755.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Use 'Settings/FMP:Config Editor' to create config files.
1. Use 'Settings/FMP:Playlist Editor' to create playlist files.

= Add to sidebar =

1. Add a widget to your sidebar through the 'Appearance/Widgets' menu in WordPress.
1. Open the admin panel of the widget, and set title of the widget, choose config and playlist.
1. Save changes.

= Add to post or page =

1. In post new form, click the FMP media button, which is on top of the edit area.
1. Choose a config file.
1. Choose a playlist or set a single mp3 file.
1. Click insert button.(You can also write the shortcode mannually. They are same.)

= Use as a template tag =

Go to http://sexywp.com/fmp for details or contact me directly.

== Frequently Asked Questions ==

= When I move to another page on my site, the player start again from the beginning, how can I make it play the music continuously? =

This is just a simple music player without server support. It is very hard for me to make it behave like that.

= After activation, no menu entries Settings/FMP:Config Editor and Settings/FMP:Playlist Editor found. =

Make sure your `/wp-content` directory is writable. If you can connect your server with terminal, you can use this command to change the permission of the directory `chmod -R 755 wp-content`.

= Is it possible to get the player to keep playing while surfing around? =

It's hard to do this. The only way I know currently is to show this player in a popup new window and keep that window open when users surfing around.

== Screenshots ==

1. The final effect of the sample configuration.
2. The screenshot of the config editor.
3. How to insert a flash mp3 player to a post or page.
4. The screenshot of the playlist editor.
