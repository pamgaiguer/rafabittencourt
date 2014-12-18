=== Srizon Facebook Album ===
Contributors: afzal_du
Donate link: http://www.srizon.com/wordpress-plugin/srizon-facebook-album
Tags: Facebook, Album, Gallery, Photo Album, Photo Gallery, Facebook Connect, Facebook Album, Facebook Gallery
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 1.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This wordpress plugin fetches the facebook albums or the whole galleries from your Facebook Fanpage/Fanpages and display them on your site as albums and galleries. You can add as many albums and galleires as you want. It will generate the shortcodes automatically which you can copy/paste into your post or page

== Description ==

This wordpress plugin fetches the facebook albums or the whole galleries from your Facebook Fanpage/Fanpages and display them on your site as albums and galleries. You can add as many albums and galleires as you want. It will generate the shortcodes automatically which you can copy/paste into your post or page
= Demo =
* http://promy.srizon.com/wp/

= Free Version's Limitation =
This Free version shows only 25 images per album and 25 (or less) album covers per gallery. Also image caption (or description) is not fetched from facebook to show below the lightbox

= Pro Version =
Pro version shows All the images from each album and all the album covers from each gallery. Image descriptions are also fetched for showing as image caption on lightbox.
You can also include/exclude albums in gallery view in pro version

* Go to: http://www.srizon.com/wordpress-plugin/srizon-facebook-album to get the pro version

== Installation ==

1. Upload srizon-facebook-album folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the admin Menu 'FB Album' and Submenus under it to create albums and galleries
4. Get the auto generated shortcode and copy/paste into a page or post
5. Some template inserts extra <p> tags into the plugin's output which breaks the layout. In that case try using raw around the shortcode... like [raw]auto_generated_shortcode_here[/raw] (example: [raw][srizonfbgallery id=1][/raw] )
if your template doesn't support [raw] tag then you may also need to install this plugin
* http://wordpress.org/plugins/raw-html/

== Frequently asked questions ==

= Can I add my own lightbox for the images =
Yes, You can. There's one lightbox added with this plugin. Also You'll find instructions inside on how to add fancybox as your lightbox. The process of adding other lightbox should be similar.

= Why there's only 25 images on my album =
This Free version shows only 25 images per album and 25 (or less) album covers per gallery. Get the Pro version to show all the images.

= Why the layout breaks on my template =
Your template is using custom formatter overriding default wordpress formatter. Try using [raw]...[/raw] around the shortcode.
Example: [raw][srizonfbgallery id=1][/raw]
if your template doesn't support [raw] tag then you may also need to install this plugin
* http://wordpress.org/plugins/raw-html/

== Screenshots ==
1. Gallery View (First Level)
2. Gallery View (Second Level) - Similar To Album View
3. Default Lightbox (Any lightbox can be added)
4. Common Option panel (admin)
5. Albums Page (admin)
6. Gallery Page (admin)
7. Adding New Gallery (Adding new album is similar)

== Changelog ==

= 1.0.0 =
* First Release

= 1.1.0 =
*Minor Edit

= 1.1.1 =
*Added wp_remote_get as an alternative for getting the api response.

= 1.1.2 =
*bugfix

= 1.1.3 =
*bugfix

= 1.1.4 =
*Responsive lightbox 'Magnificent Popup' Added