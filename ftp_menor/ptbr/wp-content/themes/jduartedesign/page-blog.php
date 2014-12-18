<?php get_header(); ?>

<div id="content">

<img src="http://rafaelbittencourt.com/ptbr/wp-content/themes/jduartedesign/images/titulos/blog.png">

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?> 
<?php query_posts("cat=6&showposts=5&paged=$paged"); ?> 
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<img src="<?php bloginfo('template_url');?>/images/post_title01.png" alt="" align="left" />
<br/>

<div id="titulo">
<a href="<?php the_permalink() ?>" title="<?php _e('Ir para/Go to: '); ?>
<?php the_title(); ?>"><?php the_title(); ?></a>
</div>

<br/>

<br/>
<div id="texto">
<div class="addthis_toolbox addthis_default_style ">
<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=ra-4e71fd3a5dc90a31" class="addthis_button_compact">Share</a>
<span class="addthis_separator">|</span>
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
</div>
<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e71fd3a5dc90a31"></script>
</div>

<?php if (is_search()) { ?>
<?php the_excerpt(); ?>
<?php } else { ?><br />
<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div><br/>
<?php } ?>

<img src="<?php bloginfo('template_url'); ?>/images/comment_on_entry.png" alt="" /></a>
<?php comments_popup_link(__('Comentários || Comments'), __('1 Comment'), __('% Comments')); ?><br/>

<img src="<?php bloginfo('template_url');?>/images/filed_in.png" alt="" />
<?php the_category(' &#183;  ') ?>

<br/><br/>
<div id="small">
Postado em/Posted on <?php the_time('F jS, Y') ?> @ <?php the_time() ?> | <?php if(function_exists('the_views')){the_views();}?>
</div>

<br/>
<img src="<?php bloginfo('template_url'); ?>/images/lined_broken.png" alt="" />
<br/><br/>

</div>
	
<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

Notícias mais lidas || Most viewed news<br/><br/>
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