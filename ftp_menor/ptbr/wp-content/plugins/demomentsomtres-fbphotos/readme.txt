=== DeMomentSomTres FBPhotos ===
Contributors: marcqueralt
Tags: facebook pages, facebook photos, photo gallery, facebook albums
Requires at least: 3.4.1
Tested up to: 3.4.1
Stable tag: trunk
License: GPLv2 or later
Version: 1.0

DeMomentSomTres FBPhotos inserts facebook page albums inside of the contents via shortcode. 
As facebook pages are public and also the foto albums, no login is required. Neither an app id.

== Description ==

DeMomentSomTres FBPhotos inserts facebook page albums inside of the contents via shortcode. 
As facebook pages are public and also the foto albums, no login is required. Neither an app id.

== Installation ==

* Usual installation process.
* You have to customize styles in template or where you need them. Proposed settings: 
** .dmst-fbphoto {float:left;width:130px;margin-right:30px;margin-bottom:30px;}
** .dmst-fbphoto a img {width:100%;height:auto;padding:5px;border:1px solid #ccc;margin-right:20px;}
** .dmst-fbphoto a:hover img {border-color:#3B5998;}
* Shortcode syntax: 
** minimal [dmst-fbphotos id="ALBUM ID REQUIRED"]
** with classes [dmst-fbphotos id="ALBUM ID REQUIRED" class="ALL CLASSES YOU WANT FOR THE GALLERY CONTAINER"]
** if you want to show captions you should add parameter caption="ok"

== Changelog ==
=1.0=
* Initial release
