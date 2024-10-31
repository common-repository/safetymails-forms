<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.henriquerodrigues.me
 * @since      1.0.0
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/admin
 * @author     Henrique Rodrigues <henoliv@gmail.com>
 */
class Safetymail_Form_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * List of custom forms.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $forms_list;

    const METHOD = 'aes-256-ctr';
    const KEY = '46i4UjFdTq19d7XBrCzK9d';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Safetymail_Form_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Safetymail_Form_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/safetymail-form-admin.css', array(), time(), 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Safetymail_Form_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Safetymail_Form_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-3.3.1.min.js', array( ), time(), false );
        wp_enqueue_script( 'form-builder', plugin_dir_url( __FILE__ ) . 'js/form-builder.min.js', array( 'jquery' ), time(), false );
        wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), time(), false );
        wp_enqueue_script( 'fontawesome', 'https://use.fontawesome.com/releases/v5.0.4/js/all.js', array(), time(), false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/safetymail-form-admin.js', array( 'jquery', 'jquery-ui-sortable' ), time(), false );
        wp_localize_script( $this->plugin_name, 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234) );
    }

    public function add_menu() {
        $hook = add_menu_page(
            '',
            __('Safetymails', 'safetymail-form'),
            'manage_options',
            $this->plugin_name,
            [ $this, 'show_page' ],
            'dashicons-sos'
        );

        $hook = add_submenu_page(
            'safetymail-form',
            '',
            __('All Forms', 'safetymail-form'),
            'manage_options',
            "{$this->plugin_name}",
            [ $this, 'show_page' ]
        );

        $hook = add_submenu_page(
            '',
            '',
            __('Edit Form', 'safetymail-form'),
            'manage_options',
            "{$this->plugin_name}-edit",
            [ $this, 'show_editor' ]
        );

        $hook = add_submenu_page(
            'safetymail-form',
            '',
            __('New Form', 'safetymail-form'),
            'manage_options',
            "{$this->plugin_name}-new",
            [ $this, 'show_builder' ]
        );

        $hook = add_submenu_page(
            'safetymail-form',
            '',
            __('Settings', 'safetymail-form'),
            'manage_options',
            "{$this->plugin_name}-config",
            [ $this, 'show_config' ]
        );
    }

    public function getFormElements($id)
    {
        global $wpdb;

        if (!$id) {
            return '';
        }

        $result = $wpdb->get_results(
            "SELECT element FROM {$wpdb->prefix}safety_forms WHERE id=8 LIMIT 1",
            'ARRAY_A'
        );

        if (empty($result)) {
            return '';
        }

        return $result[0]['element'];
    }

    public function show_page()
    {
        $this->forms_list = new SafetyMail_Form_List();

        include_once(plugin_dir_path( __FILE__ ) . 'partials/safetymail-form-admin-display.php');
    }

    public function show_config()
    {
        $config = $this->getConfig();

        $config = empty($config) ? ['api_key' => '', 'api_ticket' => ''] : $config;

        include_once(plugin_dir_path( __FILE__ ) . 'partials/safetymail-form-admin-config.php');
    }

    public function show_builder()
    {
        include_once(plugin_dir_path( __FILE__ ) . 'partials/safetymail-form-admin-new.php');
    }

    public function show_editor()
    {
        $form = SafetyMail_Form_List::get_form( $_GET['id'] );

        include_once(plugin_dir_path( __FILE__ ) . 'partials/safetymail-form-admin-edit.php');
    }

    public function save_form()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "safety_forms";

        $formSaved = $wpdb->insert(
            $table_name,
            [
                'name' => sanitize_text_field( $_POST['name'] ),
                'element' => sanitize_text_field( $_POST['fields'] ),
                'email_recipient' => sanitize_text_field( $_POST['emailRecipient'] ),
                'email_replyto' => sanitize_text_field($_POST['emailReplyTo']),
                'subject' =>  sanitize_text_field( $_POST['subject'] ),
                'html' => sanitize_text_field( $_POST['html'] ),
                'element' => sanitize_text_field( $_POST['fields'] ),
                'action' => sanitize_text_field( $_POST['form_action'] ),
                'action_content' => sanitize_text_field( $_POST['actionContent'] ),
                'show_status' => sanitize_text_field( $_POST['showStatus'] ),
                'protected' => sanitize_text_field( $_POST['protected'] ),
                'api_key' => sanitize_text_field( $_POST['api_key'] ),
                'api_ticket' => sanitize_text_field( $_POST['api_ticket'] ),
                'invalid_callback' => sanitize_text_field( $_POST['invalid_callback'] )
            ]
        );

        if ($formSaved) {
            $formUpdated = $wpdb->update(
                $table_name,
                [
                    'code' => sanitize_text_field( "[safetymail_form id=\"{$wpdb->insert_id}\"]" )
                ],
                [
                    'id' => $wpdb->insert_id
                ]
            );
        }

        wp_die($formSaved);
    }

    public function edit_form()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "safety_forms";

        $formUpdated = $wpdb->update(
            $table_name,
            [
                'name' => sanitize_text_field( $_POST['name'] ),
                'element' => sanitize_text_field( $_POST['fields'] ),
                'email_recipient' => sanitize_text_field( $_POST['emailRecipient'] ),
                'email_replyto' => sanitize_text_field($_POST['emailReplyTo']),
                'subject' =>  sanitize_text_field( $_POST['subject'] ),
                'html' => sanitize_text_field( $_POST['html'] ),
                'element' => sanitize_text_field( $_POST['fields'] ),
                'action' => sanitize_text_field( $_POST['form_action'] ),
                'action_content' => sanitize_text_field( $_POST['actionContent'] ),
                'show_status' => sanitize_text_field( $_POST['showStatus'] ),
                'protected' => sanitize_text_field( $_POST['protected'] ),
                'api_key' => sanitize_text_field( $_POST['api_key'] ),
                'api_ticket' => sanitize_text_field( $_POST['api_ticket'] ),
                'invalid_callback' => sanitize_text_field( $_POST['invalid_callback'] )
            ],
            [
                'id' => sanitize_text_field( $_POST['id'] )
            ]
        );

        wp_die($formUpdated);
    }

    public function edit_config()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "safety_forms_config";

        $config = $this->getConfig();

        if (empty($config)) {
            $configUpdated = $wpdb->insert(
                $table_name,
                [
                    'host' => sanitize_text_field( $_POST['host'] ),
                    'port' => sanitize_text_field( $_POST['port'] ),
                    'email_sender' => sanitize_text_field( $_POST['email_sender'] ),
                    'sender_name' => sanitize_text_field( $_POST['sender_name'] ),
                    'require_auth' => $_POST['require_auth'] === 'SIM' ,
                    'user' => sanitize_text_field( $_POST['user'] ),
                    'pass' => openssl_encrypt ( $_POST['pass'] , self::METHOD , self::KEY )
                ]
            );
        } else {
            $configUpdated = $wpdb->update(
                $table_name,
                [
                    'host' => sanitize_text_field( $_POST['host'] ),
                    'port' => sanitize_text_field( $_POST['port'] ),
                    'email_sender' => sanitize_text_field( $_POST['email_sender'] ),
                    'sender_name' => sanitize_text_field( $_POST['sender_name'] ),
                    'require_auth' => $_POST['require_auth'] === 'SIM',
                    'user' => sanitize_text_field( $_POST['user'] ),
                    'pass' => openssl_encrypt ( $_POST['pass'] , self::METHOD , self::KEY )
                ],
                ['port' => $config['port']]
            );
        }
        wp_die($configUpdated);
    }

    public function getConfig()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "safety_forms_config";

        $sql = "SELECT * FROM $table_name LIMIT 1";

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        if (empty($result)) {
            return [];
        }

        $result[0]['pass'] = openssl_decrypt(
            $result[0]['pass'] ,
            self::METHOD ,
            self::KEY
        );

        return $result[0];
    }
}
