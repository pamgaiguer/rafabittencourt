<?php
/**
 * Template and actions for options page.
 */
if (!defined('DB_NAME')) {
  header('HTTP/1.0 403 Forbidden');
  die;
}

//For nonce.
global $current_user;
//Setup plugin options.
$options = get_option('facebookalbum', FB_Album::default_options());

$options['cache'] = (isset($options['cache'])) ? $options['cache'] : 1;
$options['pages'] = (isset($options['pages'])) ? $options['pages'] : array();

//If nonce, do update
if (isset($_POST["fbalbum_nonce"])) :
  $options = $_POST['facebookalbum'];
  update_option('facebookalbum', $options);
  echo '<div class="updated"><p><strong>Facebook Albums has been updated</strong></p></div>';
endif;

if(isset($_GET['reset_application']) && wp_verify_nonce($_GET['reset_application'], $current_user->data->user_email)) :
  unset($options['app_id']);
  unset($options['app_secret']);
  unset($options['access_token']);
  update_option('facebookalbum', $options);
  echo '<div class="updated"><p><strong>Facebook API application has been removed.</strong></p></div>';
endif;

if(isset($_GET['clear_cache']) && wp_verify_nonce($_GET['clear_cache'], $current_user->data->user_email)) {
  global $wpdb;
  $transient_caches_query = sprintf('DELETE FROM %1$s WHERE option_name LIKE "_transient_fbalbum_%%" ', $wpdb->options);
  $transient_caches_timeout_query = sprintf('DELETE FROM %1$s WHERE  `option_name` LIKE  "_transient_timeout_fbalbum_%%" ', $wpdb->options);

  $transient_caches_result = $wpdb->query($transient_caches_query);
  $transient_caches_timeout_result = $wpdb->query($transient_caches_timeout_query);
  echo '<div class="updated"><p><strong>Album transients have been cleared. (' . $transient_caches_result .' caches removed.).</strong></p></div>';
}

 ?>
      <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Facebook Album <?php _e('Options', 'facebook-albums'); ?></h2>
        <div id="facebook-album-content" class="postbox-container" style="">
        <form method="post" action="" name="options-facebookalbum">
        <?php   wp_nonce_field(basename(__FILE__), 'fbalbum_nonce'); ?>
        <div class="settings-box">
          <h2><?php _e('General Settings', 'facebook-albums'); ?></h2>
          <div class="checkbox-group">
            <p><label><input type="checkbox" value="on" name="facebookalbum[order]" id="fbalbum_order" <?php checked($options['order'], 'on'); ?>/> <?php _e('Reverse display order.', 'facebook-albums'); ?></label>
              <span class="note"><?php _e( 'Note: Facebook does not support a "order" attribute for Graph API calls. This simply reverse the order of the photos received from Facebook.','facebook-albums'); ?></span>
            </p>
            <p><label><input type="checkbox" value="on" name="facebookalbum[title]" id="fbalbum_title" <?php checked($options['title'], 'on'); ?>/> <?php _e('Display album title.', 'facebook-albums'); ?></label></p>
            <p><label><input type="checkbox" value="on" name="facebookalbum[photo_title]" id="photo_title" <?php checked($options['photo_title'], 'on'); ?>/><?php _e('Display photo description in Lightbox.', 'facebook-albums'); ?></label></p>
          </div>
          <p><label for="fbalbum_size"><?php _e('Thumbnail Size', 'facebook-albums'); ?></label>:
            <select name="facebookalbum[size]" id="fbalbum_size" value="" class="regular-text">
              <option value="8" <?php selected($options['size'], 8) ?>><?php _e('Smaller', 'facebook-albums'); ?></option>
              <option value="6" <?php selected($options['size'], 6) ?>><?php _e('Small', 'facebook-albums'); ?></option>
              <option value="5" <?php selected($options['size'], 5) ?>><?php _e('Medium', 'facebook-albums'); ?></option>
              <option value="4" <?php selected($options['size'], 4) ?>><?php _e('Large', 'facebook-albums'); ?></option>
              <option value="3" <?php selected($options['size'], 3) ?>><?php _e('Largest', 'facebook-albums'); ?></option>
              <option value="2" <?php selected($options['size'], 2) ?>><?php _e('Full', 'facebook-albums'); ?></option>
            </select>
          </p>
          <p><label for="fbalbum_cache"><?php _e('Cache Lifetime', 'facebook-albums'); ?></label>:
            <select name="facebookalbum[cache]" id="fbalbum_cache" value="" class="regular-text">
              <option value="1" <?php selected($options['cache'], 1) ?>><?php _e('1 hour', 'facebook-albums'); ?></option>
              <option value="3" <?php selected($options['cache'], 3) ?>><?php _e('3 hours', 'facebook-albums'); ?></option>
              <option value="6" <?php selected($options['cache'], 6) ?>><?php _e('6 hours', 'facebook-albums'); ?></option>
              <option value="12" <?php selected($options['cache'], 12) ?>><?php _e('12 hours', 'facebook-albums'); ?></option>
              <option value="24" <?php selected($options['cache'], 24) ?>><?php _e('1 day', 'facebook-albums'); ?></option>
              <option value="48" <?php selected($options['cache'], 48) ?>><?php _e('2 days', 'facebook-albums'); ?></option>
            </select>
            <span class="clear-cache">
              <?php printf(__('<a href="?page=facebook-album&amp;clear_cache=%s">Clear all caches</a>'), wp_create_nonce($current_user->data->user_email)); ?>
            </span>
          </p>
          <div class="app-settings">
            <?php if(!FB_Album::verify_app_information()) : ?>
            <p><?php _e('If you wish to use <strong>personal albums</strong> you will need to create a Facebook App and enter in the App ID and App Secret below.', 'facebook-albums'); ?></p>
            <p><label><?php _e('App ID', 'facebook-albums'); ?></label><input type="text" name="facebookalbum[app_id]" value="<?php echo $options['app_id']; ?>" /></p>
            <p><label><?php _e('App Secret', 'facebook-albums'); ?></label><input type="text" name="facebookalbum[app_secret]" value="<?php echo $options['app_secret']; ?>" /></p>
            <?php else:
            FB_Album::set_facebook_sdk();
            // Get User ID
            $user = FB_Album::$facebook_sdk->getUser();
            if ($user) {
              try {
                $old_access_token = $options['access_token'];
                // Proceed knowing you have a logged in user who's authenticated.
              $user_profile = FB_Album::$facebook_sdk->api('/me');
              FB_Album::$facebook_sdk->setExtendedAccessToken();
              $options['access_token'] = FB_Album::$facebook_sdk->getAccessToken();
              update_option('facebookalbum', $options);

              } catch (FacebookApiException $e) {
                echo '<div class="error"><p><strong>OAuth Error</strong>Error added to error_log: '.$e.'</p></div>';
                error_log($e);
                $user = null;
              }
            }

            // Login or logout url will be needed depending on current user state.
            $app_link_text = $app_link_url = null;
            if ($user) {
              $app_link_url = FB_Album::$facebook_sdk->getLogoutUrl(array('next' => admin_url()));
              $app_link_text = __("Logout of your app", 'facebook-albums');
            } else {
              $app_link_url = FB_Album::$facebook_sdk->getLoginUrl(array('scope' => 'user_photos'));
                $app_link_text = __('Log into Facebook with your app', 'facebook-albums');
            } ?>
              <input type="hidden" name="facebookalbum[app_id]" value="<?php echo $options['app_id']; ?>" />
              <input type="hidden" name="facebookalbum[app_secret]" value="<?php echo $options['app_secret']; ?>" />
              <input type="hidden" name="facebookalbum[access_token]" value="<?php echo $options['access_token']; ?>" />
              <?php if($user) : ?>
                <div style="float: right; margin: 10px;"><span style="margin: 0 10px;"><?php echo $user_profile['name']; ?></span><img src="https://graph.facebook.com/<?= $user_profile['id'] ?>/picture?type=square" style="vertical-align: middle"/></div>
              <?php endif; ?>
              <ul>
                <li><a href="https://developers.facebook.com/apps/<?php echo $options['app_id']; ?>" target="_blank"><?php _e("View your application's settings.", 'facebook-albums'); ?></a></li>
                <li><a href="<?php echo $app_link_url; ?>"><?php echo $app_link_text; ?></a></li>
              </ul>
              <div style="clear: both;">&nbsp;</div>
              <p><?php printf(__('Having issues once logged in? Try <a href="?page=facebook-album&amp;reset_application=%s">resetting application data.</a> <em>warning: removes App ID and App Secret</em>'), wp_create_nonce($current_user->data->user_email)); ?></p>
              <p><strong>Notice!</strong> Your extended access token will only last about 2 months. So visit this page every month or so to keep the access token fresh.</p>
            <?php endif; ?>
          </div>
        </div>
        <div class="settings-box">
          <h2>Colorbox <?php _e('Settings', 'facebook-albums'); ?></h2>
          <p><label><input type="checkbox" value="true" name="facebookalbum[colorbox][enabled]" id="fbalbum_order" <?php checked($options['colorbox']['enabled'], 'true'); ?>/> <?php _e('Enable', 'facebook-albums'); ?> Colorbox</label></p>
          <div class="colorbox-options" style="<?php if($options['colorbox']['enabled'] != 'true') echo 'display:none'; ?>">
            <p><label><?php _e('Overlay opacity', 'facebook-albums'); ?><input type="number" min="0.00" max="1.00" name="facebookalbum[colorbox][opacity]" step="0.01" value="<?php echo $options['colorbox']['opacity'] ?>"></label></p>
            <p><label for="transition"><?php _e('Transition Type', 'facebook-albums'); ?></label>:
              <select name="facebookalbum[colorbox][transition]" id="trainsition" value="" class="regular-text">
                <option value="none" <?php selected($options['colorbox']['transition'], 'none') ?>><?php _e('None', 'facebook-albums'); ?></option>
                <option value="elastic" <?php selected($options['colorbox']['transition'], 'elastic') ?>><?php _e('Elastic', 'facebook-albums'); ?></option>
                <option value="fade" <?php selected($options['colorbox']['transition'], 'fade') ?>><?php _e('Fade', 'facebook-albums'); ?></option>
              </select>
            </p>
          </div>
        </div>
        <div class="settings-box">
          <h2><?php _e('Widget Options', 'facebook-albums'); ?></h2>
          <p><?php _e('You have the option to specify a different Facebook Album URL for each page the widget is on.', 'facebook-albums'); ?></p>
          <div style="height: 300px; overflow: auto; padding-left: 10px; border-left: 2px solid #e6e6e6;">
            <?php foreach( get_pages() as $page):
              $existing_page_value = (isset($options['pages'][$page->ID])) ? $options['pages'][$page->ID] : '';
            ?>
            <p><label for="wp_page_<?php echo $page-> ID; ?>"><?php echo $page -> post_title; ?></label><br/><input type="text" size="60" name="facebookalbum[pages][<?php echo $page->ID; ?>]" id="wp_page_<?php echo $page->ID; ?>" value="<?php echo $existing_page_value; ?>"></p>
            <?php endforeach; ?>
            </div>
        </div>
            <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
            </p>
        </form>
        </div>
        <div id="facebook-album-meta" class="postbox-container" style="width: 261px;">
          <div class="author">
            <div class="by">by</div>
            <div class="name">matt glaman</div>
            <div class="website">
              <a href="http://glamanate.com/?utm_source=fbalbum_options&amp;utm_medium=website&amp;utm_campaign=fbalbum" target="_blank">glamanate.com</a>
            </div>
          </div>
          <div class="donate">
            <p>Please support this plugin's development so more features and capabilities can be added!</p>
            <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=CTT2K9UJZJ55S" class="btn" target="_blank">Donate Now</a>
          </div>
          <div class="video">
            <iframe width="420" height="315" src="http://www.youtube.com/embed/ZiNJPg6sv1I?rel=0" frameborder="0" allowfullscreen></iframe>
            <caption>How to use shortcode in Pages or Posts</caption>
            <div style="height:25px;">&nbsp;</div>
            <iframe width="420" height="315" src="http://www.youtube.com/embed/3p_OZ7qbxk4?rel=0" frameborder="0" allowfullscreen></iframe>
            <caption>How to setup a Facebook app</caption>
          </div>
          <div class="news">
            <h3>Plugin News</h3>
          <?php
              $news_widget = array(
                'link' => 'http://glamanate.com',
                'url' => 'http://glamanate.com/category/facebook-album-news/feed/',
                'title' => 'Plugin News',
                'items' => 3,
                'show_summary' => 0,
                'show_author' => 0,
                'show_date' => 0
              );
            echo '<div class="rss-widget">';
            wp_widget_rss_output( $news_widget );
            echo "</div>";
            ?>
          </div>
        </div>
      </div>
