<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.henriquerodrigues.me
 * @since      1.0.0
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/public
 * @author     Henrique Rodrigues <henoliv@gmail.com>
 */
class Safetymail_Form_Public {

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

    const METHOD = 'aes-256-ctr';
    const KEY = '46i4UjFdTq19d7XBrCzK9d';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

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
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/safetymail-form-public.css', array(), time(), 'all' );

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

        wp_enqueue_script( 'form-render', plugin_dir_url( __FILE__ ) . 'js/form-render.min.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, false );
        wp_enqueue_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), time(), false );
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/safetymail-form-public.js', array( 'jquery' ), time(), false );
        wp_localize_script(
            $this->plugin_name,
            'ajax_object',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'we_value' => 1234
            )
        );
    }

    public function add_code() {
        add_shortcode( 'safetymail_form', [$this, 'form_hook'] );
    }

    public function form_hook($atts){

        $a = shortcode_atts(array('id' => 0), $atts);
        return $this->getFormElements($a['id']);
    }

    /**
     * retrieves the form elements from the database
     *
     * @param int $id
     * @return void
     */
    public function getFormElements($id)
    {
        $elements = $this->getForm($id);

        if (empty($elements)) {
            return '';
        }

        switch ($elements['action']) {
            case 'NOTHING':
                $formHeader = '<form action="" class="safetymail" data-type="NOTHING" method="POST">';
                break;
            case 'MESSAGE':
                $formHeader = "<form action=\"\" class=\"safetymail\" data-type=\"MESSAGE\" method=\"POST\" data-message=\"{$elements['action_content']}\">";
                break;
            case 'REDIRECT':
                $formHeader = "<form action=\"{$elements['action_content']}\" data-type=\"REDIRECT\" class=\"safetymail\" method=\"POST\">";
                break;
        }
        echo <<<LIMITER
<div id="success-container" class="alert alert-success hidden"></div>
        <div id="error-container" class="alert alert-danger hidden"></div>
        $formHeader
            <div class="form-render" data-elements='{$elements['element']}'></div>
            <input type="hidden" name="safety_id" id="safetyId" value="$id" />
            <input type="hidden" name="key" id="key" value="{$elements['api_key']}" />
            <input type="hidden" name="ticket" id="ticket" value="{$elements['api_ticket']}" />
            <input type="hidden" name="callback" id="callback" value="{$elements['invalid_callback']}" />
        </form>
LIMITER;
    }

    public function send_mail()
    {
        $config = $this->getConfig();

        if (empty($config)) {
            return false;
        }

        require(ABSPATH . WPINC . '/class-phpmailer.php');
        require(ABSPATH . WPINC . '/class-smtp.php');

        $form = $this->getForm($_POST['id']);

        $htmlMessage = '';

        if (intval($form['html'])) {
            foreach ($_POST['fields'] as $field => $data){
                $safetymailFields .= "<tr><td class=\"first\">{$data['name']}</td><td>{$data['value']}</td></tr>";
            }

            $template = file_get_contents(plugin_dir_url( __FILE__ ) . 'partials/safetymail-form-public-template.html');

            $htmlMessage = str_replace('SAFETYMAIL_FIELDS', $safetymailFields, $template);
        }

        $textMessage = "Contato\n";

        foreach ($_POST['fields'] as $field => $data){
            $textMessage .= "{$data['name']}: {$data['value']}\n";
        }

        try{
            $mail = new PHPMailer;
            $mail->isSMTP();

            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->Host = $config['host'];
            $mail->Port = $config['port'];

            $mail->setFrom($config['email_sender'], $config['sender_name']);
            $mail->addReplyTo($form['email_replyto']);
            $mail->addAddress($form['email_recipient']);
            $mail->Subject = $form['subject'];

            if ($config['require_auth']) {
                $mail->SMTPAuth = true;
                $mail->Username = $config['user'];
                $mail->Password = $config['pass'];
            }

            if (!empty($htmlMessage)) {
                $mail->msgHTML($htmlMessage);
            }

            $mail->AltBody = $textMessage;

            $mail->Send();
            return true;
        } catch (phpmailerException $e) {
            return false;
        }
    }

    /**
     * retrieves the form from the database
     *
     * @param int $id
     * @return void
     */
    public function getForm($id)
    {
        global $wpdb;

        if (!$id) {
            return '';
        }

        $result = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}safety_forms WHERE id=$id LIMIT 1",
            'ARRAY_A'
        );

        return empty($result) ? $result : $result[0];
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
            $result[0]['pass'],
            self::METHOD,
            self::KEY
        );

        return $result[0];
    }
}
