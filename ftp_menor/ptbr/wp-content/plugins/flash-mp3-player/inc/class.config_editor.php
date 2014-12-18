<?php

class FMP_Config_Editor {

    var $config_dir;
    var $current_config;
    var $config_array;
    var $updated_mess;
    var $error_mess;
    var $wp_ver;

    function FMP_Config_Editor() {
        global $fmp_jw_files_dir, $wp_version;
        $this->wp_ver = floatval(substr($wp_version, 0, 3));
        $this->config_dir = $fmp_jw_files_dir . '/configs';
        $this->update_config_filelist();
        add_action('wp_loaded', array(&$this, 'include_color_picker'));
    }

    function update_config_filelist() {
        $this->config_array = array();
        $temp = my_scandir($this->config_dir);
        foreach ($temp as $config_file) {
            if (strpos($config_file, '.xml') !== false) {
                $this->config_array[] = $config_file;
            }
        }
        unset($temp);
    }

    function include_color_picker() {
        if ($this->wp_ver >= 2.7) {
            wp_enqueue_script('farbtastic');
            wp_enqueue_style('farbtastic');
        }
    }

    function add_menu_item() {
        add_options_page('Flash MP3 Player -> Config Editor', 'FMP:Config Editor', 'manage_options', 'fmp_config_editor', array(&$this, 'edit_a_config_file'));
    }

