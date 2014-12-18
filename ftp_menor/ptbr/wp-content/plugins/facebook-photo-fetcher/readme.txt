=== Plugin Name ===
Contributors: Justin_K
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L32NVEXQWYN8A
Tags: facebook, photos, images, pictures, gallery, albums, fotobook, media
Requires at least: 2.5
Tested up to: 3.8
Stable tag: 2.1.11

Allows you to automatically create Wordpress photo galleries from any Facebook album you can access.  Simple to use and highly customizable.


== Description ==

This plugin allows you to quickly and easily generate Wordpress photo galleries from any Facebook album you can access.

The idea was inspired by [Fotobook](http://wordpress.org/extend/plugins/fotobook/), though its approach is fundamentally different: while Fotobook's emphasis is on automation, this plugin allows a great deal of customization. With it you can create galleries in any Post or Page you like, right alongside your regular content. You do this simply by putting a "magic HTML tag" in the post's content - much like [Wordpress Shortcode](http://codex.wordpress.org/Gallery_Shortcode). Upon saving, the tag will automatically be populated with the Facebook album content. Presentation is fully customizable via parameters to the "magic tag" - you can choose to show only a subset of an album's photos, change the number of photos per column, show photo captions, and more.

Also, Facebook Photo Fetcher does not limit you to just your own albums: you can create galleries from friends and fanpages as well. This is very handy if you're not the main photo-poster in your social circle: just let your friend or family upload all those wedding pics, then import them directly to your blog!

Features:

* Uses Facebook's API to instantly create Wordpress photo galleries from Facebook albums.
* Galleries are fully customizable: you can import complete albums, select excerpts, random excerpts, album descriptions, photo captions, and more.
* Galleries can be organized however you like: in any post or page, alone or alongside your other content.
* Simple PHP template function allows programmers to manually embed albums in any template or widget.
* Built-in LightBox: Photos appear in attractive pop-up overlays without the need for any other plugins.
* Admin panel handles all the setup for you: Just login and you're ready to start making albums.
* Admin panel includes a utility to search for albums you can access (and use to create galleries).
* Admin panel includes a utility to auto-traverse all your posts and pages, updating albums that may've changed on Facebook.
* No custom database tables required; galleries live in regular post content.

For a Demo Gallery, see the [plugin's homepage](http://www.justin-klein.com/projects/facebook-photo-fetcher).

Note: In order to allow this plugin to access your photos, it requires a one-time "phone home" authorization.  This is necessary to comply with Facebook's security rules, which require that apps authorize from a specific, known location.  If you're allergic to "phone home" scripts then you won't be able to use this (or any Facebook app-based) plugin.

== Installation ==

[Installation Instructions](http://www.justin-klein.com/projects/facebook-photo-fetcher#instructions)

== Frequently Asked Questions ==

[FAQ](http://www.justin-klein.com/projects/facebook-photo-fetcher#faq)


== Screenshots ==

[Demo Gallery](http://www.justin-klein.com/projects/facebook-photo-fetcher#demo)


== Changelog ==
= 2.1.11 (2014-01-22) =
* Strip smartphone emoji from image descriptions (was causing crashes/incomplete results) 

= 2.1.10 (2013-12-13) =
* Verified compatibility with WP 3.8
* CSS fix for TwentyFourteen theme

= 2.1.9 (2013-10-25) =
* Don't output a title attribute for photos which don't have captions (to avoid an odd-looking mouseover)
* Add an admin panel warning for users running over SSL (which blocks the login button after some recent Chrome & Firefox browser updates)
* Tested with WP 3.7

= 2.1.8 (2013-05-09) =
* Add a note about the authorization process (to satisfy wp.org's plugin repo guidelines)

= 2.1.7 (2013-05-07) =
* Remove activation/deactivation auth

= 2.1.6 (2013-02-07) =
* Fix some harmless server 404 error logs due to old IE-specific CSS
* Fix a harmless warning on activation if WP_DEBUG is enabled

= 2.1.5 (2012-12-26) =
* Oops - missed one more bug in Fancybox in the previous commit.  Should be working now.
* Tested on WP3.5

= 2.1.4 (2012-12-26) =
* Fix a bug in Fancybox that prevents the use of URLs in 'rel' attribute
* Change the gallery 'rel' attribute to satisfy the HTML5 validator
* Also bundle the uncompressed version of Fancybox (for easier debugging/testing)


= 2.1.3 (2012-11-29) =
* Add filter "fpf_parse_params" to allow developers to supplement the included magic tag params with their own.
* Add filter "fpf_album_data" to modify the album metadata (i.e. author, date, covor photo, etc).
* Add filter "fpf_photos_presort" to modify the photo objects received from Facebook.  Applied before trimming/sorting.
* Add filter "fpf_photos_postsort" to modify the photo objects received from Facebook.  Applied after trimming/sorting.
* Show "FB Photo Fetcher+" in the admin menu if a 3rd party addon is present, and add support for an "Addon" tab.
* Move the "donate" link to the bottom of the Support Info tab (rather than a tab of its own).

= 2.1.2 (2012-11-20) =
* Don't verify the ssl certificate when contacting Facebook (to fix SSL3_GET_SERVER_CERTIFICATE on servers with improper cURL configurations)

= 2.1.1 (2012-11-19) =
* Add the url to the Support Info tab

= 2.1.0 (2012-11-19) =
* Add a button to the admin panel to renew Facebook access tokens; it's available from 1 day after the token is created (since FB only allows you to renew once per day)
* Add a more descriptive error message upon failure to get an access token
* Add a "Support Info" tab to the admin panel
* Fix for galleries in custom post types
* Fix a debug notice about wp_enqueue_style/wp_enqueue_script
* Fix z-indices in Twenty Eleven (so the lightbox doesn't come up beneath the header)
* Get rid of the isGroup and isPage warnings (not necessary since I changed the magic tag identifier)
* Rephrase the 'count mismatch' error message

= 2.0.0 (2012-11-16) =
* Complete rewrite of all Facebook authentication/interaction code; the plugin now uses the new Graph API.  Existing users of v1.x will need to re-authorize in the admin panel, and potentially update existing album tags (but only if you plan to re-fetch those albums).  Please visit the plugin documentation page for more information on upgrading.
* New tabbed admin panel
* The Magic Tag identifier has been changed to "FBGallery2"
* The ID numbering scheme has been changed
* The admin panel revalidates your access token whenever it's loaded, to make sure it hasn't expired
* Added a button to remove an existing token from the database (aka deauthorize)
* Renamed the 'item count' postmeta from _fb_album_size to _fpf_album_size
* Added new postmeta _fpf_album_cover with the Facebook URL of the cover photo
* Removed the Add-From-Server feature (it wasn't working properly; may re-add it at some point in the future...)
* Name and uid are no longer stored in the db, as they're only used by the admin panel (and can be fetched as part of the revalidation test)
* Nicer formatting for album search results
* The footer now says "Generated by Facebook Photo Fetcher 2"
* More changes than I can list...

= 1.3.5 (2012-11-13) =
* Facebook broke the redirect URL on their login dialog.  This fixes it to properly display "success" after authorization again.

= 1.3.4 (2012-08-21) =
* Oops - previous version didn't fully fix the problem.  Should work properly now.

= 1.3.3 (2012-08-20) =
* Handle multibyte characters in caption excerpts

= 1.3.2 (2012-08-16) =
* Facebook changed their API and broke things yet again (removal of prompt_permission.php endpoint). This update should work around it and get things running as they were previously.
* Facebook has announced that they'll break offline_access on Oct 3, 2012.  This update should keep the plugin running after that update as well. 
* Update setup instructions
* Update Wordpress compatibility number. 

= 1.3.1 (2012-06-05) =
* Update the instructions for getting userIDs in the admin panel (Facebook changed their URL scheme again).

= 1.3.0 (2012-04-21) =
* Apparently, the previous lightbox implementation included with this plugin was not GPL-compatible (leading to its removal from the repository). This update uses a different lightbox that should satisfy WP.org.  NOTE that if you update, you will need to re-fetch all of your albums so that their code will be updated to use new lightbox.  You can do this quickly via the "Re-Fetch All Albums In Pages" and "Re-Fetch All Albums In Posts" admin panel buttons. 

= 1.2.15 (2012-02-05) =
* Update version compatability number
* Fix "refresh albums" to work for PRIVATE post/pages (as well as public)
* Admin panel code cleanups
* Slightly revise instructions
* Add better support for code addons

= 1.2.14 (2011-12-19) =
* Apply trailingslashit() to the thumbnail path to prevent double-slash
* Update compatibility number 

= 1.2.13 (2011-11-28) =
* Removed plugin sponsorship messages.  See [Automattic Bullies WordPress Plugin Developers -- Again](http://gregsplugins.com/lib/2011/11/26/automattic-bullies/).
* Update compatibility number

= 1.2.12 (2011-06-28) =
* Add a note to the admin panels that search is only for personal albums
* Reformat the search results to be copy-pasteable tags

= 1.2.11 (2011-06-14) =
* Update compatability tag
* Add (hide-able) sponsorship message

= 1.2.10 (2011-06-08) =
* Slight cleanups to admin panel
* Some code restructuring to support an eventual cronjob addon

= 1.2.9 (2011-03-18) =
* Add new "orderby=reverse" param

= 1.2.8 (2010-11-02) =
* Remove unneeded debug code

= 1.2.7 (2010-10-30) =
* Add return URL to paypal donate button

= 1.2.6 (2010-10-28) =
* Error check if the user denies necessary permissions while connecting to Facebook

= 1.2.5 (2010-10-14) =
* Marked as compatible up to 3.0.1

= 1.2.4 (2010-10-14) =
* Bug fix: Categories were getting lost when using "Re-Fetch All Albums in Posts" 

= 1.2.3 (2010-08-08) =
* Oops - forgot to add a check in one more spot

= 1.2.2 (2010-08-08) =
* Added a check for other plugins globally including the Facebook API

= 1.2.1 (2010-08-07) = 
* Something got left out of the 1.2.0 commit...

= 1.2.0 (2010-08-07) =
* Update the Facebook client library so it'll play nice with newer plugins
* The minimum requirement is now PHP5.

= 1.1.13 (2010-07-24) =
* Update connection process for Facebook's new privacy policies (to address the bug where no albums were returned by search)

= 1.1.12 (2010-07-15) =
* Fix bug where thumbnails were not downloaded for non-group/page albums where only a portion of the album is shown.

= 1.1.11 (2010-03-16) =
* Use php long tags instead of short tags; should work on XAMPP servers now.

= 1.1.10 (2010-03-14) =
* Sorry - 1.1.9 broke regexp's again for 64-bit userID's. Should be fixed.

= 1.1.9 (2010-03-14) =
* Oops - regexp mistake required a space after the albumID in the start tag; fixed.

= 1.1.8 (2010-03-14) =
* The last version broke isPage; fixed.

= 1.1.7 (2010-03-13) =
* Added support for 64-bit userIDs (aka albumID's with dashes and minuses)

= 1.1.6 (2010-03-13) =
* Added a check for has_post_thumbnail exists (so it won't die on pre-2.9 wordpress installations)

= 1.1.5 (2010-03-11) =
* Fix an issue where the last row of photos weren't clearing their floats properly; YOU'LL NEED TO REGENERATE YOUR GALLERIES for this fix to be applied.
* Always explicitly prompt for infinite session (many users seemed to be getting this error)

= 1.1.4 (2010-03-10) =
* Add isPage parameter - now you can get photos from fan pages!

= 1.1.3 (2010-03-09) =
* Include close/next/prev/loading images for lightbox

= 1.1.2 (2010-03-09) =
* Add version number to plugin code
* Small fixes & cleanups
* Update instructions to clear up a common issue

= 1.1.1 (2010-03-08) =
* Fix bug if photo captions are enabled and contain square brackets

= 1.1.0 (2010-03-08) =
* Add support for GROUP photo albums (in addition to USERs)
* Some code restructuring

= 1.0.3 (2010-03-08) =
* Add support for "rand" argument (randomized album excerpts)
* Add links to FAQ when fail to connect with facebook
* Minor cleanups

= 1.0.2 (2010-03-07) =
* Add support for PHP4

= 1.0.1 (2010-03-06) =
* Add default stylesheet

= 1.0.0 (2010-03-06) =
* First Release


== Support ==

Please direct all support requests [here](http://www.justin-klein.com/projects/facebook-photo-fetcher#feedback)