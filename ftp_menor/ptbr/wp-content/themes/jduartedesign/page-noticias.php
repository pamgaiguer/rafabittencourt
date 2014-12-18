<?php get_header(); ?>

<div id="content">

<img src="http://rafaelbittencourt.com/ptbr/wp-content/themes/jduartedesign/images/titulos/noticias.png">

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?> 
<?php query_posts("cat=3&showposts=5&paged=$paged"); ?> 
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<img src="<?php bloginfo('template_url');?>/images/post_title01.png" alt="" align="left" />
<br/>

<div id="titulo">
<a href="<?php the_permalink() ?>" title="<?php _e('Ir para: '); ?>
<?php the_title(); ?>"><?php the_title(); ?></a>
</div>

<br/>

<div id="texto">
<div style="float:left; margin-left: 10px;">
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=80&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:80px; height:20px"></iframe>
</div>
<div class="addthis_toolbox addthis_default_style ">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
</div>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=xa-50c1fdf23e4524ad"></script>
</div>

<?php if (is_search()) { ?>
<?php the_excerpt(); ?>
<?php } else { ?><br />
<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div>
<?php } ?>

<img src="<?php bloginfo('template_url');?>/images/filed_in.png" alt="" />
<?php the_category(' &#183;  ') ?>

<br/><br/>
<div id="small">
Postado em <?php the_time('F jS, Y') ?> @ <?php the_time() ?> | <?php if(function_exists('the_views')){the_views();}?>
</div>

<br/>
<img src="<?php bloginfo('template_url'); ?>/images/lined_broken.png" alt="" />
<br/>

</div>
	
<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

<h2>Páginas mais lidas</h2>
<?php if (function_exists('get_most_viewed')): ?>
<ul>
<?php get_most_viewed(); ?>
</ul>
<?php endif; ?>

<br/>
<?php if(function_exists('wp_pagenavi')) {wp_pagenavi();}?>

<br/><br/><br/>
<?php get_useronline();?> 

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>