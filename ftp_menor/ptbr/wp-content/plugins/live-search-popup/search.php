<?php include "header.php"; ?>

<div class="clearer">&nbsp;</div>
 <div class="post">
  <?php if (have_posts()) : ?>
   <h2 class="searchresult">Search Results</h2>
    <div class="searchdetails"> Search results for "<?php echo ""."$s"; ?>" </div>
     <?php while (have_posts()) : the_post(); ?>
     <h2 class="searchresult"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
     <div class="searchinfo"><?php _e("("); ?> <?php the_category(' and') ?> <?php _e(")"); ?></div>
     <div class="clearer">&nbsp;</div>
      <?php the_excerpt() ?>
       <div class="searchinfo"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>">(to view associated entry please click here)</a></div>

  <?php endwhile; ?>
<?php else : ?>
 NothingNot Found
<?php endif; ?>
</div>

<div class="postnavigation">
 <div class="right"><?php posts_nav_link('','','previous posts + &raquo;') ?></div>
 <div class="left"><?php posts_nav_link('','&laquo; + newer posts ','') ?></div>
</div>

<div id="bottomcontent">&nbsp;</div>

<?php include "footer.php"; ?>