<?php

/**
 *
 *
 * @link              https://www.apigenius.io/rgelhausen
 * @since             1.0.0
 * @package           product-videos-for-woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       Product Videos for Woocommerce
 * Plugin URI:        https://www.apigenius.io/software/product-videos-for-woocommerce/
 * Description:       This plugin allows users to manually and automatically search, find and embed videos, for all the products they sell, from sites like YouTube, Facebook and Vimeo. You can display the videos in a custom tab or in the image gallery sectioin.
 * Version:           1.0.0
 * Author:            ApiGenius.io
 * Author URI:        https://www.apigenius.io/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-videos-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}


/* create menu pages */
// Top level menu - Product Dashboard
if (!function_exists ('api_pv_dashboard')) {
	function api_pv_dashboard() {
	   add_menu_page('Product Videos for Woocommerce', 'Product Videos', 'manage_woocommerce', 'api-pv-dashboard', 'api_pv_dashboard_callback', 'dashicons-editor-code', 56);
	}
	add_action('admin_menu', 'api_pv_dashboard');
}
if (!function_exists ('api_pv_dashboard_callback')) {
	function api_pv_dashboard_callback() {
	   include(plugin_dir_path(__FILE__) . 'admin-pages/dashboard.php');
    }
}

// Product Data Page
if (!function_exists ('api_pv_data_details')) {
	function api_pv_data_details() {
	   add_submenu_page('api-pv-dashboard', 'Video Data', 'Video Data','manage_woocommerce', 'api-pv-data-details', 'api_pv_data_details_callback');
	}
	add_action('admin_menu', 'api_pv_data_details');
}
if (!function_exists ('api_pv_data_details_callback')) {
	function api_pv_data_details_callback() {
	   include(plugin_dir_path(__FILE__) . 'admin-pages/data-details.php');
	}
}

// Automations admin page
if (!function_exists ('api_pv_automation_page')) {
	function api_pv_automation_page() {
	   add_submenu_page('api-pv-dashboard', 'Automations', 'Automations','manage_options', 'api-pv-automations', 'api_pv_automation_page_callback');
	}
	add_action('admin_menu', 'api_pv_automation_page');
}
if (!function_exists ('api_pv_automation_page_callback')) {
	function api_pv_automation_page_callback() {
	   include(plugin_dir_path(__FILE__) . 'admin-pages/automation-page.php');
	}
}

// Settings page
if (!function_exists ('api_pv_settings')) {
	function api_pv_settings() {
	   add_submenu_page('api-pv-dashboard', 'Settings', 'Settings','manage_woocommerce', 'api-pv-settings', 'api_pv_settings_callback');
	}
	add_action('admin_menu', 'api_pv_settings');
}

if (!function_exists ('api_pv_settings_callback')) {
	function api_pv_settings_callback() {
	   include(plugin_dir_path(__FILE__) . 'admin-pages/settings.php');
	}
}

// How to page
if (!function_exists ('api_pv_how_to')) {
	function api_pv_how_to() {
	   add_submenu_page('api-pv-dashboard', 'How To & Help', 'How To & Help','manage_woocommerce', 'api-pv-how_to', 'api_pv_how_to_callback');
	}
	add_action('admin_menu', 'api_pv_how_to');
}
if (!function_exists ('api_pv_how_to_callback')) {
	function api_pv_how_to_callback() {
	   include(plugin_dir_path(__FILE__) . 'admin-pages/how-to.php');
	}
}

/* include pages */
// Plugin settings
include(plugin_dir_path(__FILE__) . 'settings/settings-register.php');
include(plugin_dir_path(__FILE__) . 'settings/settings-callback.php');
include(plugin_dir_path(__FILE__) . 'settings/settings-validation.php');

// primary functions for the plugin
include(plugin_dir_path(__FILE__) . 'functions/functions-primary.php');
// plugin hooks - developer hooks
include(plugin_dir_path(__FILE__) . 'functions/functions-shared.php');
// automation functions
include(plugin_dir_path(__FILE__) . 'functions/functions-automations.php');

// woocommerce custom fields
include(plugin_dir_path(__FILE__) . 'functions/functions-woocommerce.php');

/* Default options */
if (!function_exists ('api_pv_default_options')) {
	function api_pv_default_options() {
		return array(
			'api_pv_api_key' => '',
			'api_pv_only_site' => '',
			'api_pv_identifier_title' => '',
			'api_pv_if_not_available' => 0,
			'api_pv_brand_exclude_words' => '',
			'api_pv_brand_dont_update' => '',
			'api_pv_trim_title' => '',
			'api_pv_title_exclude_words' => '',
			'api_pv_exclude_numbers' => '',
			'api_pv_include_powered_by' => '',
			'api_pv_include_powered_by' => '',
			'api_pv_apigenius_user_name' => '',
			'api_pv_featured_video' => '',
			'api_pv_hide_video_tab' => '',
			'api_pv_max_videos' => '',
			'api_pv_tab_name' => '',
			'api_pv_tab_priority' => '',
			'api_pv_video_width' => 560,
			'api_pv_video_height' => 340,
			'api_pv_brand_attribute' => '',
			'api_pv_part_number_attribute' => ''
		);
	}
}

function api_pv_custom_cron( $schedules ){
    if(!isset($schedules['5min'])){
        $schedules['5min'] = array(
            'interval' => 5*60,
            'display' => __('Once every 5 minutes'));
    }
    return $schedules;
}
add_filter( 'cron_schedules', 'api_pv_custom_cron' );

/* activation and deactivation code */

// activation
// create options
if (!function_exists ('api_pv_create_options')) {
	function api_pv_create_options() {
		add_option('api_pv_automation_all');
		add_option('api_pv_automation_count', 0);
		add_option('api_pv_total_products_start', 0);
		add_option('api_pv_product_store_count', 0);
		add_option('api_pv_automation_products_per');
	}
	register_activation_hook(__FILE__, 'api_pv_create_options');
}

// schedule event used for automations
function api_pv_cron() {
	if (! wp_next_scheduled ('api_pv_automation_hook')) {
		wp_schedule_event(time(), '5min', 'api_pv_automation_hook');
	}
}
register_activation_hook(__FILE__, 'api_pv_cron');

// deactivation
// remove plugin created events
if (!function_exists ('api_pv_cron_deactivation')) {
	function api_pv_cron_deactivation() {
		wp_clear_scheduled_hook('api_pv_automation_hook');
	}
	register_deactivation_hook(__FILE__, 'api_pv_cron_deactivation');
}
