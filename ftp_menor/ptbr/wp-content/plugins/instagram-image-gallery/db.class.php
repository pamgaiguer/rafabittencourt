<?php
/**
 * Statigram Wordpress Database File
 *
 * @category Wordpress
 * @package  Statigram_Wordpress
 * @author   rydgel <gcc@statigr.am>
 * @author   gaetan <gaetan@statigr.am>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version  1.0
 * @link     http://statigr.am

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
 **/

/**
 * Statigram Widget Database Class
 *
 * @category Wordpress
 * @package  Statigram_Wordpress
 * @author   rydgel <gcc@statigr.am>
 * @author   gaetan <gaetan@statigr.am>
 * @license  GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 * @version  1.0
 * @link     http://statigr.am
 **/
class Db
{
    /**
     * Get the Statigram Widget Database Table Name
     *
     * @return string table name
     */
    public static function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . "statigram_widget";
    }


    /**
     * Create database table for widget
     *
     * call register_activation_hook(__FILE__,'dbInstall'); in activation plugin file
     *
     * @return null
     */
    public static function dbInstall()
    {
        $table_name = self::getTableName();

        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                `choice` enum('myfeed','hashtag') NOT NULL DEFAULT 'myfeed',
                `username` varchar(255) NOT NULL,
                `hashtag` varchar(255) NOT NULL DEFAULT 'statigram',
                `linking` enum('statigram','instagram') NOT NULL DEFAULT 'statigram',
                `show_infos` enum('true','false') NOT NULL DEFAULT 'true',
                `width` int(6) NOT NULL DEFAULT '380',
                `height` int(6) NOT NULL DEFAULT '420',
                `mode` enum('grid','slideshow') NOT NULL DEFAULT 'grid',
                `pace` int(6) NOT NULL DEFAULT '10',
                `layout_x` int(1) NOT NULL DEFAULT '3',
                `layout_y` int(1) NOT NULL DEFAULT '2',
                `padding` int(6) NOT NULL DEFAULT '10',
                `photo_border` enum('true','false') NOT NULL DEFAULT 'true',
                `background` varchar(6) NOT NULL DEFAULT 'FFFFFF',
                `text` varchar(6) NOT NULL DEFAULT '777777',
                `widget_border` enum('true','false') NOT NULL DEFAULT 'true',
                `radius` int(11) NOT NULL DEFAULT '5',
                `borderColor` varchar(6) NOT NULL DEFAULT 'DDDDDD',
                PRIMARY KEY (`choice`)
                );";

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        self::dbInsert('username', 'statigram');
    }

    /**
     * Remove database table for widget
     *
     * call register_deactivation_hook(__FILE__,'dbRemove'); in activation plugin file
     *
     * @return null
     */
    public static function dbRemove()
    {
        global $wpdb;

        $table_name = self::getTableName();

        $sql = "DROP TABLE $table_name;";
        $wpdb->query($sql);
    }

    /**
     * Single update in database
     *
     * call register_activation_hook(__FILE__,'dbInsert'); in activation plugin file
     *
     * @param type $field field
     * @param type $value value
     *
     * @return null
     */
    public static function dbUpdateOneField($field, $value)
    {
        if (isset($field) && isset($value)) {
            global $wpdb;

            $table_name = self::getTableName();

            $querystr = "UPDATE $table_name SET `$field` =  '$value' ;";
            $wpdb->query($querystr);
        }
    }

    /**
     * Multiple update on the database
     *
     * @param array $postDatas Form data
     *
     * @return null
     */
    public static function dbUpdateMultiFields($postDatas)
    {
        if (isset($postDatas)) {
            self::dbUpdateOneField('choice', $postDatas['choice']);
            self::dbUpdateOneField('username', $postDatas['username']);
            if ($postDatas['show_infos'] == 'on') {
                self::dbUpdateOneField('show_infos', true);
            } else {
                self::dbUpdateOneField('show_infos', false);
            }
            self::dbUpdateOneField('hashtag', $postDatas['hashtag']);
            self::dbUpdateOneField('linking', $postDatas['linking']);
            self::dbUpdateOneField('width', $postDatas['width']);
            self::dbUpdateOneField('height', $postDatas['height']);
            self::dbUpdateOneField('mode', $postDatas['choose-mode']);
            self::dbUpdateOneField('layout_x', $postDatas['layout_x']);
            self::dbUpdateOneField('layout_y', $postDatas['layout_y']);
            self::dbUpdateOneField('padding', $postDatas['padding']);
            self::dbUpdateOneField('pace', $postDatas['pace']);
            if ($postDatas['photo_border'] == 'on') {
                self::dbUpdateOneField('photo_border', true);
            } else {
                self::dbUpdateOneField('photo_border', false);
            }
            self::dbUpdateOneField('background', $postDatas['background']);
            self::dbUpdateOneField('text', $postDatas['text']);
            if ($postDatas['widget_border'] == 'on') {
                self::dbUpdateOneField('widget_border', true);
            } else {
                self::dbUpdateOneField('widget_border', false);
            }
            self::dbUpdateOneField('radius', $postDatas['radius']);
            self::dbUpdateOneField('borderColor', $postDatas['border-color']);

            return true;
        } else {
            return false;
        }
    }


    /**
     * Mise à jour du champ $field avec la valeur $value
     *
     * @param type $field field
     * @param type $value value
     *
     * @global type $wpdb
     *
     * @return null
     */
    public static function dbInsert($field, $value)
    {
        if (isset($field) && isset($value)) {
            global $wpdb;

            $table_name = self::getTableName();

            $wpdb->insert($table_name, array("$field" => $value));
        }
    }


    /**
     * Get the data stored in database
     *
     * @return array widget datas
     */
    public static function getPluginValues()
    {
        global $wpdb;

        $table_name = self::getTableName();

        $querystr = "SELECT * FROM $table_name";

        $pluginValues = $wpdb->get_row($querystr, OBJECT);

        return $pluginValues;
    }

    /**
     * Parse values of database to build the iframe code
     *
     * @param array $pluginValues getPluginValues() data
     *
     * @return string iframe
     */
    public static function parseValues($pluginValues)
    {
        $values = array();
        foreach ($pluginValues as $key => $value) {

            if ($key === 'borderColor') {
                $values['border-color'] = $value;
                continue;
            }

            $values[$key] = $value;
        }

        if (!$values['username']) {
            unset($values['username']);
        }

        if (!$values['hashtag']) {
            unset($values['hashtag']);
        }

        $url = "http://statigr.am/widget.php?" . http_build_query($values);
        return '<iframe src="'.$url.'" allowTransparency="true" frameborder="0" scrolling="no" style="border:none; overflow:hidden; width:'.$values['width'].'px; height:'.$values['height'].'px;"></iframe>';
    }

    /**
     * Render iframe
     *
     * @return string
     */
    public static function renderIframe()
    {
        $pluginValues = self::getPluginValues();
        $value = Db::parseValues($pluginValues);

        return $value;
    }
}
