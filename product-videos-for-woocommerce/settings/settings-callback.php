<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
   die;
}

// setting section description callbacks
if (!function_exists ('api_pv_general_options_section_callback')) {
    function api_pv_general_options_section_callback() {
    	echo wp_kses_post('<p>General settings for the plugin.  Get your free API key via the link below.</p>');
    }
}
if (!function_exists ('api_pv_video_accuracy_section_callback')) {
    function api_pv_video_accuracy_section_callback() {
    	echo wp_kses_post('<p>With these options you improve upon or vary the video search results.</p>');
    }
}
if (!function_exists ('api_pv_video_display_section_callback')) {
    function api_pv_video_display_section_callback() {
    	echo wp_kses_post('<p>This section of options will determine the display of the videos on the product page.</p>');
    }
}
if (!function_exists ('api_pv_assign_attributes_section_callback')) {
    function api_pv_assign_attributes_section_callback() {
    	echo wp_kses_post('<p>With these options you can assign attributes to there correct attribute slug.</p>');
    }
}

// callback: text field
if (!function_exists ('api_pv_text_field_callback')) {
    function api_pv_text_field_callback($args) {
    	$plugin_options = get_option('api_pv_options', api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? wp_kses_post($args['label']) : '';
    	$value = isset($plugin_options[$id]) ? sanitize_text_field($plugin_options[$id]) : '';
    	echo '<input id="api_pv_options_'. esc_html($id) .'" name="api_pv_options['. esc_html($id) .']" type="text" size="40" value="'. esc_html($value) .'"><br />';
    	echo '<label for="api_pv_options_'. esc_html($id) .'">'. wp_kses_post($label) .'</label>';
    }
}

// callback: textarea
if (!function_exists ('api_pv_textarea_field_callback')) {
    function api_pv_textarea_field_callback($args) {
        // allowed html tags for descriptions
        $allowed_html = array(
            'br' => array(),
            'strong' => array(),
            'u' => array()
        );
    	$plugin_options = get_option('api_pv_options', api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? wp_kses_post($args['label']) : '';
    	$value = isset($plugin_options[$id]) ? wp_kses(stripslashes_deep($plugin_options[$id]), $allowed_html) : '';
    	echo '<textarea id="api_pv_options_'. esc_html($id) .'" name="api_pv_options['. esc_html($id) .']" rows="5" cols="50">'. esc_textarea($value) .'</textarea><br />';
        echo '<label for="api_pv_options_'. esc_html($id) .'">'. wp_kses_post($label) .'</label>';
    }
}

// callback: checkbox field
if (!function_exists ('api_pv_check_box_callback')) {
    function api_pv_check_box_callback($args) {
    	$plugin_options = get_option('api_pv_options', api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? wp_kses_post($args['label']) : '';
    	$checked = isset($plugin_options[$id]) ? checked($plugin_options[$id], 1, false) : '';
    	echo '<input id="api_pv_options_'. esc_html($id) .'" name="api_pv_options['. esc_html($id) .']" type="checkbox" value="1"'. esc_html($checked) .'> ';
    	echo '<label for="api_pv_options_'. esc_html($id) .'">'. wp_kses_post($label) .'</label>';
    }
}

// radio field call back - video site
if (!function_exists ('api_pv_identifier_site_radio_field_callback_options')) {
    function api_pv_identifier_site_radio_field_callback_options() {
    	return array(
            ''  => esc_html__('All', 'product-videos-for-woocommerce'),
            'youtube'  => esc_html__('YouTube', 'product-videos-for-woocommerce'),
    		'facebook'  => esc_html__('Facebook', 'product-videos-for-woocommerce'),
            'dailymotion'  => esc_html__('Daily Motion', 'product-videos-for-woocommerce'),
            'vimeo'  => esc_html__('Vimeo', 'product-videos-for-woocommerce'),
    	);
    }
}
if (!function_exists ('api_pv_identifier_site_radio_field_callback')) {
    function api_pv_identifier_site_radio_field_callback($args) {
    	$plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? sanitize_text_field($args['label']) : '';
    	$selected_option = isset($plugin_options[$id]) ? sanitize_text_field($plugin_options[$id]) : '';
    	$radio_options = api_pv_identifier_site_radio_field_callback_options();
    	foreach ($radio_options as $value => $label) {
    		$checked = checked($selected_option === $value, true, false);
    		echo '<label><input name="api_pv_options['. esc_html($id) .']" type="radio" value="'. esc_html($value) .'"'. esc_html($checked) .'> ';
    		echo '<span>'. esc_html($label) .'</span></label><br />';
    	}
    }
}

// radio field call back - identifier in title
if (!function_exists ('api_pv_identifier_title_radio_field_callback_options')) {
    function api_pv_identifier_title_radio_field_callback_options() {
    	return array(
            'brand_title'  => esc_html__('Brand (recommended)', 'product-videos-for-woocommerce'),
    		'sku_title'  => esc_html__('SKU (limited results)', 'product-videos-for-woocommerce'),
            'part_number_title'  => esc_html__('Part Number (limited results)', 'product-videos-for-woocommerce'),
            'none_title'  => esc_html__('None', 'product-videos-for-woocommerce'),
    	);
    }
}
if (!function_exists ('api_pv_identifier_title_radio_field_callback')) {
    function api_pv_identifier_title_radio_field_callback($args) {
    	$plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? sanitize_text_field($args['label']) : '';
    	$selected_option = isset($plugin_options[$id]) ? sanitize_text_field($plugin_options[$id]) : '';
    	$radio_options = api_pv_identifier_title_radio_field_callback_options();
    	foreach ($radio_options as $value => $label) {
    		$checked = checked($selected_option === $value, true, false);
    		echo '<label><input name="api_pv_options['. esc_html($id) .']" type="radio" value="' . esc_html($value) .'"'. esc_html($checked) .'> ';
    		echo '<span>'. esc_html($label) .'</span></label><br />';
    	}
    }
}
// callback: select field
if (!function_exists ('api_pv_select_callback')) {
    function api_pv_select_callback($args) {
    	$plugin_options = get_option('api_pv_options', api_pv_default_options());
    	$id    = isset($args['id'])    ? sanitize_text_field($args['id'])    : '';
    	$label = isset($args['label']) ? sanitize_text_field($args['label']) : '';
    	$selected_option = isset($plugin_options[$id]) ? sanitize_text_field($plugin_options[$id]) : '';
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $select_options = array('' => '');
        foreach ($attribute_taxonomies as $taxonomy) {
            $taxonomy_name = $taxonomy->attribute_name;
            $taxonomy_label = $taxonomy->attribute_label;
            $name_label_array = array($taxonomy_name => $taxonomy_label);
            $select_options = array_merge($select_options, $name_label_array);
        }
    	echo '<select id="api_pv_options_'. esc_html($id) .'" name="api_pv_options['. esc_html($id) .']">';
    	foreach ($select_options as $value => $option) {
    		$selected = selected($selected_option === $value, true, false);
    		echo '<option value="'. esc_html($value) .'"'. esc_html($selected) .'>'. esc_html($option) .'</option>';
    	}
    	echo '</select> <label for="api_pv_options_'. esc_html($id) .'">'. esc_html($label) .'</label>';
    }
}
