<?php
# Loads the database functionalities
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

/**
 * Fired during plugin activation
 *
 * @link       http://www.henriquerodrigues.me
 * @since      1.0.0
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/includes
 * @author     Henrique Rodrigues <henoliv@gmail.com>
 */
class Safetymail_Form_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;

        $table_name = $wpdb->prefix . "safety_forms";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email_recipient varchar(255) NOT NULL,
            email_replyto varchar(255) NOT NULL,
            subject varchar(255) NOT NULL,
            html tinyint(1) NOT NULL,
            element text NOT    NULL,
            code tinytext DEFAULT '' NOT NULL,
            action enum('NOTHING','MESSAGE','REDIRECT') NOT NULL,
            action_content text,
            show_status tinyint(1) NOT NULL,
            protected tinyint(1) NOT NULL,
            api_key varchar(255) NOT NULL,
            api_ticket varchar(255) NOT NULL,
            invalid_callback varchar(255),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . "safety_forms_config";

        $sql = "CREATE TABLE $table_name (
            host varchar(255) NOT NULL,
            port varchar(4) NOT NULL,
            email_sender varchar(255) NOT NULL,
            sender_name varchar(255) NOT NULL,
            require_auth tinyint(1) NOT NULL,
            user varchar(255),
            pass varchar(255)
        ) $charset_collate;";

        dbDelta( $sql );
    }
}
