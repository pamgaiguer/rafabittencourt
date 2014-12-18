<?php get_header(); ?>

<div id="content">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<div align="justify"><?php the_content(__('Read the rest of this page &raquo;')); ?></div>
<?php wp_link_pages(); ?>
<?php edit_post_link(__('Edit'), '<p>', '</p>'); ?>
</div>

<?php endwhile; endif; ?>
</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>