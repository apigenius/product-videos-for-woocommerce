<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
   die;
}

// This class of functions creates, displays and saves the product custom fields and tab on the product edit page.
// Check to make sure WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    // only run if there's no other class with this name
    global $post;
    if (! class_exists('API_pv_CUSTOM_FIELDS')){
        class API_pv_CUSTOM_FIELDS {
            public function __construct(){
                add_filter('woocommerce_product_data_tabs', array($this, 'api_pv_tab'), 20);
                add_action('woocommerce_product_data_panels', array($this, 'woocommerce_product_data_panels'));
                add_action('woocommerce_process_product_meta', 'API_pv_CUSTOM_FIELDS::save', 20, 2);
            }

            public function api_pv_tab($product_data_tabs){
                $product_data_tabs['api_pv_tabs'] = array(
                    'label'  => __(esc_html('Product Videos'), 'api-pv-custom-fields'),
                    'target' => 'api_pv_tabs',
                    'class'  => array()
             );

                return $product_data_tabs;
            }

            public static function save($post_id, $post){
                // update post meta
                if(isset($_POST['api_pv_last_updated'])){
                    update_post_meta($post_id, 'api_pv_last_updated', sanitize_text_field(wc_clean($_POST['api_pv_last_updated'])));
                }
                if(isset($_POST['api_pv_video_urls_manual'])){
                    update_post_meta($post_id, 'api_pv_video_urls_manual', sanitize_text_field(wc_clean($_POST['api_pv_video_urls_manual'])));
                }
                if(isset($_POST['api_pv_search_query'])){
                    update_post_meta($post_id, 'api_pv_search_query', sanitize_text_field(wc_clean($_POST['api_pv_search_query'])));
                }
                if(isset($_POST['api_pv_job_status'])){
                    update_post_meta($post_id, 'api_pv_job_status', sanitize_text_field(wc_clean($_POST['api_pv_job_status'])));
                }
                if(isset($_POST['api_pv_video_urls'])){
                    update_post_meta($post_id, 'api_pv_video_urls', sanitize_text_field(wc_clean($_POST['api_pv_video_urls'])));
                }
                if(isset($_POST['api_pv_json'])){
                    update_post_meta($post_id, 'api_pv_json', sanitize_text_field(wc_clean($_POST['api_pv_json'])));
                }
            }

            public function woocommerce_product_data_panels(){
                $post_id = get_the_ID();
                ?>
                <div id='api_pv_tabs' class='panel woocommerce_options_panel'>
                    <?php
                        // display the product custom field tab
                        echo '<p><strong>Product Videos for Woocommerce</strong></p>';
                        echo '<p>To manually enter or manage imported videos for this product <a href="/wp-admin/admin.php?page=api-pv-data-details&keyword&product_id=' . $post_id . '&status&sort_by&per_page&SearchProducts=Search" target="_blank">Click Here</a>';
                        woocommerce_wp_text_input(array(
                            'id'          => 'api_pv_job_status',
                            'data_type'   => 'text',
                            'label'       => __(esc_html('Update Status'), 'api-pv-custom-fields'),
                            'custom_attributes' => array('readonly' => 'readonly')
                       ));
                        woocommerce_wp_text_input(array(
                            'id'          => 'api_pv_video_urls_manual',
                            'data_type'   => 'text',
                            'label'       => __(esc_html('Manually Enter Video Ids'), 'api-pv-custom-fields'),
                            'description'   => __(esc_html('If you would like to manually enter the video ids for this product, you can do so here. Enter only the video slug, seperated with a | symbol, for example: JZcu_VRBv_Y|tj2L4Hrpr5M|JIPK2NQ85Fc'), 'woocommerce'),
                            'desc_tip'    => true,
                            'custom_attributes' => array('readonly' => 'readonly')
                       ));
                        woocommerce_wp_text_input(array(
                            'id'          => 'api_pv_last_updated',
                            'data_type'   => 'text',
                            'label'       => __(esc_html('Last Updated'), 'api-pv-custom-fields'),
                            'placeholder' => '',
                            'description'   => __(esc_html('This is the date of the last update.'), 'woocommerce'),
                            'desc_tip'    => true,
                            'custom_attributes' => array('readonly' => 'readonly')
                       ));
                       woocommerce_wp_text_input(array(
                           'id'          => 'api_pv_search_query',
                           'data_type'   => 'text',
                           'label'       => __(esc_html('Search Query Used'), 'api-pv-custom-fields'),
                           'placeholder' => '',
                           'description'   => __(esc_html('This is the search query that was used when importing videos, on the last update.'), 'woocommerce'),
                           'desc_tip'    => true,
                           'custom_attributes' => array('readonly' => 'readonly')
                      ));
                        woocommerce_wp_text_input(array(
                            'id' => 'api_pv_video_urls',
                            'label' => __(esc_html('Imported Video Ids'), 'api-pv-custom-fields'),
                            'data_type' => 'string',
                            'custom_attributes' => array('readonly' => 'readonly')
                       ));
                       woocommerce_wp_textarea_input(array(
                           'id'          => 'api_pv_json',
                           'data_type'   => 'json',
                           'label'       => __(esc_html('Video JSON'), 'api-pv-custom-fields'),
                           'description'   => __('This is the full JSON response from the videos API.', 'woocommerce'),
                           'desc_tip'    => true,
                           'custom_attributes' => array('readonly' => 'readonly')
                     ));
                    ?>
                </div>
                <?php
            }

        }
        $GLOBALS['api_pv_identifier'] = new API_pv_CUSTOM_FIELDS();
    }
}
