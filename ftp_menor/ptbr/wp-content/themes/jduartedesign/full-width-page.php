<?php
/*
Template Name: Full Width
*/
?>

<?php
  $post = $wp_query->post;
  if (is_page('dates' , '')) {
      include(TEMPLATEPATH.'/full-width-page-news.php');
  }
  else{
      include(TEMPLATEPATH.'/full-width-page-default.php');
  }
?>