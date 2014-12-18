=== Wordpress Facebook Grabber ===
Contributors: Fabio borraccetti	
Donate link: http://www.entula.net
Tags: facebook, comments, album, gallery, image,
Requires at least: 2.5
Tested up to: 3.1
Stable tag: 3.1

Wp Facebook Grabber plugin provide a way to take public content from facebook and insert in your Wordpress pages or posts.

== Description ==

Wp Facebook Grabber plugin provide a way to take public content from facebook and insert in your Wordpress pages or posts.
It works usin graph facebook api and json php/jquery function.


Actually the plugin support Facebook Photo Album and Feed data from Facebook profile.
You can put your feed status update and photo album from facebook everywhere in wordpress post or page, and theme it using css stylesheet.

For now it takes info using facebook id code.

It support for example:
[fbAlbum]132919960064546[/fbAlbum]
[fbFeed]128091230547419[/fbFeed]
Once the plugin is installed, you'll be able to add this tags to your pages or posts, the plugin will change the tag width the data  grabbed from facebook.

The facebook id code can be taken from the public profile url of facebook, for example:
http://www.facebook.com/pages/Porto-Torres-Italy/Il-Melo-Residence-Sardegna/126319364057939
The facebook id code is 126319364057939 and you can grab the feed status using: [fbFeed]128091230547419[/fbFeed] inside your wordpress posts or pages.

Facebook album id code can be taken from http://graph.facebook.com/[your_facebook_main_id]/albums
this gives something like that:
"id": "132919960064546",
"from": {
"name": "Il Melo Residence - Sardegna",
"category": "Local_hotels_lodging",
"id": "126319364057939"
},
...
The first id is the facebook album id and can be set between [fbAlbum]your_album_id[/fbAlbum] tags without spaces.

Extra information, user comments, demo and other can be found here: http://www.entula.net/wordpress-facebook-grabber

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the content of compressed archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use [fbAlbum] or [fbFeed] ad described in description section

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. wp_facebook_album_grabber : This is what you can have in your post or pages using fbAlbum tag.
2. wp_facebook_feed_grabber : This is what you can have in your post or pages using fbfeed tag.

2. This is the second screen shot

== Changelog ==

= 1.0 =
* first release

= 2.0 =
added bugfix for multiple call to fbAlbum tag thanks to Carlo Alberto Ferraris cafxx @  strayorange.com
added support for jquery based grab, if no curl or file_gets_content are available on the server
minor bugfix loading css file

= 3.0 =
added option panel
added maximum printed feed
added maximum printed photo from album
minor bugfix 
bugfix about css file name
added support for jquery based feed grabber

= 3.1 =
added option for jquery/curl mode
removed 25 max limit for photos ( remember, more photos means slow page load, use jquery if you post more than 40 photo )
minor bugfix thanks to tobeweb.fr and psysoul.hu

= 3.2 =
generic bugfix and some code cleanup
bugfix for feed grabber in jquery mode
new option in option panel for autodetect facebook album ID 

= 3.3 =
minor bugfix in case of no data from facebook.
feed are now probably down, i'm working on it.

= 3.4 =
fixed feed grabber bug, now access token supported.
minor bugfix.

= 3.5 =
Major bugfix about facebook feed grabber.
fixed new feed grabber bug, facebook graph for feed grab totally rewritten.
fixed some minor javascript bugs in case of no data from graph.
added facebook apps data to option panel.

= 4.0 =
Major bugfix.
Fixed typo error, f to log ion
jquery now not included, you have to load jquery in your header
jquery now use noconflict and jQuery sintax instead of $
added "original content" link for feed and album


== Upgrade Notice ==

jquery now not included, you have to load jquery in your header