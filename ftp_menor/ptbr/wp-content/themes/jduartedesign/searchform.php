<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<p>
<input type="text" value="<?php echo wp_specialchars($s, 1); ?>" name="s" id="s" />
<input type="submit" id="searchsubmit" value="<?php _e('Pesquisa/Search'); ?>" />
</p>
</form>