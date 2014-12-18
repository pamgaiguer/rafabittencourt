<?php
define('SUCCESS', 0);
define('FAIL', -99);
define('NO_WRITE_PERMISSION', -1);
define('FILE_ALREADY_EXISTS', -2);

class FMP_Playlist_Editor{
var $playlist_dir;
var $current_playlist;
var $playlist_array;
var $updated_message;
var $error_message;

function FMP_Playlist_Editor(){
    global $fmp_jw_files_dir;
    $this->playlist_dir = $fmp_jw_files_dir . '/playlists';
    $this->update_playlist_array();
    add_action('wp_loaded', array(&$this, 'include_javascript_libraries'));
    add_action('admin_head', array(&$this, 'admin_css'));
    add_action('admin_head', array(&$this, 'admin_js'));
    add_action('wp_ajax_create_new_playlist', array(&$this, 'create_new_playlist'));
    add_action('wp_ajax_delete_selected_playlist', array(&$this, 'delete_selected_playlist'));
}

function update_playlist_array(){
    $this->playlist_array = array();
    $temp = my_scandir($this->playlist_dir);
    foreach($temp as $playlist){
        if(strpos($playlist, '.xml') !== false){
            $this->playlist_array[] = $playlist;
        }
    }
    unset($temp);
}

function include_javascript_libraries(){
    if (!strpos($_SERVER['QUERY_STRING'], 'fmp_playlist_editor')) return;
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('thickbox');
}

function add_menu_item(){
    add_options_page('Flash MP3 Player -> Playlist Editor', 'FMP:Playlist Editor', 'manage_options',
            'fmp_playlist_editor', array(&$this, 'edit_a_playlist_file'));
}

function edit_a_playlist_file(){
    global $fmp_jw_url;
    if(isset($_POST['delete-playlist'])){
        if(!empty($_POST['select-file'])){
            unlink($this->playlist_dir . '/' . $_POST['select-file']);
            $this->update_playlist_array();
            $this->updated_message = '<p>The playlist file : '. $_POST['select-file'] .' has been deleted.</p>';
        }
    }
    
    if(isset($_POST['edit-playlist'])){
        $this->current_playlist = $_POST['select-file'];
    }else if(isset($_REQUEST['current-edit-list'])){
        $this->current_playlist = $_REQUEST['current-edit-list'];
    }else{
        $this->current_playlist = $this->playlist_array[0];
    }
    
    if(isset($_POST['save-changes'])){
        $newinfos = isset($_POST['songsinfo']) ? $_POST['songsinfo'] : array();
        $songs = array();
        if(!empty($newinfos)) foreach($newinfos as $song){
            $song['annotation'] = stripslashes($song['annotation']);
            $song['info'] = htmlspecialchars(htmlspecialchars_decode($song['info']));
            $songs[] = $song;
        }
        $this->save_playlist($songs, $this->playlist_dir . '/' . $this->current_playlist);
        $this->updated_message = '<p>Your change on '. $this->current_playlist.' has been saved.</p>';
    }
    
    $songs = array();
    if(isset($_POST['create-new-playlist'])){
        if(!empty($_POST['new-filename'])){
            $filename = sanitize_title($_POST['new-filename']);
            $this->current_playlist = $filename . '.xml';
            $this->updated_message = '<p>Playlist file : '. $filename
                    .'.xml has been created and now you are editting it.</p>';
        }else{
            $this->error_message = '<p>You should input the file name.</p>';
            $this->load_playlist($songs, $this->playlist_dir . '/' . $this->current_playlist);
        }
    }else{
        $this->load_playlist($songs, $this->playlist_dir . '/' . $this->current_playlist);
    }


?>

<div class="wrap">
    <h2><?php _e('MP3 Player Playlists Manager','fmp');?></h2>
    <?php $this->display_message();?>
    <div id="playlists-editor-form">
        <div id="left-side">
            <div id="lists-column">
                <div class="meta-box-sortables ui-sortable">
                    <div id="playlists-manager" class="postbox">
                        <h3 class="hndle"><span><?php _e("Playlists Management", "fmp");?></span></h3>
                        <div class="inside">
                            <div id="create-playlists-actions">
                                <input id="create-new-playlist" name="create-new-playlist"
                                       title="Input new playlist's name:"
                                       type="button" class="button button-highlighted thickbox"
                                       value="<?php _e("New");?>"
                                       alt="#TB_inline?height=150&width=300&inlineId=dialogCreatePlaylist"/>
                                <input id="delete-playlist" name="delete-playlist"
                                       title="Delete confirmation:"
                                       type="button" class="button button-highlighted thickbox"
                                       value="<?php _e("Delete");?>"
                                       alt="#TB_inline?height=150&width=300&inlineId=dialogDeletePlaylist"/>
                            </div>
                            <div class="tabs-panel">
                                <form method="post">
                                    <select id="playlists-panel" multiple="multiple" name="select-file">
                                    <?php foreach($this->playlist_array as $playlistfile){
                                        echo '<option value="', $playlistfile, '"';
                                        if ($this->current_playlist == $playlistfile){
                                            echo ' selected="selected" ';
                                        }
                                        echo '>', $playlistfile, '</option>';
                                    }?>
                                    </select>
                                    <input type="hidden" name="edit-playlist" value="1"/>
                                </form>
                            </div><!--.tabs-panel-->
                        </div><!--.inside-->
                    </div><!--#playlist-manager-->
                </div>
            </div><!--#lists-column-->
            <div id="donate-column">
                <p><?php _e('If you like this, please feel free to <br/>buy me a beer.','fmp');?></p>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="Z8WPA64G3D79W">
                    <input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif"
                           border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" width="1" height="1">
                </form>
                <p><?php _e('Visit the <a href="http://sexywp.com/fmp" target="_blank">plugin\'s homepage</a> for further details. Bug report or function suggestion send to <a href="mailto:charlestang@foxmail.com">HERE</a>.', 'fmp'); ?><br />
                    <a href="http://sexywp.com/forum/" target="_blank">Now you can visit forum to share your idea with other users.</a>
                </p>
            </div><!--#donate-column-->
        </div>
        <div id="editor-column">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h3 class="hndle">
                        <span style="color:blue;"><?php echo $this->current_playlist;?></span>
                    </h3>
                        <div id="edit-playlist-actions">
                            <input id="add-new-song" name="add-new-song"
                                   type="button" class="button button-highlighted thickbox"
                                   title="Add a New Song"
                                   value="<?php _e("New");?>"
                                   alt="#TB_inline?inlineId=dialogEditSongInfo"/>
                            <input id="save-current-playlist" name="save-current-playlist"
                                   type="button" class="button button-primary"
                                   value="<?php _e("Save");?>" />
                        </div>
                    <div class="inside">

                        <div id="songs-list" class="tabs-panel">
                            <div id="songs-order">
                                <ul>
                                    <?php 
                                    $song_total_number = count($songs);
                                    for ($ii = 0; $ii < $song_total_number; $ii ++){
                                        echo '<li>', ($ii + 1) ,'</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div id="songs-item">
                                <form method="post">
                                <ul>
                                    <?php $i = 0; foreach ($songs as $song) : ?>
                                        <li class="ui-state-default">
                                        <img width="32px" height="32px" src="<?php echo $song['image'];?>" />
                                        <span class="song-title"><?php echo esc_attr($song['annotation']);?></span>
                                        <span class="song-item-control"><a href="#" name="edit">Edit</a><a href="#" name="delete">Delete</a></span>
                                        <br class="clear" />
                                        <input type="hidden" name="songsinfo[<?php echo $i;?>][annotation]" value="<?php echo esc_attr($song['annotation']);?>" />
                                        <input type="hidden" name="songsinfo[<?php echo $i;?>][location]" value="<?php echo $song['location'];?>" />
                                        <input type="hidden" name="songsinfo[<?php echo $i;?>][info]" value="<?php echo htmlspecialchars($song['info']);?>" />
                                        <input type="hidden" name="songsinfo[<?php echo $i;?>][image]" value="<?php echo $song['image'];?>" />
                                        </li>
                                    <?php $i++; endforeach;?>
                                </ul>
                                    <input type="hidden" name="save-changes" />
                                    <input type="hidden" name="current-edit-list" value="<?php echo $this->current_playlist;?>" />
                                </form>
                            </div>
                            <br class="clear" />
                        </div>
                    </div>
                </div>
            </div>
        </div><!--#editor-column-->
        <br class="clear">    
    </div><!--#playlists-editor-form-->

<!--Dialogs-->
    <div id="dialogCreatePlaylist" style="display:none">
        <p><input type="text" name="new-playlist-name" size="38" /></p>
        <p>
            <label for="dcp001">
                <input type="checkbox" name="overwrite-same" id="dcp001"/>
                <?php _e('Overwrite the same name list.','fmp');?>
            </label>
        </p>
        <input type="button" name="ok" value="<?php _e('OK', 'fmp');?>"/>
        <input type="button" name="cancel" value="<?php _e('Cancel', 'fmp');?>" />
    </div><!--dialogCreatePlaylist-->
    <div id="dialogDeletePlaylist" style="display:none">
        <p><?php _e("Do you really want to DELETE the playlist ",'fmp');?><strong></strong></p>
        <input type="button" name="ok" value="<?php _e('OK', 'fmp');?>"/>
        <input type="button" name="cancel" value="<?php _e('Cancel', 'fmp');?>" />
    </div><!--dialogDeletePlaylist-->
    <div id="dialogEditSongInfo" style="display:none">
        <table class="form-table"><tbody>
            <tr valign="top">
                <th scope="row" style="width:100px;text-align:right"><label for="edit-song-title">Title:</label></th>
                <td><input type="text" id="edit-song-title" name="edit-song-title" size="50"/></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:100px;text-align:right"><label for="edit-song-link">Link:</label></th>
                <td><input type="text" id="edit-song-link" name="edit-song-link" size="50" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:100px;text-align:right"><label for="edit-song-url">Song's URL:</label></th>
                <td><input type="text" id="edit-song-url" name="edit-song-url" size="50"/></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:100px;text-align:right"><label for="edit-song-image">Image's URL:</label></th>
                <td><input type="text" id="edit-song-image" name="edit-song-image" size="50"/></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:100px;text-align:right">&nbsp;</th>
                <td>
                    <input type="button" name="cancel" value="Cancel"/>
                </td>
            </tr>
        </tbody></table>
    </div><!--dialogEditSongInfo-->
<!--End Dialogs-->
</div>

<?php

}

/**
 * This is the ajax callback to create a blank playlist in the directory.
 */
function create_new_playlist(){
    $filename = $this->playlist_dir . '/';
    if (!is_writable($filename)){ //test the write permission
        echo NO_WRITE_PERMISSION;
        die();
    }
    //echo 'test overwrite', sanitize_title($_POST['playlist_name']);
    $overwrite = (isset($_POST['overwrite_same']) && $_POST['overwrite_same']) === 'true' ? true : false;
    $filename = $filename . sanitize_title($_POST['playlist_name']) . '.xml';
    if (file_exists($filename) && !$overwrite){ //file existed and cannot overwrite
        echo FILE_ALREADY_EXISTS;
        die();
    }
    $songs = array();
    $this->save_playlist($songs,  $filename);
    echo sanitize_title($_POST['playlist_name']) . '.xml';
    die();
}

function delete_selected_playlist(){
    $filename = $this->playlist_dir . '/' . $_POST['playlist_name'];
    if (!is_writable($filename)){ //test the write permission
        echo NO_WRITE_PERMISSION;
        die();
    }
    if (unlink($filename)){
        echo SUCCESS;
    }else {
        echo FAIL;
    }
    die();
}

function display_message(){
    if(!empty($this->updated_message)){
        echo '<div id="message" class="updated">' . $this->updated_message . '</div>';
    }else if(!empty($this->error_message)){
        echo '<div id="message" class="error">' . $this->error_message . '</div>';
    }
}


function load_playlist(&$songs, $filename){
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->load($filename);
    $tracklists = $doc->getElementsByTagName('trackList');
    $songs = array();
    foreach($tracklists as $tracklist){

        $tracks = $tracklist->getElementsByTagName('track');
        foreach($tracks as $track){
            $song = array();
            foreach($track->childNodes as $node){
                if($node->nodeType == XML_ELEMENT_NODE){
                    $song[$node->nodeName] = $node->nodeValue;
                }
            }
            $songs[] = $song;
        }

    }
}

function save_playlist(&$songs, $filename){
    $doc = new DOMDocument('1.0', 'UTF-8');
    $root = $doc->createElement('playlist');
    $tracklist = $doc->createElement('trackList');
    foreach($songs as $song){
        $track = $doc->createElement('track');
        foreach($song as $key => $val){
            $track->appendChild($doc->createElement($key, $val));
        }
        $tracklist->appendChild($track);
    }
    $root->appendChild($tracklist);
    $doc->appendChild($root);
    $doc->formatOutput = true;
    $doc->save($filename);
}




function admin_js(){
    global $fmp_jw_url;
    if (!strpos($_SERVER['QUERY_STRING'], 'fmp_playlist_editor')) return;
    ?>
<script type="text/javascript">
(function($){//the name space of the playlist editor
//get the size of visible area
var getVisibleSize = function(){
    var de = document.documentElement;
    var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
    var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
    arrayPageSize = [w,h];
    return arrayPageSize;
}

//display messages or tips
var displayMessage = function (message, cls){
    $message_tip = $('<div id="message">').addClass(cls).html('<p>' + message + '</p>');
    $("div.wrap>h2:first").after($message_tip);
    $message_tip.fadeOut(5000, function(){$message_tip.remove()});
}

//When the dom is ready.
$(function(){
    //Fade out the message and tips
    $('#message').fadeOut(5000, function(){$(this).remove()});

    //Make the songs' list sortable
    $("#songs-item ul").sortable();
    $("#songs-item ul").disableSelection();

    //Reset the songs list height
    var resetSongListHeight = function(){
        var visible_size = getVisibleSize();
        if (visible_size[1] - 187 > 450){
            $('#editor-column .inside').height(visible_size[1] - 187);
        }
    }
    resetSongListHeight();
    $(window).resize(resetSongListHeight);
});

//the behavior of the lists column
$(function(){
    //when click the option in the <select>, the page will jump
    var playlist_item_click = function(){
        $("#playlists-manager form").submit();
    }
    $("#playlists-panel>option").click(playlist_item_click);
    $("#playlists-panel").change(playlist_item_click);

    var $text = $("#dialogCreatePlaylist>p>input[type=text]");
    var $check = $("#dialogCreatePlaylist>p>label>input[type=checkbox]");
    var $tip = $("#dialogDeletePlaylist>p>strong");

    //***the behavior on the dialog 'create new playlist'
    $("#dialogCreatePlaylist>input[name=ok]").click(function(){
        var data = {
            action: 'create_new_playlist',
            playlist_name: $text.val(),
            overwrite_same: $check.attr('checked')
        };
        $.post(ajaxurl, data, function(response){
            if (response == -1) { //no write permission
                displayMessage("<?php _e('No permission to write the file.','fmp');?>",'error');
            }else if (response == -2){ //file already exists and cannot overwrite
                displayMessage("<?php _e('File already exists and you don\'t want to overwrite.','fmp');?>",'error');
            }else { //playlist create successfully
                $("<option>").val(response).text(response).click(playlist_item_click)
                    .appendTo($("#playlists-panel"));
                displayMessage("<?php _e('Playlist created sucessfully!','fmp');?>", 'updated');
            }
            tb_remove();
        });
    });
    $("#dialogCreatePlaylist>input[name=cancel]").click(function(){
        tb_remove();
    });
    //end of 'create new playlist'***

    //***the behavior on the dialog 'delete playlist'
    $("#delete-playlist").click(function(){
        $tip.text($("#playlists-panel>option:selected").val());
    });
    $("#dialogDeletePlaylist>input[name=ok]").click(function(){
        var filename = $("#playlists-panel>option:selected").val();
        var data = {
            action: 'delete_selected_playlist',
            playlist_name: filename
        };
        $("#delete-playlist").attr("disabled", true);
        $.post(ajaxurl, data, function(response){
            if (response == -1) { //no write permission
                displayMessage("<?php _e('No permission to write the file.','fmp');?>",'error');
            }else if (response == -2){
                displayMessage("<?php _e('Delete failed. Don\'t know the reason.','fmp');?>",'error');
            }else { //playlist delete successfully
                $("#playlists-panel>option:selected").remove();
                displayMessage(filename + "<?php _e(' deleted.','fmp');?>", 'updated');
                $("#editor-column h3>span:first").html("&nbsp;");
                $("#songs-order ul").html("");
                $("#songs-item ul").html("");
                $("#edit-playlist-actions>input[type=button]").attr("disabled",true);
            }
            tb_remove();
        });
    });
    $("#dialogDeletePlaylist>input[name=cancel]").click(function(){
        tb_remove();
        });
    //end of 'delete playlist'
}); ////end the behavior of the lists column

//the behavior of the editor column
$(function(){
    var $cancel_button = $("#dialogEditSongInfo input[name=cancel]");
    var $songs_list = $("#songs-item ul:first");
    var $songs_order = $('#songs-order ul:first');

    $('#save-current-playlist').click(function(){
        $('#songs-item form').submit();
    });
    
    var clear_edit_panel = function (){
        $('#edit-song-title').val('');
        $('#edit-song-url').val('');
        $('#edit-song-image').val('');
        $('#edit-song-link').val('');
    }

    var link_edit_click = function(){
        var $list_item = $(this).parent().parent();
        var t = 'Edit song\'s info';
        var a = '#TB_inline?inlineId=dialogEditSongInfo';
        tb_show(t,a,false);
        $(this).blur();
        $('#edit-song-title').val($list_item.find('input[name*=annotation]').val());
        $('#edit-song-url').val($list_item.find('input[name*=location]').val());
        $('#edit-song-link').val($list_item.find('input:eq(2)').val());
        $('#edit-song-image').val($list_item.find('input[name*=image]').val());
        var $update_button = $('<input type="button" name="update" value="Update"/>').click(function(){
            var title = $('#edit-song-title').val();
            var url = $('#edit-song-url').val();
            var link = $('#edit-song-link').val();
            var image = $('#edit-song-image').val();
            $list_item.find('img').attr('src',image);
            $list_item.find('.song-title').text(title);
            $list_item.find('input[name*=annotation]').val(title);
            $list_item.find('input[name*=location]').val(url);
            $list_item.find('input:eq(2)').val(link);
            $list_item.find('input[name*=image]').val(image);
            tb_remove();
        });
        $cancel_button.before($update_button); //insert the "update" button
    }
    
    var link_edit_blur = function(){
        jQuery("#TB_window").bind('tb_unload', function(){
            clear_edit_panel();
            $cancel_button.parent().find('input[name!=cancel]').remove();
        });
    }
    var link_delete_click = function(){
        $(this).parent().parent().remove();
        $songs_order.find('li').last().remove();
    }
    $('#songs-item a[name=edit]').click(link_edit_click);
    $('#songs-item a[name=edit]').blur(link_edit_blur);
    $('#songs-item a[name=delete]').click(link_delete_click);
    
    //*** the behavior on the dialog "add new song"
    $("#add-new-song").click(function(){
        var $add_button = $('<input type="button" name="add" value="Add"/>').click(function(){
            var number = $songs_order.find('li').length + 1;
            var index = (new Date()).getTime();
            $('<li>').text(number).appendTo($songs_order);
            var $list_item = $('<li class="ui-state-default">');
            var title = $('#edit-song-title').val();
            var url = $('#edit-song-url').val();
            var link = $('#edit-song-link').val();
            var image = $('#edit-song-image').val();
            $('<img width="32px" height="32px">').attr("src", image).appendTo($list_item);
            $('<span class="song-title">').text(title).appendTo($list_item);
            var $song_item_control = $('<span class="song-item-control">');
            $('<a href="#" name="edit">Edit</a>').click(link_edit_click).blur(link_edit_blur).appendTo($song_item_control);
            $('<a href="#" name="delete">Delete</a>').click(link_delete_click).appendTo($song_item_control);
            $song_item_control.appendTo($list_item);
            $('<br class="clear" />').appendTo($list_item);
            $('<input type="hidden" name="songsinfo[' + index + '][annotation]" value="' + title + '" />')
                    .appendTo($list_item);
            $('<input type="hidden" name="songsinfo[' + index + '][location]" value="' + url + '" />')
                    .appendTo($list_item);
            $('<input type="hidden" name="songsinfo[' + index + '][info]" value="' + link + '" />')
                    .appendTo($list_item);
            $('<input type="hidden" name="songsinfo[' + index + '][image]" value="' + image + '" />')
                    .appendTo($list_item);
            $list_item.appendTo($songs_list);
            link_edit_blur();
            tb_remove();
        });
        $cancel_button.before($add_button); //insert the "add" button
    });
    //end of "add new song"
    $cancel_button.click(function(){
        tb_remove();
    });
});////end the behavior of the editor column

})(jQuery);
</script>
    <?php
}/*the end of function admin_js();*/

function admin_css(){
    global $fmp_jw_url;
    if (!strpos($_SERVER['QUERY_STRING'], 'fmp_playlist_editor')) return;
    ?>
<style type="text/css">
    div#playlists-editor-form{
        padding-top:10px;
        margin-left: 300px;
    }
    #left-side{
        float:left;
        margin-left: -300px;
        width:285px;
    }
    #donate-column{
        text-align:center;
    }
    #editor-column{
        display: block;
        float:right;
        position: relative;
        width:100%;
        min-width:350px;
    }
    #editor-column .inside{
        overflow:auto;
        height:450px;
    }
    #playlists-editor-form h3{
        font-size:12px;
        font-weight:bold;
        line-height:1;
        margin:0;
        padding:7px 9px;
    }
    #playlists-manager .tabs-panel{
        border-style:solid;
        border-width:1px;
        min-height:200px;
        overflow:auto;
        padding:0.5em 0.9em;
    }
    select#playlists-panel{
        width:100%;
        min-height:234px;
    }
    div#songs-list{
        padding-top: 10px;
        clear:both;
        margin-left:40px;
        height: 440px;
    }
    div#songs-order{
        width:35px;
        clear:left;
        float:left;
        margin-left: -35px;
    }
    div#songs-item ul{
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    div#songs-item ul li {
        margin: 0 3px 3px 0;
        padding: 0.2em;
        font-size: 1.4em;
        height: 32px;
    }
    div#songs-order ul{
        margin:0;
        padding:0;
    }
    div#songs-order ul li{
        margin: 0 3px 3px 0;
        border:1px solid silver;
        padding: 0.2em;
        font-size: 1.4em;
        line-height: 32px;
        height:32px;
        text-align:center;
    }
    .ui-state-default{
        border: 1px solid #d3d3d3;
        background-attachment: scroll;
        background-repeat: repeat-x;
        background-image: url("<?php echo $fmp_jw_url;?>/inc/images/item-bg.png");
        background-position: 50% 50%;
        background-color: #e6e6e6;
        font-weight: 400;
        color: #555555;
        outline-width: medium;
        outline-style: none;
    }
    .ui-state-default img{
        float:left;
        margin-right:5px;
    }
    div#songs-item .song-title{
        font-size: 0.8em;
        position: relative;
        line-height: 32px;
        height: 32px;
        float:left;
        max-width:325px;
        overflow:hidden;
    }
    div#songs-item .song-item-control{
        line-height: 32px;
        float:right;
        position: relative;
    }
    .song-item-control a{
        font-size: 12px;
        margin-right: 5px;
    }
</style>
<!--[if lte IE 7]>
<style type="text/css">
* html #left-side{
    margin-left:-150px;
}
* html div#songs-list{
    margin-left:0;
}
* html div#songs-order{
    margin-left:0px;
}
* html select#playlists-panel{
    height:234px;
}
* html div#songs-order ul li{
    margin: 0 3px 6px 0;
}
</style>
<![endif]-->
    <?php
}/*the end of function admin_css();*/
}/*The end bracket of the class FMP_Playlist_Editor*/
?>