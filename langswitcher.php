<?php
/**
 * Plugin Name:       Langswitcher
 * Description:       For Switch Domain Lang
 * Version:           1.0.0
 * Author:            Mc Makdonal
 * Author URI:        https://www.google.com
 * Text Domain:       Google
 * License:           FREE
 * License URI:       https://www.google.com
 * GitHub Plugin URI: https://www.google.com
 */

/*
 * Plugin constants
 */
if (!defined('LANG_URL')) {
    define('LANG_URL', plugin_dir_url(__FILE__));
}

if (!defined('LANG_PATH')) {
    define('LANG_PATH', plugin_dir_path(__FILE__));
}

class Langswitcher
{

    /**
     * The security nonce
     *
     * @var string
     */
    private $_nonce = 'langswitcher_admin';

    /**
     * The option name
     *
     * @var string
     */
    private $option_name = 'langswitcher_data';

    /**
     * Returns the saved options data as an array
     *
     * @return array
     */
    private function getData()
    {
        return get_option($this->option_name, array());
    }

    public function __construct()
    {
        // Admin page calls:
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('wp_ajax_save_langswitcher', array($this, 'save_langswitcher'));
        add_action('admin_enqueue_scripts', array($this, 'addAdminScripts'));
        add_action('wp_footer', array($this, 'sticky'));
        add_action('wp_enqueue_scripts', array($this, 'addStyleFront'));
    }

    public function addAdminMenu()
    {
        add_menu_page(
            __('Langswitcher', 'langswitcher'),
            __('Langswitcher', 'langswitcher'),
            'manage_options',
            'langswitcher',
            array($this, 'adminLayout'),
            'dashicons-networking'
        );
    }

    public function adminLayout()
    {
        $data = $this->getData();
        $status = (isset($data['status'])) ? $data['status'] : '';
        $flag = (isset($data['flag'])) ? $data['flag'] : '';
        $url = (isset($data['url'])) ? $data['url'] : '';
        $popup = (isset($data['popup'])) ? $data['popup'] : '';
        ?>
        <div class="ls-div">
            <form id="langswitcher_form">
                <label for="">สถานะการใช้งาน</label>
                <select class="ls-input" id="langswitcher_status" name="langswitcher_status">
                    <option value="disable" <?php echo ($status == "off") ? "selected" : ""; ?> >Disable</option>
                    <option value="enable" <?php echo ($status == "on") ? "selected" : ""; ?> >Enable</option>
                </select>

                <label for="">เลือกภาษา สำหรับ Redirct</label>
                <select class="ls-input" id="langswitcher_flag" name="langswitcher_flag">
                    <option value="th" <?php echo ($flag == "th") ? "selected" : ""; ?> >Thailand</option>
                    <option value="us" <?php echo ($flag == "us") ? "selected" : ""; ?> >United State</option>
                </select>

                <label for="">URL สำหรับ Redirect</label>
                <input type="url" value="<?php echo $url; ?>" class="ls-input" id="langswitcher_url" name="langswitcher_url" placeholder="เช่น https://www.mcmakdonal.com/en">

                <label for="">Popup แสดงเลือกภาษา หน้าแรก</label>
                <select class="ls-input" id="langswitcher_popup" name="langswitcher_popup">
                    <option value="off" <?php echo ($popup == "off") ? "selected" : ""; ?> >Off</option>
                    <option value="on" <?php echo ($popup == "on") ? "selected" : ""; ?> >On</option>
                </select>

                <input type="submit" class="ls-submit" value="บันทึก">
            </form>
        </div>
    <?php
}

    public function save_langswitcher()
    {
        if (wp_verify_nonce($_POST['security'], $this->_nonce) === false) {
            die('Invalid Request!');
        }

        $data = $this->getData();

        foreach ($_POST as $field => $value) {

            if (substr($field, 0, 13) !== "langswitcher_" || empty($value)) {
                continue;
            }

            // We remove the langswitcher_ prefix to clean things up
            $field = substr($field, 13);

            $data[$field] = $value;

        }

        $result = update_option($this->option_name, $data);
        if ($result) {
            echo "true";
        } else {
            echo "false";
        }
        die();
    }

    public function addAdminScripts()
    {
        wp_enqueue_script('langswitcer-admin', LANG_URL . '/assets/js.js', array(), 1.0);
        wp_enqueue_script('langswitcer-swal', LANG_URL . '/assets/swal.js', array(), 1.0);
        wp_enqueue_style('langswitcer-admin', LANG_URL . '/assets/css.css', false, '1.0.0');
        $admin_options = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            '_nonce' => wp_create_nonce($this->_nonce),
        );
        wp_localize_script('langswitcer-admin', 'langswitcer_exchanger', $admin_options);
    }

    public function sticky()
    {
        $data = $this->getData();
        $status = (isset($data['status'])) ? $data['status'] : '';
        $flag = (isset($data['flag'])) ? $data['flag'] : '';
        $url = (isset($data['url'])) ? $data['url'] : '';
        $popup = (isset($data['popup'])) ? $data['popup'] : '';

        if ($status == "enable") {
            echo '<div class="ls-bar">
            <a href="' . $url . '" class=""><img src="' . LANG_URL . '/img/' . $flag . '.png' . '" title="Switch Language" alt="Switch Language" ></a>
          </div>';

            if ($popup == "on") {

                $th = '<li><img src="' . LANG_URL . '/img/th.png' . '" title="Switch Language" alt="Switch Language" ></li>';
                $us = '<li><img src="' . LANG_URL . '/img/us.png' . '" title="Switch Language" alt="Switch Language" ></li>';

                $str = '<div id="myNav" class="overlay"><a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                        <div class="overlay-content">
                            <h3>Select Language</h3>
                            <ul>';

                if ($flag == "th") {
                    $str .= "<a href='" . get_site_url() . "'>" . $th . "</a>";
                    $str .= "<a href='" . $url . "'>" . $us . "</a>";
                }

                if ($flag == "us") {
                    $str .= "<a href='" . get_site_url() . "'>" . $us . "</a>";
                    $str .= "<a href='" . $url . "'>" . $th . "</a>";
                }

                $str .= '</ul>
                        </div>
                    </div>';

                echo $str;
            }

        }
    }

    public function addStyleFront()
    {
        wp_enqueue_style('langswitcer-admin', LANG_URL . '/assets/front.css', false, '1.0.0');
        wp_enqueue_script('langswitcer-front', LANG_URL . '/assets/front.js', array(), 1.0);
    }

}

new Langswitcher();