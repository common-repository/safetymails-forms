<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.henriquerodrigues.me
 * @since             1.0.0
 * @package           Safetymail_Form
 *
 * @wordpress-plugin
 * Plugin Name:       SafetyMails Forms
 * Plugin URI:        https://www.safetymails.com/
 * Description:       Não deixe seu e-mail marketing naufragar por causa de e-mails ruins. Crie formulários usando nossa API de validação em tempo real e evite que e-mails ruins sejam inseridos em suas listas.
 * Version:           1.0.0
 * Author:            Henrique Rodrigues
 * Author URI:        http://www.henriquerodrigues.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       safetymail-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SFPLUGIN_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-safetymail-form-activator.php
 */
function activate_safetymail_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safetymail-form-activator.php';
	Safetymail_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-safetymail-form-deactivator.php
 */
function deactivate_safetymail_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-safetymail-form-deactivator.php';
	Safetymail_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_safetymail_form' );
register_deactivation_hook( __FILE__, 'deactivate_safetymail_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-safetymail-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_safetymail_form() {

	$plugin = new Safetymail_Form();
	$plugin->run();

}
run_safetymail_form();
