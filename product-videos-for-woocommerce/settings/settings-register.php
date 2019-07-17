<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
   die;
}

// register plugin settings
if (!function_exists ('api_pv_register_settings')) {
    function api_pv_register_settings() {
        $allowed_html = array(
            'span' => array(
                'style' => array()
            ),
            'br' => array(),
            'style' => array()
        );

    	// Register the plugin settings
    	register_setting(
    		'api_pv_options',
    		'api_pv_options',
    		'api_pv_validate_options'
    	);

        // Add the settings page sections
    	add_settings_section(
    		'api_pv_general_options_section',
    		'API Options',
    		'api_pv_general_options_section_callback',
    		'api-pv-settings'
    	);
        add_settings_section(
    		'api_pv_video_accuracy_section',
    		'Video Accuracy',
    		'api_pv_video_accuracy_section_callback',
    		'api-pv-settings'
    	);
        add_settings_section(
    		'api_pv_video_display_section',
    		'Video Display',
    		'api_pv_video_display_section_callback',
    		'api-pv-settings'
    	);
    	add_settings_section(
    		'api_pv_assign_attributes_section',
    		'Assign Attribute Options',
    		'api_pv_assign_attributes_section_callback',
    		'api-pv-settings'
    	);

        // register settings
        add_settings_field(
    		'api_pv_api_key',
            esc_html__('API Key', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_general_options_section',
    		[ 'id' => 'api_pv_api_key', 'label' => 'You can obtain your API Key by logging in or registering an account on <u><a href="https://www.apigenius.io/software/product-videos-for-woocommerce/#pricing" target="_blank">ApiGenius.io</a></u>.' ]
    	);

        add_settings_field(
    		'api_pv_only_site',
    		esc_html__('Only Search This Website', 'product-videos-for-woocommerce'),
    		'api_pv_identifier_site_radio_field_callback',
    		'api-pv-settings',
    		'api_pv_general_options_section',
    		[ 'id' => 'api_pv_only_site', 'label' => esc_html__('Only search the specified site during the video import.', 'product-videos-for-woocommerce') ]
    	);

        add_settings_field(
            'api_pv_randomize',
            esc_html__('Randomize Results', 'product-videos-for-woocommerce'),
            'api_pv_check_box_callback',
            'api-pv-settings',
            'api_pv_video_accuracy_section',
            [ 'id' => 'api_pv_randomize', 'label' => 'If you have a large number of very similar products, you may want to try this option.  Results may be less accurate but the embeded videos will have more variety.' ]
        );

        add_settings_field(
    		'api_pv_identifier_title',
    		esc_html__('Include Identifier in Video Title', 'product-videos-for-woocommerce'),
    		'api_pv_identifier_title_radio_field_callback',
    		'api-pv-settings',
    		'api_pv_video_accuracy_section',
    		[ 'id' => 'api_pv_identifier_title', 'label' => esc_html__('Require one of the following identifiers to be in the title of the video, for it to be embeded on the product page.', 'product-videos-for-woocommerce') ]
    	);

        add_settings_field(
            'api_pv_if_not_available',
            esc_html__('If No Identifier', 'product-videos-for-woocommerce'),
            'api_pv_check_box_callback',
            'api-pv-settings',
            'api_pv_video_accuracy_section',
            [ 'id' => 'api_pv_if_not_available', 'label' => 'If you are requiring one of the above identifiers (Sku, Brand, Part Number) in the title and the product does not have the specified identifier, tick this box if you would still like search and embed videos for the product, without requiring the indentifier in the title.' ]
        );

        add_settings_field(
            'api_pv_include_powered_by',
            esc_html__('Include Powered By Link and Earn Money', 'product-videos-for-woocommerce'),
            'api_pv_check_box_callback',
            'api-pv-settings',
            'api_pv_video_display_section',
            [ 'id' => 'api_pv_include_powered_by', 'label' => 'If you include a small powered by link under the videos, any time somone clicks on the link and signs up for a paid plan, you earn 20% commissions on an ongoing basis.  For full details please see the
            <u><a href="https://www.apigenius.io/partner-program-overview/" target="_blank">Partner Program</a></u> page.' ]
        );

        add_settings_field(
    		'api_pv_apigenius_user_name',
            esc_html__('ApiGenius User Name', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_apigenius_user_name', 'label' => 'You can get your username by going to the <u><a href="https://www.apigenius.io/partner-program/" target="_blank">Partner Program Account Page</a></u> on Apigenius. It will be labeled "Your affiliate username is:"' ]
    	);

        add_settings_field(
            'api_pv_featured_video',
            esc_html__('Include Videos in Image Gallery', 'product-videos-for-woocommerce'),
            'api_pv_check_box_callback',
            'api-pv-settings',
            'api_pv_video_display_section',
            [ 'id' => 'api_pv_featured_video', 'label' => 'With this option you can include the videos in your image gallery so users can scroll through and watch videos in product image area of your product pages.' ]
        );

        add_settings_field(
            'api_pv_hide_video_tab',
            esc_html__('Hide the Video Tab', 'product-videos-for-woocommerce'),
            'api_pv_check_box_callback',
            'api-pv-settings',
            'api_pv_video_display_section',
            [ 'id' => 'api_pv_hide_video_tab', 'label' => 'The video tab IS visible by default.  If you would like to hide the video tab, tick this option.' ]
        );

        add_settings_field(
    		'api_pv_max_videos',
            esc_html__('Max Number of Videos', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_max_videos', 'label' => 'The default is 1.  This sets how many videos you want to import if that many are available. If you only want to search for videos, then manually select them, you can set this to 0.' ]
    	);

        add_settings_field(
    		'api_pv_tab_name',
            esc_html__('Video Tab Name', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_tab_name', 'label' => 'The default name is Videos, but you can change it with this setting.' ]
    	);

        add_settings_field(
    		'api_pv_tab_priority',
            esc_html__('Video Tab Priority', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_tab_priority', 'label' => 'Please select a number from 1 to 50.  The higher the number, the sooner the Videos tab will be displayed on the product page, in the product detail tabs section.' ]
    	);

        // register settings
        add_settings_field(
    		'api_pv_video_width',
            esc_html__('Video Width', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_video_width', 'label' => 'Set the width of the video in the product video tab, i.e. 560' ]
    	);

        // register settings
        add_settings_field(
    		'api_pv_video_height',
            esc_html__('Video Height', 'product-videos-for-woocommerce'),
    		'api_pv_text_field_callback',
    		'api-pv-settings',
    		'api_pv_video_display_section',
    		[ 'id' => 'api_pv_video_height', 'label' => 'Set the height of the video in the product video tab, i.e. 350' ]
    	);

        add_settings_field(
    		'api_pv_brand_attribute',
            esc_html__('Brand', 'product-videos-for-woocommerce'),
    		'api_pv_select_callback',
            'api-pv-settings',
            'api_pv_assign_attributes_section',
    		[ 'id' => 'api_pv_brand_attribute', 'label' => '' ]
    	);

        add_settings_field(
    		'api_pv_part_number_attribute',
            esc_html__('Part Number Attribute', 'product-videos-for-woocommerce'),
    		'api_pv_select_callback',
            'api-pv-settings',
            'api_pv_assign_attributes_section',
    		[ 'id' => 'api_pv_part_number_attribute', 'label' => '' ]
    	);

    }
    add_action( 'admin_init', 'api_pv_register_settings' );
}
