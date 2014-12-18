=== Facebook Album ===
Contributors: mglaman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CTT2K9UJZJ55S
Tags: facebook, albums, pictures
Requires at least: 3.4
Tested up to: 3.5.1
Stable tag: 2.0.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Put your Facebook page albums on your WordPress.

== Description ==

Facebook Album allows you to display your Facebook photos right onto your WordPress site. Simply copy an album's URL and use the plugin's shortcode - or widget - to display that album. By default the plugin works seamlessly with Facebook Page albums, however to use your personal Facebook albums you must configure a Facebook app. If you need help, [check out this YouTube video](http://www.youtube.com/watch?v=3p_OZ7qbxk4).

Through Settings -> Facebook Album you can adjust the thumbnail sizes of your album's photos when they are displayed, along with enabling or disabling the album's title or the photo's description display.

This plugin comes bundled with Colorbox, which can be disabled. Certain colorbox options are configurable, more will be soon.


= Shortcode Example: =
[fbalbum url=https://www.facebook.com/media/set/?set=a.376995711728.190761.20531316728]

[fbalbum url=https://www.facebook.com/media/set/?set=a.376995711728.190761.20531316728 limit=10]

If you have questions, suggestions, or problems please email the developer at nmd.matt@gmail.com

== Installation ==

Upload the Facebook Album plugin to your WordPress plugin folder, and then just activate it! Use the shortcode in posts and pages.

 == Frequently Asked Questions ==

= Plugin says Facebook API came back with result, but no photos =
This is an authentication issue. If you are not using a Facebook app, try setting one up to see if that resolves the issue. Only Facebook Page photo albums can be used without a Facebook app.

= My album is never found? =
The plugin "looks" for the album ID in the URL, if the URL is wrong, the plugin cannot find an album to display

= Can I change the thumbnails displayed from the shortcode? =
Yes, find Facebook Albums under the Settings area of your dashboard.

= How do I use Facebook Albums =
Simply use the [fbalbum url=] shortcode. Not sure how it will look? Copy the example in the description.

= I want different thumb sizes for my pictures =
By defauly Facebook stores images in 9 sizes, one being original, and two a pretty similar in miniture thumbnails. I incorporated the basic thumbnails to try and keep the Facebook appeal to the plugin.

== Changelog ==

= 2.0.5.3 =
* Fix reverse albums, broken in 2.0.5.2 with API update
* Fix displaying album title, broken in API changes from 2.0.5.2

= 2.0.5.2 =
* Facebook app authentication fixes (still some bugs, but should be more reliable.)
* Facebook Graph API call improvements - should work for Groups now (if user_groups permission set.)
* Escape photo titles, " and ' were breaking Colorbox.

= 2.0.5.1. =
* Minor release to fix some caching issues.

= 2.0.5 =
* Improved caching, cache will be cleared when widget or post updated (per that album's cache.)
* Facebook authentication and access token issues should be resolved! Tokens only last 2 months.

= 2.0.4 =
* Gave the widget some love, updated the code to match shortcode
* Tidied up the code
* Began localization
* Photos now link to 960px (or closest to it) size photo.
* Fixed missing thumbnails due to smaller image size

= 2.0.3 =
* Add WordPress transients to cache Facebook response to increase performance
* Adjusted thumbnail resolution

= 2.0.1/2.0.2 =
* Clean up a few missed things in code.
* Realized Personal albums and Page albums have different URLs which was causing an error.
* Fix expiring access token issue (need further testing).

= 2.0 =
* Major overhaul of code
* Utilizes Facebook apps, allows for personal photo albums
* Admin page design overhaul
* Admin page now has newsfeed for developer to keep users aware of upcoming changes or issues.

= 1.0.8 =
* Clean up code
* Bug fixes
* Widgets will now respect reverse album order option

= 1.0.7.1 =
* Misc bug fixes.
* Loading graphic and close graphic should always load properly.
* Widgets now use Lightbox as well.
* Added titles to widgets

= 1.0.7 =
Can specify different albums for each page, if no URL specified uses default within the widget setting.

= 1.0.6a =
Added gallery support, fixed Lightbox images. Users can now go left to right to browse album

= 1.0.6 =
Allows you to add limit= to shorten amount of photos.

= 1.0.4 =
Pulls all pictures from album at one time. Uses Lightbox2 to display pictures within site instead of linking to Facebook, also displays captions within Lightbox.

= 1.0.3 =
Added options page under Settings menu to allow image thumbnail to be set.

= 1.0.2a =
Remove default URL, reports it was overriding input.

= 1.0.2 =
Added ability to change thumbnail sizes and quantity for the widget.

= 1.0.1 =
 First release!

== Screenshots ==

1. The widget form
2. Page using the shortcode
3. Plugin Settings
