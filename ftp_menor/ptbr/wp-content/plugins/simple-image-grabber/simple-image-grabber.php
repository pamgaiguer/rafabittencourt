<?php
/*
Plugin Name: Simple Image Grabber
Plugin URI: http://bavotasan.com/2009/simple-image-grabber-wordpress-plugin/
Description: Display one or all images from a post's content.
Author: c.bavota
Version: 1.0.3
Author URI: http://bavotasan.com
License: GPL2
*/

/*  Copyright 2012  c.bavota  (email : cbavota@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Retreive one or all images from a post's content and display it.
 *
 * @param	int $num			Index number of image to retrieve 
 * @param	array $num			First parameter can also be an array
 * @param	int $width			Width setting for retrieved image
 * @param	int $height			Height setting for retrieved image
 * @param	string $class		Class setting for retrieve image
 * @param	boolean $permalink	Link image to parent post
 * @param	boolean $echo		Echo or return result
 *
 * @uses	wp_parse_args()		Parse the defaults and array parameters
 * @uses	get_permalink()		Get permalink to parent post
 * @uses	get_the_content()	Get the post content
 *
 * @return	string  Echo or return requests images
 *
 * @author c.bavota
 */
function images( $num = 1, $width = null, $height = null, $class = 'alignleft', $permalink = true, $echo = true ) {
	
	// Parse all of the defaults and parameters
	if ( is_array( $num ) ) {
		
		$defaults = array(
			'number' => 1,
			'width' => '',
			'height' => '',
			'class' => 'alignleft',
			'permalink' => true,
			'echo' => true	
		);
		
		$args = wp_parse_args( $num, $defaults );
	
		extract( $args, EXTR_OVERWRITE );

	} else {
		
		// Fix for number parameter
		$number = $num;
		
	}
	
	// Set $more variable to retrieve full post content
	global $more;
	$more = 1;

	// Setup variables according to passed parameters
	$size = empty( $width ) ? '' : ' width="' . $width . 'px"';
	$size = empty( $height ) ? $size : $size . ' height="' . $height . 'px"'; 
	$class = empty( $class ) ? '' : ' class="' . $class . '"';
	$link = empty( $permalink ) ? '' : '<a href="' . get_permalink() . '">';
	$linkend = empty( $permalink ) ? '' : '</a>';
	
	$content = get_the_content();
	
	// Number of images in content
	$count = substr_count( $content, '<img' );
	$start = 0;
	
	// Loop through the images
	for ( $i = 1; $i <= $count; $i++ ) {

		// Get image src
		$imgBeg = strpos( $content, '<img', $start );
		$post = substr( $content, $imgBeg );
		$imgEnd = strpos( $post, '>' );
		$postOutput = substr( $post, 0, $imgEnd + 1 );

		// Replace width || height || class
		if ( $width || $height )
			$replace = array( '/width="[^"]*" /', '/height="[^"]*" /', '/class="[^"]*" /' );
		else
			$replace = '/class="[^"]*" /';

		$postOutput = preg_replace( $replace, '', $postOutput );

		$image[$i] = $postOutput;

		$start = $imgBeg + $imgEnd + 1;

	}

	// Go through the images and return/echo according to above parameters
	if ( ! empty( $image ) ) {
	
		if ( 'all' == $number ) {
	
			$x = count( $image );
			$images = '';
			
			for ( $i = 1; $i <= $x; $i++ ) {
	
				if ( stristr( $image[$i], '<img' ) ) {
	
					$theImage = str_replace( '<img', '<img' . $size . $class, $image[$i] );
					$images .= $link . $theImage . $linkend;
				
				}
				
			}
	
		} else {
	
			if ( stristr( $image[$number], '<img' ) ) {
	
				$theImage = str_replace( '<img', '<img' . $size . $class, $image[$number] );
				$images = $link . $theImage . $linkend;
	
			}
			
		}
	
		// Reset the $more tag back to zero
		$more = 0;
	
		// Echo or return 
		if ( ! empty( $echo ) )
	    	echo $images;
	    else
	    	return $images;

	}

}