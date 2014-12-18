<?php
/**
 * Statigram Wordpress Plugin
 *
 * @category Wordpress
 * @package  Statigram_Wordpress
 * @author   rydgel <jerome.mahuet@gmail.com>
 * @author   gaetan <gaetan@statigr.am>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version  1.0
 * @link     http://statigr.am
 **/

// Posting widget form
if (isset($_POST['settingPlugin'])) {
    $updateSuccess = db::dbUpdateMultiFields($_POST);
}

$pluginValues = Db::getPluginValues();
?>

<div class='wrap' id="wrap-statigram">
    <div id="icon-themes" class="icon32"><br></div>
    <h2>Generate your Instagram widget</h2>
    <br>
    <?php
    if ($updateSuccess) {
    ?>
        <div id="message" class="updated below-h2">
            <p>Your plugin configuration was saved.</p>
        </div>
    <?php
    }
    ?>
    <div class="widget-liquid-left">
        <div id="widgets-left">
            <div class="widgets-holder-wrap">
                <div class="sidebar-name">
                    <h3>Settings</h3>
                </div>
                <div class="widget-holder">
                    <div class="content-column">
                        <form method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                            <input type="hidden" id="loader" value="<?php echo StatigramWidget::getLoader(); ?>">
                            <input type="hidden" name="settingPlugin" value='1'>
                            <input type="hidden" id="notrack" value='1'>
                            <table class="form-table">
                                <tbody>
                                    <tr valign="top">
                                        <th scope="row"><label for="choose-content">Content</label></th>
                                        <td>
                                        <select name="choice" id="choose-content">
                                            <option <?php if($pluginValues->choice == 'myfeed') echo 'selected="selected"'; ?> value="myfeed">My Feed</option>
                                            <option <?php if($pluginValues->choice == 'hashtag') echo 'selected="selected"'; ?> value="hashtag">Hashtag</option>
                                        </select>
                                        </td>
                                    </tr>

                                    <tr valign="top" class="user-group">
                                        <th scope="row"><label for="username">Username</label></th>
                                        <td><input name="username" type="text" id="username" value="<?php echo $pluginValues->username; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top" class="user-group">
                                        <th scope="row">Informations</th>
                                        <td> <fieldset><legend class="screen-reader-text"><span>Show infos</span></legend><label for="infos">
                                        <input name="show_infos" type="checkbox" id="infos" <?php if($pluginValues->show_infos) echo 'checked="checked"'; ?>>
                                        Show infos</label>
                                        </fieldset></td>
                                    </tr>

                                    <tr valign="top" class="hash-group">
                                        <th scope="row"><label for="hashtag">Hashtag</label></th>
                                        <td><input name="hashtag" type="text" id="hashtag" value="<?php echo $pluginValues->hashtag; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><label for="linking">Linking to</label></th>
                                        <td>
                                        <select name="linking" id="linking">
                                            <option <?php if($pluginValues->linking == 'statigram') echo 'selected="selected"'; ?> value="statigram">Statigram</option>
                                            <option <?php if($pluginValues->linking == 'instagram') echo 'selected="selected"'; ?> value="instagram">Instagram</option>
                                        </select>
                                        &nbsp; Choose where the link redirect
                                        </td>
                                    </tr>

                                    <tr valign="top"><td><hr/></td></tr>

                                    <tr valign="top">
                                        <th scope="row"><label for="width">Width (in pixels)</label></th>
                                        <td><input name="width" type="text" id="width" value="<?php echo $pluginValues->width; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><label for="height">Height (in pixels)</label></th>
                                        <td><input name="height" type="text" id="height" value="<?php echo $pluginValues->height; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><label for="choose-mode">Mode</label></th>
                                        <td>
                                        <select name="choose-mode" id="choose-mode">
                                            <option <?php if($pluginValues->mode == 'grid') echo 'selected="selected"'; ?> value="grid">Grid</option>
                                            <option <?php if($pluginValues->mode == 'slideshow') echo 'selected="selected"'; ?> value="slideshow">Slideshow</option>
                                        </select>
                                        </td>
                                    </tr>

                                    <tr valign="top" class="mode-grid">
                                        <th scope="row"><label>Layout</label></th>
                                        <td>
                                            <select name="layout_x" id="layoutX">
                                                <option <?php if($pluginValues->layout_x == '1') echo 'selected="selected"'; ?> value="1">1</option>
                                                <option <?php if($pluginValues->layout_x == '2') echo 'selected="selected"'; ?> value="2">2</option>
                                                <option <?php if($pluginValues->layout_x == '3') echo 'selected="selected"'; ?> value="3">3</option>
                                                <option <?php if($pluginValues->layout_x == '4') echo 'selected="selected"'; ?> value="4">4</option>
                                                <option <?php if($pluginValues->layout_x == '5') echo 'selected="selected"'; ?> value="5">5</option>
                                                <option <?php if($pluginValues->layout_x == '6') echo 'selected="selected"'; ?> value="6">6</option>
                                                <option <?php if($pluginValues->layout_x == '7') echo 'selected="selected"'; ?> value="7">7</option>
                                            </select>
                                            <span class="sep">X</span>
                                            <select name="layout_y" id="layoutY">
                                                <option <?php if($pluginValues->layout_y == '1') echo 'selected="selected"'; ?> value="1">1</option>
                                                <option <?php if($pluginValues->layout_y == '2') echo 'selected="selected"'; ?> value="2" selected="">2</option>
                                                <option <?php if($pluginValues->layout_y == '3') echo 'selected="selected"'; ?> value="3">3</option>
                                                <option <?php if($pluginValues->layout_y == '4') echo 'selected="selected"'; ?> value="4">4</option>
                                                <option <?php if($pluginValues->layout_y == '5') echo 'selected="selected"'; ?> value="5">5</option>
                                                <option <?php if($pluginValues->layout_y == '6') echo 'selected="selected"'; ?> value="6">6</option>
                                                <option <?php if($pluginValues->layout_y == '7') echo 'selected="selected"'; ?> value="7">7</option>
                                                <option <?php if($pluginValues->layout_y == '8') echo 'selected="selected"'; ?> value="8">8</option>
                                            </select> &nbsp;
                                            <span class="help-inline">Horizontal x Vertical</span>
                                        </td>
                                    </tr>

                                    <tr valign="top" class="mode-grid">
                                        <th scope="row"><label for="padding">Padding (in pixels)</label></th>
                                        <td><input name="padding" type="text" id="padding" value="<?php echo $pluginValues->padding; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top" class="mode-slideshow">
                                        <th scope="row"><label for="pace">Transition between two pictures</label></th>
                                        <td><input name="pace" type="text" id="pace" value="<?php echo $pluginValues->pace; ?>" class="regular-text"></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row">Photo border</th>
                                        <td> <fieldset><legend class="screen-reader-text"><span>Border white with shadow</span></legend><label for="photo-border">
                                        <input name="photo_border" type="checkbox" id="photo-border" <?php if($pluginValues->photo_border) echo 'checked="checked"'; ?>>
                                        Border white with shadow</label>
                                        </fieldset></td>
                                    </tr>

                                    <tr valign="top"><td><hr/></td></tr>

                                    <tr valign="top" class="background">
                                        <th scope="row"><label for="background">Background color</label></th>
                                        <td><input name="background" type="text" id="background" value="<?php echo $pluginValues->background; ?>" class="regular-text color {required: false}"><br> Leave empty for transparent</td>
                                    </tr>

                                    <tr valign="top" class="text">
                                        <th scope="row"><label for="text">Text color</label></th>
                                        <td><input name="text" type="text" id="text" value="<?php echo $pluginValues->text; ?>" class="regular-text color {required: false}"></td>
                                    </tr>

                                    <tr valign="top" class="text">
                                        <th scope="row"><label for="text">Widget border</label></th>
                                        <td>
                                            <input name="widget_border" type="checkbox" id="widget-border" <?php if($pluginValues->widget_border) echo 'checked="checked"'; ?>>
                                            <input name="radius" type="radius" id="radius" value="<?php echo $pluginValues->radius; ?>" class="small-text">px (radius)
                                            <input name="border-color" type="text" id="border-color" value="<?php echo $pluginValues->borderColor; ?>" class="regular-text color {required: false}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
                        </form>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
    </div>

    <div class="widget-liquid-right">
        <div id="widgets-right">
            <div class="widgets-holder-wrap">
                <div class="sidebar-name">
                    <h3>Preview</h3>
                </div>
                <div class="widgets-sortables ui-sortable">
                    <div class="content-column">
                        <div id="content-iframe"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
