<?php

/* Plugin Name: DeMomentSomTres FBPhotos
 * Plugin URI: http://demomentsomtres.com/english/wordpress-plugins/demomentsomtres-facebook-photos/
 * Description: Replace [dmst-fbphotos id="ALBUM_ID" class="CLASSES"] with a link to the facebook album
 * Version: 1.0.1
 * Author: Marc Queralt
 * Author URI: http://demomentsomtres.com/catala/author/marc
 */

// Register the post code
add_shortcode('dmst-fbphotos', 'dmst_fbphotos_shortcode');

// The callback function that will replace [dmst-fbphotos]
function dmst_fbphotos_shortcode($attr) {
    $default = array(
        'id' => '',
        'class' => '',
        'captions' => '',
    );
    $opcions = shortcode_atts($default, $attr);
    if ('' == $opcions['id']):
        return '';
    endif;
//    $url = 'https://graph.facebook.com/' . $opcions['id'];
//    $json = json_decode(file_get_contents($url));
//    if (isset($json->error)):
//        return '';
//    endif;
//    $album_nom = $json->name;
//    $album_link = $json->link;
    $url = 'https://graph.facebook.com/' . $opcions['id'] . '/photos';
    $json = json_decode(file_get_contents($url));
    if (isset($json->error)):
        return '';
    endif;
    $classes = ('' == $opcions['class']) ? '' : ' class="' . $opcions['class'] . '" ';
    $resultat = '<div' . $classes . '>';
    foreach ($json->data as $imatge):
        $resultat.='<div class="dmst-fbphoto">';
        $resultat.='<a href="' . $imatge->link . '" target="_blank">';
        $resultat.='<img src="' . $imatge->picture . '" />';
        if ('' != $opcions['captions']):
            $resultat.='<p>' . $imatge->name . '</p>';
        endif;
        $resultat.='</a>';
        $resultat.='</div>';
    endforeach;
    $resultat.='<div style="clear:both;"></div></div>';
    return $resultat;
}

?>