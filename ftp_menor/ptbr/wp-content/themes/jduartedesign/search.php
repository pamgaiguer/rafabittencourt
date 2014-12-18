<?php include('header.php'); ?>

<div id="conteudo_inteiro">

<div id="texto">

<?php if (have_posts()): while (have_posts()) : the_post();?>
<?php the_time('j M Y');?> ||

<div id="texto">
<a href="<?php the_Permalink();?>"> <?php the_title();?></a>
</div>

<?php the_excerpt_rereloaded(45);?><br/><hr/>

<?php endwhile; else:?>
Desculpe, página nao encontrada || Sorry page not found
<?php endif;?>

</div>

</div>

<?php include('footer.php'); ?>