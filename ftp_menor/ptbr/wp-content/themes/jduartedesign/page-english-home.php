<?php include('header-english.php'); ?>

<br/>

<table width="854" border="0" align="center">
<tr>
<th width="250" scope="row"><div align="center"><a href="https://www.youtube.com/user/RafaInjecaonaTesta" target="_blank">INJE&Ccedil;&Atilde;O NA TESTA CHANNEL</a></div></th>
<td width="340"><div align="center"><strong><a href="https://www.youtube.com/watch?v=guk-0-nE_Tw&list=PL_TqAU4yPHO5KmXhIscdvagj2CyDx6XHW" target="_blank">#PLAY ANGRA</a></strong></div></td>
<td width="250"><div align="center"><strong><a href="https://www.youtube.com/user/AngraChannel" target="_blank">ANGRA CHANNEL</a></strong></div></td>
</tr>
<tr>

<th scope="row">

<iframe width="250" height="180" src="http://www.youtube.com/embed/CQ3HQzjgc3k" frameborder="0" allowfullscreen></iframe><br/><br/>

</th>
<td><center>

<iframe width="250" height="180" src="http://www.youtube.com/embed/guk-0-nE_Tw" frameborder="0" allowfullscreen></iframe><br/><br/>

</center>
</td>
<td>

<iframe width="250" height="180" src="http://www.youtube.com/embed/XxY5pHps5xU" frameborder="0" allowfullscreen></iframe><br/><br/>

</td>
</tr>
</table>

<div id="content_index">

<img src="http://rafaelbittencourt.com/ptbr/wp-content/themes/jduartedesign/images/titulos/music_player_index.png">

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?> 
<?php query_posts("cat=9&showposts=5&paged=$paged"); ?> 
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">

<?php if (is_search()) { ?>
<?php the_excerpt(); ?>
<?php } else { ?><br />
<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div><br/>
<?php } ?>

<img src="<?php bloginfo('template_url'); ?>/images/lined_broken_index.png" alt="" />

</div>
	
<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

</div>











<div id="sidebar_index">

<img src="http://rafaelbittencourt.com/ptbr/wp-content/themes/jduartedesign/images/titulos/videos_index.png">

<?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; ?> 
<?php query_posts("cat=8&showposts=5&paged=$paged"); ?> 
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">

<?php if (is_search()) { ?>
<?php the_excerpt(); ?>
<?php } else { ?><br/>
<div align="justify"><?php the_content(__('Leia mais || Read More')); ?></div>
<?php } ?>

<img src="<?php bloginfo('template_url'); ?>/images/lined_broken_index.png" alt="" />

</div>
	
<?php endwhile; ?>
		
<?php else : ?>

<h2 class="pagetitle"><?php _e('Search Results'); ?></h2>
<p><?php _e('Sorry, but no posts matched your criteria.'); ?></p>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>