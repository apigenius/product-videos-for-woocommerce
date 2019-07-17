<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
   die;
}

if (!function_exists ('api_pv_callback_validate_options')) {
    function api_pv_callback_validate_options( $input ) {

         // if set, save the settings
        if ( isset( $input['api_pv_api_key'] ) ) {
        	$input['api_pv_api_key'] = sanitize_text_field( $input['api_pv_api_key'] );
        }

        if ( isset( $input['api_pv_max_videos'] ) ) {
            $input['api_pv_max_videos'] = sanitize_text_field(preg_replace('/\s+/', '', $input['api_pv_max_videos']));
        }

        $radio_options = api_pv_identifier_site_radio_field_callback();
        if ( ! isset( $input['api_pv_only_site'] ) ) {
        	$input['api_pv_only_site'] = null;
        }
        if ( ! array_key_exists( $input['api_pv_only_site'], $radio_options ) ) {
        	$input['api_pv_only_site'] = null;
        }

        $radio_options = api_pv_identifier_title_radio_field_callback();
        if ( ! isset( $input['api_pv_identifier_title'] ) ) {
        	$input['api_pv_identifier_title'] = null;
        }

        if ( ! isset( $input['api_pv_randomize'] ) ) {
        	$input['api_pv_randomize'] = null;
        }
        $input['api_pv_randomize'] = ( $input['api_pv_randomize'] == 1 ? 1 : 0 );

        if ( ! array_key_exists( $input['api_pv_identifier_title'], $radio_options ) ) {
        	$input['api_pv_identifier_title'] = null;
        }

        if ( ! isset( $input['api_pv_if_not_available'] ) ) {
        	$input['api_pv_if_not_available'] = null;
        }
        $input['api_pv_if_not_available'] = ( $input['api_pv_if_not_available'] == 1 ? 1 : 0 );

        if ( ! isset( $input['api_pv_featured_video'] ) ) {
        	$input['api_pv_featured_video'] = null;
        }
        $input['api_pv_featured_video'] = ( $input['api_pv_featured_video'] == 1 ? 1 : 0 );

        if ( isset( $input['api_pv_apigenius_user_name'] ) ) {
            $input['api_pv_apigenius_user_name'] = sanitize_text_field(preg_replace('/\s+/', '', $input['api_pv_apigenius_user_name']));
        }

        if ( ! isset( $input['api_pv_include_powered_by'] ) ) {
        	$input['api_pv_include_powered_by'] = null;
        }
        $input['api_pv_include_powered_by'] = ( $input['api_pv_include_powered_by'] == 1 ? 1 : 0 );

        if ( ! isset( $input['api_pv_hide_video_tab'] ) ) {
        	$input['api_pv_hide_video_tab'] = null;
        }
        $input['api_pv_hide_video_tab'] = ( $input['api_pv_hide_video_tab'] == 1 ? 1 : 0 );

        if ( isset( $input['api_pv_tab_name'] ) ) {
            $input['api_pv_tab_name'] = sanitize_text_field( $input['api_pv_tab_name'] );
        }
        if ( isset( $input['api_pv_tab_priority'] ) ) {
            $input['api_pv_tab_priority'] = sanitize_text_field( $input['api_pv_tab_priority'] );
        }
        if ( isset( $input['api_pv_video_width'] ) ) {
            $input['api_pv_video_width'] = sanitize_text_field( $input['api_pv_video_width'] );
        }
        if ( isset( $input['api_pv_video_height'] ) ) {
            $input['api_pv_video_height'] = sanitize_text_field( $input['api_pv_video_height'] );
        }

        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $select_options = array( 'default' => '' );
        foreach ( $attribute_taxonomies as $taxonomy ) {
            $taxonomy_name = $taxonomy->attribute_name;
            $taxonomy_label = $taxonomy->attribute_label;
            $name_label_array = array( sanitize_text_field($taxonomy_name) => sanitize_text_field($taxonomy_label));
            $select_options = array_merge( $select_options, $name_label_array );
        }

        // part_number attribute callback
        if ( ! isset( $input['api_pv_part_number_attribute'] ) ) {
        	$input['api_pv_part_number_attribute'] = null;
        }
        if ( ! array_key_exists( $input['api_pv_part_number_attribute'], $select_options ) ) {
        	$input['api_pv_part_number_attribute'] = null;
        }
        // brand attribute callback
        if ( ! isset( $input['api_pv_brand_attribute'] ) ) {
        	$input['api_pv_brand_attribute'] = null;
        }
        if ( ! array_key_exists( $input['api_pv_brand_attribute'], $select_options ) ) {
        	$input['api_pv_brand_attribute'] = null;
        }

        return $input;
    }
}