    function edit_a_config_file() {

        if (isset($_POST['delete-config'])) {
            if (!empty($_POST['select-file'])) {
                unlink($this->config_dir . '/' . $_POST['select-file']);
                $this->update_config_filelist();
                $this->updated_mess = '<p>The config file: ' . $_POST['select-file'] . ' has been deleted.</p>';
            }
        }

        if (isset($_POST['edit-config'])) {
            $this->current_config = $_POST['select-file'];
            $this->updated_mess = '<p>Now editing config file: ' . $_POST['select-file'] . '</p>';
        } else {
            $this->current_config = $this->config_array[0];
        }

        $configs = array();
        $this->load_config($configs, $this->config_dir . '/' . $this->current_config);

        if (isset($_POST['create-new-config'])) {
            if (!empty($_POST['new-config-name'])) {
                $filename = sanitize_title($_POST['new-config-name']);
                $this->current_config = $filename . '.xml';
                $this->updated_mess = '<p>Now editing config file: ' . $this->current_config . '</p>';
            } else {
                $this->error_mess = '<p>You should input the file name.</p>';
            }
        }

        if (isset($_POST['save-change']) && is_array($_POST['player-config'])) {
            foreach ($configs as $key => $val) {
                $configs[$key] = $_POST['player-config'][$key];
            }
            $configs['backcolor'] = '0x' . substr($configs['backcolor'], 1);
            $configs['frontcolor'] = '0x' . substr($configs['frontcolor'], 1);
            $configs['lightcolor'] = '0x' . substr($configs['lightcolor'], 1);
            $this->current_config = $_POST['current_config_file'];
            $this->save_config($configs, $this->config_dir . '/' . $this->current_config);
            $this->update_config_filelist();
            $this->updated_mess = '<p>All change in config file: ' . $this->current_config . ' saved.</p>';
        }

        global $fmp_jw_url;
        $colorwheel = $fmp_jw_url . '/inc/images/color_wheel.png';
        ?>
        <?php
        if ($this->wp_ver < 2.7):

            $jsurl = $fmp_jw_url . '/inc/js/farbtastic.js';
            $cssurl = $fmp_jw_url . '/inc/css/farbtastic.css';
            ?>
            <link rel='stylesheet' href='<?php echo $cssurl; ?>' type="text/css" media="all" />
            <script type="text/javascript" src="<?php echo $jsurl; ?>"></script>
        <?php endif; ?>
        <script type="text/javascript">
            (function($){
                $(document).ready(function(){
                    if($('#backcolor').length < 1) return;
                    $('#backcolor_toggle').click(function(){
                        $('#backcolorpicker').toggle(300);
                    });
                    $('#frontcolor_toggle').click(function(){
                        $('#frontcolorpicker').toggle(300);
                    });
                    $('#lightcolor_toggle').click(function(){
                        $('#lightcolorpicker').toggle(300);
                    });

                    $backcolor = $('#backcolor');
                    $frontcolor = $('#frontcolor');
                    $lightcolor = $('#lightcolor');

                    $.farbtastic('#backcolorpicker', '#backcolor').setColor($backcolor.val());
                    $.farbtastic('#frontcolorpicker', '#frontcolor').setColor($frontcolor.val());
                    $.farbtastic('#lightcolorpicker', '#lightcolor').setColor($lightcolor.val());

                    $('#backcolorpicker').hide();
                    $('#frontcolorpicker').hide();
                    $('#lightcolorpicker').hide();
                
                });
            })(jQuery);
        </script>
        <div class="wrap">
            <h2>MP3 Player Config Editor</h2>
            <?php $this->print_message(); ?>
            <p><?php _e('Visit the <a href="http://sexywp.com/fmp">plugin\'s homepage</a> for further details. If you find a bug, or have a fantastic idea for this plugin, <a href="mailto:charlestang@foxmail.com">feel free to send me a email</a> !', 'fmp'); ?><br /><a href="http://sexywp.com/forum/">Now you can visit forum to share your idea with other users.</a></p>
            <?php $phpver = phpversion();
            if (floatval(substr($phpver, 0, 3)) < 5.0) : ?>
                <p>Your PHP version is <?php echo $phpver; ?>. This config editor cannot work in PHP 4.x environment.</p>
        <?php endif; ?>
            <p>
                <strong>Feel absolutely free to </strong>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="Z8WPA64G3D79W">
                <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" width="1" height="1">
            </form>
        </p>
        <form method="post">

            <h3> Config Files Management</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Choose config file:</th>
                    <td>
                        <select id="select-file-to-edit" name="select-file">
        <?php foreach ($this->config_array as $configfile) : ?>
                                <option value="<?php echo $configfile; ?>" <?php if ($configfile == $this->current_config)
                echo 'selected="selected" '; ?>><?php echo $configfile; ?></option>
        <?php endforeach; ?>
                        </select>
                        <input name="edit-config" type="submit" value="Edit" class="button-primary"/>
                        <input name="delete-config" type="submit" value="Delete" class="button-highlighted"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Create a new config:</th>
                    <td>
                        File Name:<input name="new-config-name" type="text" class="regular-text" />
                        <input name="create-new-config" type="submit" value="Create a New Config" class="button-secondary"/>
                    </td>
                </tr>
            </table>

            <h3> Editing Config File: <span style="color:red"><?php echo $this->current_config; ?></span> </h3>

            <table><tr>
                    <td>

                        <table class="form-table">

                            <tr valign="top">
                                <th scope="row">Display Panel</th>
                                <td> <fieldset><legend class="hidden">Show display</legend><label for="show-display">
                                            <input type="hidden" name="player-config[showdisplay]" value="false" />
                                            <input id="show-display" type="checkbox" name="player-config[showdisplay]" value="true" <?php if ($configs['showdisplay'] == 'true')
            echo ' checked="checked" '; ?> />
                                            check to show display panel.</label>
                                    </fieldset></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Playlist</th>
                                <td>
                                    <fieldset><legend class="hidden">Show display</legend><label for="show-playlist">
                                            <input type="hidden" name="player-config[showplaylist]" value="false" />
                                            <input id="show-playlist" type="checkbox" name="player-config[showplaylist]" value="true" <?php if ($configs['showplaylist'] == 'true')
            echo ' checked="checked" '; ?> />
                                            check to show playlist panel.</label>
                                    </fieldset></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Auto Start</th>
                                <td>
                                    <fieldset><legend class="hidden">Auto Start</legend><label for="autostart">
                                            <input type="hidden" name="player-config[autostart]" value="false" />
                                            <input id="autostart" type="checkbox" name="player-config[autostart]" value="true" <?php if ($configs['autostart'] == 'true')
            echo ' checked="checked" '; ?> />
                                            check to auto start playing music when page loaded.</label>
                                    </fieldset></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Shuffle</th>
                                <td> <fieldset><legend class="hidden">Shuffle</legend><label for="shuffle">
                                            <input type="hidden" name="player-config[shuffle]" value="false" />
                                            <input id="shuffle" type="checkbox" name="player-config[shuffle]" value="true" <?php if ($configs['shuffle'] == 'true')
            echo ' checked="checked" '; ?> />
                                            check to shuffle the play order.</label>
                                    </fieldset></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">Color Schema</th>
                                <td> <fieldset><legend class="hidden">Color Schema</legend>
                                        <p>Back Color: <input type="text" id="backcolor" name="player-config[backcolor]" value="<?php echo "#" . substr($configs['backcolor'], 2); ?>" size="8" />
                                            <img id="backcolor_toggle" src="<?php echo $colorwheel; ?>" />
                                        <div id="backcolorpicker"></div>
                                        <br/><small>Notice:When you set the background image, this will not work.</small>
                                        </p>
                                        <p>Front Color: <input type="text" id="frontcolor" name="player-config[frontcolor]" value="<?php echo "#" . substr($configs['frontcolor'], 2); ?>" size="8" />
                                            <img id="frontcolor_toggle" src="<?php echo $colorwheel; ?>" />
                                        <div id="frontcolorpicker"></div>
                                        </p>
                                        <p>Light Color: <input type="text" id="lightcolor" name="player-config[lightcolor]" value="<?php echo "#" . substr($configs['lightcolor'], 2); ?>" size="8" />
                                            <img id="lightcolor_toggle" src="<?php echo $colorwheel; ?>" />
                                        <div id="lightcolorpicker"></div>
                                        </p>
                                    </fieldset></td>
                            </tr>

                            <?php
                            $repeat_option = array(
                                'all' => array('attr' => ' value="all" ', 'text' => 'All'),
                                'one' => array('attr' => ' value="one" ', 'text' => 'One'),
                                'list' => array('attr' => ' value="list" ', 'text' => 'List'),
                                'none' => array('attr' => ' value="none" ', 'text' => 'None')
                            );
                            $linktarget_option = array(
                                '_self' => array('attr' => ' value="_self" ', 'text' => 'Open in this Window or Frame'),
                                '_blank' => array('attr' => ' value="_blank" ', 'text' => 'Open New'),
                                '_top' => array('attr' => ' value="_top" ', 'text' => 'Open in this Window and replace content'),
                            );
                            $repeat_option[$configs['repeat']]['attr'] .= 'selected="selected" ';
                            $linktarget_option[$configs['linktarget']]['attr'] .= 'selected="selected" ';
                            ?>
                            <tr valign="top">
                                <th scope="row"><label for="repeat">Repeat</label></th>
                                <td>
                                    <select id="repeat" name="player-config[repeat]">
        <?php
        foreach ($repeat_option as $r_o) {
            echo '<option ', $r_o['attr'], '>', $r_o['text'], '</option>';
        }
        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="linktarget">Link Target</label></th>
                                <td>
                                    <select id="link-target" name="player-config[linktarget]">
        <?php
        foreach ($linktarget_option as $l_o) {
            echo '<option ', $l_o['attr'], '>', $l_o['text'], '</option>';
        }
        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="volume">Volume</label></th>
                                <td><input id="volume" type="text" name="player-config[volume]" value="<?php echo $configs['volume']; ?>" size="15" /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="jpgfile">Background Image URL</label></th>
                                <td>
                                    <input type="text" id="jpgfile" class="regular-text" name="player-config[jpgfile]" value="<?php echo $configs['jpgfile']; ?>" />
                                    <br /><small>Notice: You should input the background image's URL but NOT the server file path.</small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label for="callback">Javascript Callback*</label></th>
                                <td>
                                    <input type="text" id="callback" class="regular-text" name="player-config[callback]" value="<?php echo isset($configs['callback'])?$configs['callback']:''; ?>" />
                                    <br /><small>Notice: If you don't konw what it is, please leave it blank.</small>
                                </td>
                            </tr>
                        </table>
                        <p>
                            <input type="hidden" value="<?php echo $this->current_config; ?>" name="current_config_file" />
                            <input type="submit" value="Save changes" name="save-change" class="button-primary" />
                            <input type="reset" value="Reset" name="reset" class="button-highlighted" /></p>

                    </td>
                    <td>

                        <!--Here Print a Player to preview the effect-->
                        <?php
                        global $fmp_jw_util, $fmp_jw_url, $fmp_jw_files_dir, $fmp_jw_files_url;
                        $temp = array();
                        $temp = my_scandir($fmp_jw_files_dir . '/playlists');
                        foreach ($temp as $listname) {
                            if (strpos($listname, 'playlist.xml') !== false) {
                                $testlistfile = $listname;
                                break;
                            }
                        }
                        unset($temp);

                        $imginfo = @getimagesize($configs['jpgfile']);
                        $width = intval($imginfo[0]) > 0 ? intval($imginfo[0]) : 177;
                        $height = intval($imginfo[1]) > 0 ? intval($imginfo[1]) : 280;

                        $tag_args = array(
                            'player_url' => $fmp_jw_url . '/player/player.swf',
                            'config_url' => $fmp_jw_files_url . '/configs/' . $this->current_config,
                            'playlist_url' => $fmp_jw_files_url . '/playlists/' . $testlistfile,
                            'width' => $width,
                            'height' => $height
                        );
                        ?>
                        <h4>Preview</h4>
                        <div style="width:100%;height:100%;text-align:center;background-color:#efefef;padding:15px 20px;border:1px solid #0f0">
                            <span style="color:blue"> Suggest dimension: Width-<?php echo $width; ?> Height-<?php echo $height; ?> </span>
                            <span style="color:blue"> You should dimension infos in Widget admin panel or the place you call the player. </span>
                            <div style="margin:10px auto;"><?php $fmp_jw_util->print_player($tag_args); ?></div>
                        </div>

                    </td>
                </tr></table>

        </form>
        </div>


        <?php
    }

    function print_message() {
        if (!empty($this->updated_mess)) {
            echo '<div class="updated">', $this->updated_mess, '</div>';
        } else if (!empty($this->error_mess)) {
            echo '<div class="error">', $this->error_mess, '</div>';
        }
    }

    function load_config(&$configs, $filename) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->load($filename);
        $config_nodes = $doc->firstChild->childNodes;
        foreach ($config_nodes as $config_node) {
            if ($config_node->nodeType == XML_ELEMENT_NODE)
                $configs[$config_node->nodeName] = $config_node->nodeValue;
        }
    }

    function save_config(&$configs, $filename) {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $roote = $doc->createElement('mp3config');
        foreach ($configs as $key => $val) {
            $node = $doc->createElement($key, $val);
            $roote->appendChild($node);
        }
        $doc->appendChild($roote);
        $doc->formatOutput = true;
        $doc->save($filename);
    }

}
?>
