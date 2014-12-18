<?php get_header(); ?>

<div id="content">

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
				
<div class="post" id="post-<?php the_ID(); ?>">
<img src="<?php bloginfo('template_url'); ?>/images/post_title01.png" alt="" align="left" />
<br/>

<div id="titulo">
<?php the_title(); ?>
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

<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div>

<?php wp_link_pages(); ?>
<img src="<?php bloginfo('template_url');
?>/images/filed_in.png" alt="" align="left" /> <?php the_category(' &#183;  ') ?><br/>
</div>

<br/>

<div id="small">
Postado em <?php the_time('F jS, Y') ?> @ <?php the_time() ?> | <?php if(function_exists('the_views')){the_views();}?>
</div>

<br/><hr/><br/>		
<?php comments_template(); ?>
	
<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

<br/><hr/>

<h2>Notícias mais lidas</h2>
<?php if (function_exists('get_most_viewed')): ?>
<ul>
<?php get_most_viewed(); ?>
</ul>
<?php endif; ?>

<br/><br/>
<?php get_useronline();?> 

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>