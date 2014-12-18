<?php

class Flash_MP3_Player_Widget extends WP_Widget {
    function Flash_MP3_Player_Widget(){
		$widget_ops = array(
            'classname' => 'widget_flash_mp3_player',
            'description' => __( 'Add a MP3 Player to your sidebar.','fmp')
        );
		$this->WP_Widget('fmp_widget', __('Flash MP3 Player'), $widget_ops);
    }

	function widget($args, $instance) {
        global $fmp_jw_url, $fmp_jw_files_url, $fmp_jw_util;
        extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Flash MP3 Player', 'fmp' ) : $instance['title'], $instance, $this->id_base);

        $tag_args = array(
            'player_url'        => $fmp_jw_url . '/player/player.swf',
            'config_url'        => $fmp_jw_files_url . '/configs/'. $instance['config_url'],
            'playlist_url'      => $fmp_jw_files_url . '/playlists/'. $instance['playlist_url'],
            'width'             => $instance['width'],
            'height'            => $instance['height'],
            'id'                => $instance['container_id'],
            'class'             => $instance['container_class'],
            'transparent'       => false
        );

        echo $before_widget, $before_title, $title, $after_title;
        $fmp_jw_util->print_player($tag_args);
        echo $after_widget;

	}

    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['config_url'] = $new_instance['config_url'];
        $instance['playlist_url'] = $new_instance['playlist_url'];
        $instance['width'] = $new_instance['width'];
        $instance['height'] = $new_instance['height'];
        $instance['container_id'] = $new_instance['container_id'];
        $instance['container_class'] = $new_instance['container_class'];
		return $instance;
	}

    function form($instance) {
        global $fmp_jw_url, $fmp_jw_files_url, $fmp_jw_files_dir, $fmp_jw_util;

        $config_files = array();
        $playlist_files = array();
        $temp = array();
        $temp = my_scandir($fmp_jw_files_dir . '/configs');
        foreach($temp as $name){
            if(strpos($name, '.xml') !== false)
                $config_files[] = $name;
        }
        $temp = array();
        $temp = my_scandir($fmp_jw_files_dir . '/playlists');
        foreach($temp as $name){
            if(strpos($name, '.xml') !== false)
                $playlist_files[] = $name;
        }
        unset($temp,$name);

        $instance = wp_parse_args( (array) $instance, array(
            'title' => __('Flash MP3 Player', 'fmp'),
            'width' => 177,
            'height' => 280,
            'exclude' => '',
            'config_url' => 'fmp_jw_widget_config.xml',
            'playlist_url' => 'fmp_jw_widget_playlist.xml',
            'container_id' => '',
            'container_class' => '',
            ) );

        $title = esc_attr($instance['title']);
        $width = $instance['width'];
        $height = $instance['height'];
        $config_url = esc_attr($instance['config_url']);
        $playlist_url = esc_attr($instance['playlist_url']);
        $container_id = $instance['container_id'];
        $container_class = $instance['container_class'];

        if (count($config_files) > 0 && !in_array($config_url, $config_files))
            $config_url = $config_files[0];
        if (count($playlist_files) > 0 && !in_array($playlist_url, $playlist_files))
            $playlist_url = $playlist_files[0];

        $admin_url = get_option('siteurl') . '/wp-admin';
        ?>

        <p><label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:','fmp');?></label>
            <input id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo $title;?>" class="widefat" />
        </p>
        <p><label for="<?php echo $this->get_field_id('width');?>"><?php _e('Width:', 'fmp');?></label>
            <input id="<?php echo $this->get_field_id('width');?>" name="<?php echo $this->get_field_name('width');?>" type="text" value="<?php echo $width;?>" class="widefat" />
            <br/><small>Just input the number, the unit is pixel.</small>
        </p>
        <p><label for="<?php echo $this->get_field_id('height');?>"><?php _e('Height:','fmp');?></label>
            <input id="<?php echo $this->get_field_id('height');?>" name="<?php echo $this->get_field_name('height');?>" type="text" value="<?php echo $height;?>" class="widefat" />
            <br/><small>Just input the number, the unit is pixel.</small>
        </p>
        <p><label for="<?php echo $this->get_field_id('config_url');?>"><?php _e('Choose a config file:','fmp');?></label>
            <select id="<?php echo $this->get_field_id('config_url');?>" name="<?php echo $this->get_field_name('config_url');?>" >
                <?php foreach($config_files as $config_file) :?>
                <option value="<?php echo $config_file;?>" <?php selected($config_url, $config_file, true);?>><?php echo $config_file;?></option>
                <?php endforeach;?>
            </select>
            <br/><small>You can CREATE or EDIT a config file <a href="<?php echo $admin_url;?>/options-general.php?page=fmp_config_editor">here</a>.</small>
        </p>
        <p><label for="<?php echo $this->get_field_id('playlist_url');?>"><?php _e('Choose a playlist:','fmp');?></label>
            <select id="<?php echo $this->get_field_id('playlist_url');?>" name="<?php echo $this->get_field_name('playlist_url');?>" >
                <?php foreach($playlist_files as $playlist_file) :?>
                <option value="<?php echo $playlist_file;?>" <?php selected($playlist_url, $playlist_file, true); ?>><?php echo $playlist_file;?></option>
                <?php endforeach;?>
            </select>
            <br/><small>You can CREATE or EDIT a playlist <a href="<?php echo $admin_url;?>/options-general.php?page=fmp_playlist_editor">here</a>.</small>
        </p>
        <p><label for="<?php echo $this->get_field_id('container_id');?>"><?php _e('Container <code>id</code>:','fmp');?></label>
            <input id="<?php echo $this->get_field_id('container_id');?>" name="<?php echo $this->get_field_name('container_id');?>" type="text" value="<?php echo $container_id;?>" class="widefat" />
        </p>
        <p><label for="<?php echo $this->get_field_id('container_class');?>"><?php _e('Container <code>class</code>:','fmp');?></label>
            <input type="text" id="<?php echo $this->get_field_id('container_class');?>" name="<?php echo $this->get_field_name('container_class');?>" value="<?php echo $container_class?>"  class="widefat" />
        </p>
        <?php
	}
}