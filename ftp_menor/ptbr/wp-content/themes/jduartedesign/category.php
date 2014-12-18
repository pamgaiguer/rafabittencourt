<?php get_header(); ?>

<div id="content">

Todos os posts da categoria "<?php $category = get_the_category(); echo $category[0]->cat_name; ?>"
<br/><br/>

<?php if (have_posts()): while (have_posts()) : the_post();?>
<?php the_time('F jS, Y') ?> | <?php if(function_exists('the_views')){the_views();}?> | 
<a href="<?php the_Permalink()?>" title="<?php the_title();?>" alt="<?php the_title();?>"><?php the_title();?></a>
<br/>

<?php endwhile; else:?>
<?php endif;?>

<br/>

<?php if(function_exists('wp_pagenavi')) {wp_pagenavi();}?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>