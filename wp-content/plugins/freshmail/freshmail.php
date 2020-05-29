<?php
/*
Plugin Name: Freshmail
Plugin URI: ...
Description: Plugin for integration freshmail with wordpress
Version: 1.0
Author: Frontkom - Adrian Pawlak
Author URI: ...
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


class freshmail
{
    private $options;

    public function __construct() {
        add_action("wp_ajax_helpuj_newsletter", "helpuj_newsletter");
        add_action("wp_ajax_nopriv_helpuj_newsletter", "helpuj_newsletter");
        add_action('admin_enqueue_scripts', 'freshmail_enqueue_script');
        add_action('admin_menu', array($this,'add_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    function freshmail_enqueue_script()
    {
        wp_enqueue_script( 'custom_script_js', plugins_url('js/newsletter.js', __FILE__ ), '1.0.0', false );
    } // add external scripts

    public function add_page() {
        add_options_page(
            'Settings Admin',
            'Freshmail',
            'manage_options',
            'freshmail_settings_page',
            array($this, 'create_page')
        );
    } // add page in settings

    public function create_page() {
        $this->options = get_option('freshmail');
        ?>
        <div class="wrap">
            <h2>Freshmail mailing</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('freshmail_options');
                do_settings_sections('freshmail_settings_page');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    } // create plugin page


    public function page_init()
    {
        register_setting(
            'freshmail_options',
            'freshmail',
            array($this, 'sanitize')
        );

        add_settings_section(
            'freshmail_section',
            'Zarządzanie pluginem do serwisu Freshmail',
            array($this, 'section_callback'),
            'freshmail_settings_page'
        );

        add_settings_field(
            'freshmail_list_id',
            'Freshmail ID list',
            array($this, 'freshmail_list_id_callback'),
            'freshmail_settings_page',
            'freshmail_section'
        );

        add_settings_field(
            'freshmail_bearer_id',
            'Freshmail bearer',
            array($this, 'freshmail_bearer_id_callback'),
            'freshmail_settings_page',
            'freshmail_section'
        );

        add_settings_field(
            'freshmail_url',
            'Freshmail url',
            array($this, 'freshmail_url_callback'),
            'freshmail_settings_page',
            'freshmail_section'
        );
    } // initial page

    public function section_callback() {
        echo 'Wprowadź swoje ustawienia poniżej:';
    }

    public function freshmail_list_id_callback(){
        if(isset($this->options['freshmail_list_id'])) $freshmail_list_id = esc_attr($this->options['freshmail_list_id']);
        echo '<input type="text" id="freshmail_list_id" name="freshmail[freshmail_list_id]" value="'.$freshmail_list_id.'">';
    } // callback freshmail list id

    public function freshmail_bearer_id_callback(){
        if(isset($this->options['freshmail_bearer_id'])) $freshmail_bearer_id = esc_attr($this->options['freshmail_bearer_id']);
        echo '<input type="text" id="freshmail_bearer_id" name="freshmail[freshmail_bearer_id]" value="'.$freshmail_bearer_id.'">';
    } // callback freshmail bearer id

    public function freshmail_url_callback(){
        if(isset($this->options['freshmail_url'])) $freshmail_url = esc_attr($this->options['freshmail_url']);
        echo '<input type="text" id="freshmail_url" name="freshmail[freshmail_url]" value="'.$freshmail_url.'">';
    } // callback freshmail url

    public function sanitize($input){
        $new_input = array();

        if(isset($input['freshmail_list_id']))
            $new_input['freshmail_list_id'] = sanitize_text_field($input['freshmail_list_id']);

        if(isset($input['freshmail_bearer_id']))
            $new_input['freshmail_bearer_id'] = sanitize_text_field($input['freshmail_bearer_id']);

        if(isset($input['freshmail_url']))
            $new_input['freshmail_url'] = sanitize_text_field($input['freshmail_url']);

        return $new_input;
    } // clear text in input


    function helpuj_newsletter() {

        $email = $_REQUEST['email'];
        $agree = $_REQUEST['agree'];
        $nonce = $_REQUEST['nonce'];
        $newsletter_list = 'r8kzmwue5y';
        $bearer = 'MaWPQx.m4VBxbmAv6tXXZ8m78ErIE8Q0b4r7WUG9B0';
        $freshmail_url = "https://api.freshmail.com/rest/subscriber/add";

        if (wp_verify_nonce( $nonce, 'helpuj-ajax')) {
            if($agree) {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $freshmail_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>"{\"email\":\"$email\",\"list\":\"$newsletter_list\"}",
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer $bearer",
                        "Content-Type: application/json",
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                echo $response;
                die();
            } else {
                echo 'No agree';
                die();
            }
        } else {
            wp_send_json_error( 'Invalid security token sent.' );
            wp_die();
        }
    }
}

$freshmail_settings_page = new freshmail();
