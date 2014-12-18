<?php
/**
 * Statigram Wordpress Plugin
 *
 * @category Wordpress
 * @package  Statigram_Wordpress
 * @author   rydgel <gcc@statigr.am>
 * @author   gaetan <gaetan@statigr.am>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version  1.0
 * @link     http://statigr.am

Plugin Name: Instagram image gallery
Plugin URI: http://statigr.am
Description: Showcase your recent Instagram photos or a Hashtag feed: grid/slideshow with a wide range of custom options. Powered by Statigram.
Version: 1.0.3
Author: Statigram
Author URI: http://statigr.am
Author Email: contact@statigr.am
Text Domain: statigram-locale
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2012 Statigram (contact@statigr.am)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'db.class.php';

/**
 * Statigram Widget Main Class
 *
 * @category Wordpress
 * @package  Statigram_Wordpress
 * @author   rydgel <gcc@statigr.am>
 * @author   gaetan <gaetan@statigr.am>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version  1.0
 * @link     http://statigr.am
 */
class StatigramWidget extends WP_Widget
{
    // Set this to true to get the state of origin, so you don't need to always
    // uninstall during development.
    const STATE_OF_ORIGIN = false;

    /**
     * The widget constructor. Specifies the classname and description,
     * instantiates the widget, loads localization files, and includes
     * necessary scripts and styles.
     */
    public function __construct()
    {
        parent::__construct(
            'statigram-id',
            __('Statigram Widget', 'statigram-locale'),
            array(
                'classname'     =>  'statigram-widget',
                'description'   =>  __(
                    'This advanced widget lets you
                    beautifully showcase Instagram photos on your blog
                    or website.', 'statigram-locale'
                )
           )
        );

        add_action('admin_init', array($this, 'redirect'));

        // load plugin text domain
        add_action('init', array($this, 'textdomain'));

        // Register admin styles and scripts
        add_action('admin_print_styles', array($this, 'registerAdminStyles'));
        add_action('admin_enqueue_scripts', array($this, 'registerAdminScripts'));

        // Register the admin page
        add_action('admin_menu', array($this, 'attAddOptions'));

        // Add shortcode
        add_shortcode('statigram_widget', array(new Db(), 'renderIframe'));
    }


    /**
     * Make our function to call the WordPress function to add to
     * the correct menu.
     *
     * @return null
     */
    public function attAddOptions()
    {
        add_theme_page(
            'Statigram Widget Options', 'Statigram Widget', 8,
            'statigram', array($this, 'attOptionsPage')
        );
    }


    /**
     * Content of the Admin Page
     *
     * @return null
     */
    public function attOptionsPage()
    {
        include plugin_dir_path(__FILE__) . '/views/admin-page.php';
    }


    /**
     * Fired when the plugin is activated.
     *
     * @return null
     */
    public function activate()
    {
        // Redirect the user to the widget dashboard after activation
        add_option('statigram_do_activation_redirect', true);
        Db::dbInstall();
    }


    /**
     * Redirect the user when the plugin is activated
     *
     * @return null
     */
    public function redirect()
    {
        if (get_option('statigram_do_activation_redirect', false)) {
            delete_option('statigram_do_activation_redirect');
            wp_redirect(admin_url('themes.php?page=statigram'));
            exit();
        }
    }



    /**
     * Fired when the plugin is uninstalled
     *
     * @return null
     */
    public function uninstall()
    {
        Db::dbRemove();
    }


    /**
     * Load the plugin text domain on "init"
     *
     * @return null
     */
    public function textdomain()
    {
        load_plugin_textdomain('statigram-locale', false, plugin_dir_path(__FILE__) . '/lang/');
    }


    /**
     * Registers and enqueues admin-specific styles.
     *
     * @return null
     */
    public function registerAdminStyles()
    {
        wp_enqueue_style('statigram-admin-styles', plugins_url('css/admin.css', __FILE__));
    }


    /**
     * Registers and enqueues admin-specific JavaScript.
     *
     * @return null
     */
    public function registerAdminScripts()
    {
        wp_enqueue_script('statigram-admin-script-color', plugins_url('js/jscolor.js', __FILE__));
        wp_enqueue_script('statigram-admin-script', plugins_url('js/admin.js', __FILE__));
    }

    /**
     * Outputs the content of the widget.
     *
     * @param array $args arguments
     *
     * @return string widget html content
     */
    public function widget($args)
    {
        extract($args, EXTR_SKIP);

        echo $args['before_widget'];

        include plugin_dir_path(__FILE__) . '/views/widget.php';

        echo $args['after_widget'];
    }

    /**
     * Get loader image
     *
     * @return string load path
     */
    public function getLoader()
    {
        return plugins_url('images/loader.gif', __FILE__);
    }
}

// Manage plugin ativation/deactivation hooks
register_activation_hook(__FILE__, array("StatigramWidget", 'activate'));
register_uninstall_hook(__FILE__, array("StatigramWidget", 'uninstall'));

add_action('widgets_init', create_function('', 'register_widget("StatigramWidget");'));

