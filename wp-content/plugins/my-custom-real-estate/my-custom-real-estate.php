<?php
/**
 * Plugin Name: My Custom Real Estate Manager
 * Description: Реєстрація CPT "Об'єкт нерухомості" і таксономії "Район"
 * Version: 1.0
 * Author: T.Pyat
 * Text Domain: realestate
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

define('RE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RE_PLUGIN_VERSION', '1.0');

require_once RE_PLUGIN_PATH . 'includes/class-estate-post-type.php';
add_action('init', ['Estate_Post_Type', 'register']);

require_once RE_PLUGIN_PATH . 'includes/real-estate-api.php';
require_once RE_PLUGIN_PATH . 'includes/real-estate-filter.php';
require_once RE_PLUGIN_PATH . 'includes/real-estate-widget.php';
require_once RE_PLUGIN_PATH . 'includes/class-estate-query-modifier.php';

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('estate-ajax', RE_PLUGIN_URL . 'assets/js/estate-ajax.js', ['jquery'], RE_PLUGIN_VERSION, true);

    wp_localize_script('estate-ajax', 'estate_ajax', [
        'url'     => admin_url('admin-ajax.php'),
        'i18n'    => [
            'loading' => __('Завантаження...', 'realestate'),
            'error'   => __('Сталася помилка при запиті.', 'realestate'),
        ]
    ]);
});

function re_load_plugin_textdomain() {
    load_plugin_textdomain('realestate', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 're_load_plugin_textdomain');

add_action('admin_init', 're_check_acf_plugin');
function re_check_acf_plugin() {
    if (!function_exists('acf_add_local_field_group')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>' . esc_html__('Real Estate Plugin:', 'realestate') . '</strong> ' .
                esc_html__('Для роботи плагіна потрібно встановити і активувати', 'realestate') .
                ' <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">' .
                esc_html__('Advanced Custom Fields', 'realestate') . '</a>.</p></div>';
        });
    } else {
        require_once RE_PLUGIN_PATH . 'includes/acf-fields.php';
    }
}


new Estate_Query_Modifier();