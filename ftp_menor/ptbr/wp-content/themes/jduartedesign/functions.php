<?php
       // Registrar áreas de widgets
function theme_widgets_init() {
 // Área 1
 register_sidebar( array (
 'name' => 'Primary Widget Area',
 'id' => 'primary_widget_area',
 'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
 'after_widget' => "</div>",
 'before_title' => '<h3 class="">',
 'after_title' => '</h3>',
  ) );
 
 // Área 2
 register_sidebar( array (
 'name' => 'Secondary Widget Area',
 'id' => 'secondary_widget_area',
 'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
 'after_widget' => "</div>",
 'before_title' => '<h3 class="">',
 'after_title' => '</h3>',
  ) );
} // end theme_widgets_init
 
add_action( 'init', 'theme_widgets_init' );

$preset_widgets = array (
 'primary_widget_area'  => array( 'search', 'pages', 'categories', 'archives' ),
 'secondary_widget_area'  => array( 'links', 'meta' )
);
if ( isset( $_GET['activated'] ) ) {
 update_option( 'sidebars_widgets', $preset_widgets );
}
// update_option( 'sidebars_widgets', NULL );

// Verificar widgets nas áreas de widgets
function is_sidebar_active( $index ){
  global $wp_registered_sidebars;
 
  $widgetcolums = wp_get_sidebars_widgets();
 
  if ($widgetcolums[$index]) return true;
 
 return false;
} // end is_sidebar_active
?>
