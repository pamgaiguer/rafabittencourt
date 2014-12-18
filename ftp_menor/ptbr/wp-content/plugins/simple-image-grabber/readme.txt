=== Simple Image Grabber ===
Contributors: c.bavota
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1929921
Tags: posts, images from posts, retrieve images, get images, grab images, post images, pictures, photos
Requires at least: 2.7
Tested up to: 3.4
Stable tag: 1.0.3

== Description ==

Display one or all images from a post's content. Options include image width, height, class and permalink.

== Installation ==

1. Unzip the simple-image-grabber.zip file.
2. Upload the `simple-image-grabber` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Place `<?php images(); ?>` within the loop to grab your post's images.

== Frequently Asked Questions == 

1) How can I customize the Simple Image Grabber function?

The images() function accepts five variables. 

The basic use of the function looks like: `<?php images( '1' ); ?>` or `<?php images(); ?>`

This will display the first image from your post, with its default width and height, no class and a permalink to the post.

This is how the function looks with passing all variables directly or through an array:

`<?php images( $number, $width, $height, $class, $permalink, $echo ); ?>`

`<?php 
$defaults = array(
	'number' => 1,
	'width' => null,
	'height' => null,
	'class' => 'alignleft',
	'permalink' => true,
	'echo' => true
);

images( $defaults ); 
?>`

$number = the image you want to pull from your post, ie. the first image from the post ('1') or the second image from the post ('2') and so on. NOTE: If you use 'all', it will display all images from the post.

$width = the width of the displayed image

$height = the height of the displayed image

$class = the class you would like to assign to the displayed image

$permalink = whether you would like the image to link to the post or not

$echo = echo or return the value

The following function will echo the second image from a post (if there is one) with a width of 150px and a height of 200px, the class "alignleft" and no link to the post.

`<?php images( '2', '150', '200', 'alignleft', false ); ?>`

The following will return all images from a post with their original width and height, a class name of alignright and a link to the post.

`<?php 
$args = array(
	'number' => 'all',
	'class' => 'alignright',
	'echo' => 0
);

$all_images = images( $args );
?>`

== Change Log ==

1.0.3 (2012-06-04)
<ul>
<li>Added the ability to pass an array to the images() function</li>
<li>Added the ablity to either echo or return the value string</li>
<li>Cleaned up and improved code</li>
<li>Added comments and PHPDocs</li>
</ul>

1.0.2 (2009-10-15)
<ul>
<li>Added original width and height to img tag if neither is set</li>
</ul>

1.0.1 (2009-03-16)
<ul>
<li>Fixed issue with "All" variable</li>
</ul>

1.0 (2009-03-11)
Initial Public Release
