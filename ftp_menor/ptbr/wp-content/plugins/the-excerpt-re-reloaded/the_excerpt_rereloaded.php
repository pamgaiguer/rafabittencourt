<?php
/*
Plugin Name: The Excerpt re-reloaded
Plugin URI: http://www.lucabiagini.com/2008/11/wordpress-plugin-the-excerpt-re-reloaded/
Description: This plugin does something more than the built-in excerpt function. It lets you choose excerpt length, allowed html tags, the link text to full post, the html container of the excerpt (<p>,<div>,<span> or plain text) and whether to show or not emoticons.
Version: 0.3.2
Author: Luca Biagini
Author URI: http://www.lucabiagini.com

    Copyleft 2008-2009 Luca Biagini (http://www.lucabiagini.com)
    the excerpt re-reloaded is released under the GNU General Public
    License: http://www.gnu.org/licenses/gpl.txt

    This is a WordPress plugin (http://wordpress.org). WordPress is
    free software; you can redistribute it and/or modify it under the
    terms of the GNU General Public License as published by the Free
    Software Foundation; either version 2 of the License, or (at your
    option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
    General Public License for more details.

    For a copy of the GNU General Public License, write to:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA  02111-1307
    USA

    You can also view a copy of the HTML version of the GNU General
    Public License at http://www.gnu.org/copyleft/gpl.html

~Changelog:
0.1  (Nov 2008)
First version with the first 3 parameters. More coming soon. Maybe.

0.3  (Dec 2008)
4 parameters now
Allowed tags includes the option "all" to accept every tag.
User can choose the container for the more link: <p>, <span>, <div> or none

0.3.1 (Mar 2009)
Elements in brackets i.e. [audio:song.mp3] are automatically removed now. Credits to Karsten Krohn.

0.3.2 (Sep 2009)
You can now choose whether to show or not emoticons.

*/

function the_excerpt_rereloaded($words = 40, $link_text = 'Continue reading this entry &#187;', $allowed_tags = '', $container = 'p', $smileys = 'no' )
{
	global $post;
        
    if ( $allowed_tags == 'all' ) $allowed_tags = '<a>,<i>,<em>,<b>,<strong>,<ul>,<ol>,<li>,<span>,<blockquote>,<img>';
    
    $text = preg_replace('/\[.*\]/', '', strip_tags($post->post_content, $allowed_tags));

    $text = explode(' ', $text);
    $tot = count($text);
    
    for ( $i=0; $i<$words; $i++ ) : $output .= $text[$i] . ' '; endfor;
    
    if ( $smileys == "yes" ) $output = convert_smilies($output);
 
    ?><p><?php echo force_balance_tags($output) ?><?php if ( $i < $tot ) : ?> ...<?php else : ?></p><?php endif; ?>
    <?php if ( $i < $tot ) : 
        if ( $container == 'p' || $container == 'div' ) : ?></p><?php endif; 
            if ( $container != 'plain' ) : ?><<?php echo $container; ?> class="more"><?php if ( $container == 'div' ) : ?><p><?php endif; endif; ?>
            
    <a href="<?php the_permalink(); ?>" title="<?php echo $link_text; ?>"><?php echo $link_text; ?></a><?php
    
            if ( $container == 'div' ) : ?></p><?php endif; if ( $container != 'plain' ) : ?></<?php echo $container; ?>><?php endif;
        if ( $container == 'plain' || $container == 'span' ) : ?></p><?php endif; 
        endif;
        
}