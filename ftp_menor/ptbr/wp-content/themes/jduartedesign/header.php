<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/capa_icon.jpg" type="image/x-icon" />

<title>
<?php if (is_single())
{
the_title();
echo ' || ';
}
echo 'RAFAEL BITTENCOURT || Official Website';?>
</title>

<?php
    $thumb = get_post_meta($post->ID,'_thumbnail_id',false);
    $thumb = wp_get_attachment_image_src($thumb[0], false);
    $thumb = $thumb[0];
    $default_img = 'http://rafaelbittencourt.com/ptbr/wp-content/themes/jduartedesign/images/capa_icon.jpg';
    ?>
 
<?php if(is_single() || is_page()) { ?>
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php single_post_title(''); ?>" />
    <meta property="og:description" content="<?php
    while(have_posts()):the_post();
    $out_excerpt = str_replace(array("\r\n", "\r", "\n"), "", get_the_excerpt());
    echo apply_filters('the_excerpt_rss', $out_excerpt);
    endwhile;   ?>" />
    <meta property="og:url" content="<?php the_permalink(); ?>"/>
    <meta property="og:image" content="<?php if ( $thumb[0] == null ) { echo $default_img; } else { echo $thumb; } ?>" />
<?php  } else { ?>
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php bloginfo('name'); ?>" />
    <meta property="og:url" content="<?php bloginfo('url'); ?>"/>
    <meta property="og:description" content="<?php bloginfo('description'); ?>" />
    <meta property="og:image" content="<?php bloginfo('template_url'); ?>/images/capa_icon.jpg" />
<?php  }  ?>

<meta name="description" content="<?php if ( is_single() ) { single_post_title('', true); } else { bloginfo('name'); echo " - "; bloginfo('description'); } ?>" />
<meta name="keywords" content="rafael bittencourt, angra, guitar, heavy metal, brasil, bittencourt project, inveção na testa, jduartedesign"/>
<meta name="resource-type" content="document" />
<meta name="URL" content="http://www.rafaelbittencourt.com" />
<meta name="language" content="EN" />
<meta name="company" content="Joao Duarte - J.Duarte Design - www.jduartedesign.com" />
<meta name="author" content="Joao Duarte - J.Duarte Design - www.jduartedesign.com" />
<meta name="copyright" content="Joao Duarte - J.Duarte Design - www.jduartedesign.com" />
<meta name="reply-to" content="contato@jduartedesign.com" />
<meta name="Distribution" content="Global" />
<meta name="googlebot" content="all,index,follow" />
<meta name="robots" content="all,index,follow" />
<meta name="rating" content="general" />
<meta name="doc-type" content="Web Page" />
<meta http-equiv="Content-Language" content="EN" />
<meta property="image" content="<?php bloginfo('template_url'); ?>/images/meta_icon.jpg"/>
<link rel = "image_src" href = "<?php bloginfo('template_url'); ?>/images/capa_icon.jpg"/> 
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_head(); ?>

</head>

<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_get_archives('type=monthly&format=link'); ?>

<?php wp_head(); ?>

</head>

<body>

<div id="wrapper">

<div align="center">

<img src="<?php bloginfo('template_url'); ?>/images/header.png" border="0" usemap="#topbanner-map" />

<map name="topbanner-map" id="topbanner-map">

<area shape="rect" coords="539,310,658,331" href="<?php echo get_option('home');?>/discografia/" alt="Discografia" title="Discografia" />
<area shape="rect" coords="206,335,272,355" href="<?php echo get_option('home');?>/videos/" alt="Vídeos" title="Vídeos" />
<area shape="rect" coords="168,310,220,332" href="<?php echo get_option('home');?>/" alt="Home" title="Home" />
<area shape="rect" coords="296,6,889,190" href="<?php echo get_option('home');?>/" alt="Home" title="Home" />
<area shape="rect" coords="339,310,410,331" href="<?php echo get_option('home');?>/agenda/" alt="Agenda" title="Agenda" />
<area shape="rect" coords="237,310,320,330" href="<?php echo get_option('home');?>/noticias/" alt="Notícias" title="Notícias" />
<area shape="rect" coords="607,334,698,355" href="<?php echo get_option('home');?>/contatos/" alt="Contatos" title="Contatos" />
<area shape="rect" coords="547,335,590,356" href="http://www.vsprodutora.com/site/loja/" target="_blank" alt="Loja" title="Loja" />
<area shape="rect" coords="440,335,529,356" href="<?php echo get_option('home');?>/projetos/" alt="Projetos" title="Projetos" />
<area shape="rect" coords="285,334,422,355" href="<?php echo get_option('home');?>/equipamentos/" alt="Equipamentos" title="Equipamentos" />
<area shape="rect" coords="675,310,732,332" href="<?php echo get_option('home');?>/fotos/" alt="Fotos" title="Fotos" />
<area shape="rect" coords="426,310,522,331" href="<?php echo get_option('home');?>/biografia/" alt="Biografia" title="Biografia" />

</map>

</div>

<div id="all">	