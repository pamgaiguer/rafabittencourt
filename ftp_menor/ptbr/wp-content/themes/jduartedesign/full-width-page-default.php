<?php
 /*
 Template Name: Full Width
 */
 ?>
 
 <?php get_header(); ?>

<div id="content-fullwidth">

<?php if (have_posts()) : ?>
		
<?php while (have_posts()) : the_post(); ?>
				
<div class="post" id="post-<?php the_ID(); ?>"></div>

<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div>

</div>

<br/>

<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

<?php get_footer(); ?>