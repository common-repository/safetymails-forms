<?php
# Loads the database functionalities
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Fired during plugin deactivation
 *
 * @link       http://www.henriquerodrigues.me
 * @since      1.0.0
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/includes
 * @author     Henrique Rodrigues <henoliv@gmail.com>
 */
class Safetymail_Form_Deactivator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "safety_forms";
        $wpdb->query("TRUNCATE TABLE $table_name;");
        $wpdb->query("DROP TABLE $table_name;");
    }
}
