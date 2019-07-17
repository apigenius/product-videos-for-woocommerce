<?php

    // If this file is called directly, abort.
    if (! defined('WPINC')) {
       die;
    }

    // function used to search for videos with the ApiGenius Videos API
    // API Home Page - https://www.apigenius.io/api/videos-api/
    // API Documentation Page - https://docs.apigenius.io/docs/services/videos/operations/get-videos
if (!function_exists ('api_pv_get_video')) {
    function api_pv_get_video($product_id, $query, $website, $existing_data) {
        // defaults
        $product = wc_get_product($product_id);
        $video_url = '';
        $video_title = '';
        $result = '';
        $current_video = 1;
        $video_url_array = [];

        // get plugin options
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $api_key = sanitize_text_field($plugin_options['api_pv_api_key']);
        $max_videos = sanitize_text_field($plugin_options['api_pv_max_videos']);
        if ($max_videos == '') {
            $max_videos = 1;
        }
        $identifier_title = sanitize_text_field($plugin_options['api_pv_identifier_title']);
        $if_not_available = sanitize_text_field($plugin_options['api_pv_if_not_available']);
        if ($if_not_available == 1) {
            $if_not_available_text = 'Yes';
        } else {
            $if_not_available_text = 'No';
        }
        if ($website == '') {
            $website = sanitize_text_field($plugin_options['api_pv_only_site']);
        }

        // randomize option
        $randomize = sanitize_text_field($plugin_options['api_pv_randomize']);

        // get product data
        $title = get_the_title($product_id);
        $permalink = get_the_permalink($product_id);

        // get the identifiers
        $sku = get_post_meta($product_id, '_sku', true);
        $part_number = api_pv_get_identifier($product_id, 'part_number');
        $brand = api_pv_get_identifier($product_id, 'brand');

        // used in error messages
        $brand_slug = sanitize_text_field($plugin_options['api_pv_brand_attribute']);
        $part_number_slug = sanitize_text_field($plugin_options['api_pv_part_number_attribute']);

        // check for an api key
        if (!$api_key) {
        	echo 'Please enter your API Key on the plugin <a href="/wp-admin/admin.php?page=api-pv-settings" target="_blank">settings page</a>.';
        } elseif ($identifier_title == 'sku_title' && $sku == '' && $if_not_available == 0) {
            echo 'Product ID: ' . esc_html(($product_id)) . ' - <span style="color: red;">Video not embedded.</span><br />';
            echo ' - Plugin Settings: Require Sku = True<br />';
            echo ' - Plugin Settings: If No Identifier Run Anyways = False<br />';
            if ($sku == '') {
                echo ' - <span style="color: red;">You have chosen to require the Sku in the Video Title but there is no Sku for this product</span>.<br />';
                echo '- If you would like to run the update anyways, in cases like this, you can enable the If No Identifier option in the plugin settings.<br />';
            }
            // update plugin status
            update_post_meta($product_id, 'api_pv_job_status', 'skipped');
            // update last modified date
            api_pv_modified_data($product_id);
        } elseif ($identifier_title == 'brand_title' && $if_not_available == 0 && ($brand == 'none' || $brand_slug == '')) {
            echo 'Product ID: ' . esc_html($product_id) . ' - <span style="color: red;">Video not embedded.</span><br />';
            echo ' - Plugin Settings: Require Brand = True<br />';
            echo ' - Plugin Settings: If No Identifier Run Anyways = False<br />';
            if ($brand_slug == '') {
                echo ' - <span style="color: red;">You have chosen to require the Brand in the Video Title but you have not set the Brand attribute in the Assign Attribute Options section of the <a href="/wp-admin/admin.php?page=api-pv-settings" target="_blank">Plugin Settings</a>.</span><br />';
            } elseif ($brand == 'none') {
                echo ' - <span style="color: red;">You have chosen to require the Brand in the Video Title but there is no Brand for this product.</span><br />
                - If you would like to run the update anyways, in cases like this, you can enable the If No Identifier option in the plugin settings.<br />';
            }
            update_post_meta($product_id, 'api_pv_job_status', 'skipped');
            api_pv_modified_data($product_id);
        } elseif ($identifier_title == 'part_number_title' && $if_not_available == 0 && ($part_number == 'none' || $part_number_slug == '')) {
            echo 'Product ID: ' . esc_html($product_id) . ' - <span style="color: red;">Video not embedded.</span><br />';
            echo ' - Plugin Settings: Require Part Number = True<br />';
            echo ' - Plugin Settings: If No Identifier Run Anyways = False<br />';
            if ($part_number_slug == '') {
                echo ' - <span style="color: red;">You have chosen to require the Part Number in the Video Title but you have not set the Part Number attribute in the Assign Attribute Options section of the <a href="/wp-admin/admin.php?page=api-pv-settings" target="_blank">Plugin Settings</a>.</span><br />';
            } elseif ($part_number == 'none') {
                echo ' - <span style="color: red;">You have chosen to require the Part Number in the Video Title but there is no Part Number for this product.</span><br />
                - If you would like to run the update anyways, in cases like this, you can enable the If No Identifier option in the plugin settings.<br />';
            }
            update_post_meta($product_id, 'api_pv_job_status', 'skipped');
            api_pv_modified_data($product_id);
        } else {
            // check to see if we want to use the json data saved from a previous update
            if ($existing_data == 'true') {
                $video_urls = get_post_meta($product_id, 'api_pv_video_urls', true);
                $response = get_post_meta($product_id, 'api_pv_json', true);
                if (! is_wp_error($response)) {
                    $result = json_decode($response);
                    if (! is_wp_error($video_urls)) {
                        $video_urls = json_decode($video_urls);
                        $video_count = count($result);
                    } else {
                        $video_count = 0;
                    }
                } else {
                    echo 'The saved video data for Product ID ' . esc_html($product_id) . ' was not able to be used.  Please run the update without the Existing Data checkbox selected.<br />';
                    $result = '';
                }
            } else {

                // build the api query parameters
                if ($query == '') {
                    $query = $title;
                }
                // update the query custom field for the product
                update_post_meta($product_id, 'api_pv_search_query', $query);
                // build the api query parameters
                $api_endpoint = 'https://api.apigenius.io/v1/videos';
                if ($website !== '') {
                    $query_url = '?website=' . strtolower($website) . '&query=' . $query;
                } else {
                    $query_url = '?query=' . $query;
                }
                $url = $api_endpoint . $query_url;
            	$args = array(
                        'timeout'     => 30,
                		'headers' => array(
                        'ApiGenius_API_Key' => $api_key,
                   )
            	);
            	$response = wp_remote_get($url, $args);
                if (! is_wp_error($response)) {
                    $result = json_decode($response['body']);
                } else {
                    echo '<p><span style="color: red;">Error</span> There was an error processing product ID ' . esc_html($product_id) . '. Please refresh the page and try again.</p>';
                    $result = '';
                }

                $status_code = isset($result->{'statusCode'}) ? $result->{'statusCode'} : '';
                $message = isset($result->{'message'}) ? $result->{'message'} : '';

                // if there is an error message from an invalid key
                if ($message !== '' && $status_code == 401) {
                    $error_message = $result->{'message'};
                    $result = '';
                    if ($error_message == 'Access denied due to invalid subscription key. Make sure to provide a valid key for an active subscription.') {
                        echo '<p>Access Denied: Your API key is invalid. Please check the <a href="/wp-admin/admin.php?page=api-pv-settings" target="_blank">Plugin Settings Page</a>.</p>';
                        echo '<p><strong>Please Note:</strong>  If you have recently changed your subscription, you will need to use your New API Key which can be found on the  <a href="https://www.apigenius.io/keys-usage/" target="_blank">Apigenius.io Keys & Usage Page.</p>';
                    }
                    EXIT;
                } elseif ($status_code == 403) {
                    // out of api calls
                    if (strpos($message, 'Out of call volume quota') !== false) {
                        echo 'You are are out of monthly API credits.  You can check your balance and upgrade your account by <a href="https://www.apigenius.io/keys-usage/" target="_blank">Clicking Here</a>.';
                        update_option('api_pv_automation_count', 0);
                        update_option('api_pv_total_product_count', 0);
                        update_option('api_pv_automation_all', 'call_limit_reached');
                    }
                    EXIT;
                }
            }

            if ($result !== '') {
                // get the status code
                echo '<div style="border: 1px solid #d8d8d8;background: #fafafa;padding: 0px 10px 20px;">';
                if (isset($result->{'items'})) {
            		$items = $result->{'items'};
                    // randomize the videos processed for similar products
                    if ($randomize == '1') {
                        shuffle($items);
                    }
            		$count = count($items);
            	} else {
            		$count = 0;
            	}
                $numbers = range(1, 20);
                echo '<p><u>Query Settings</u></p>';
                if ($website !== '') {
                    echo '<strong>Website Searched:</strong> ' . esc_html(ucwords($website)) . '<br />';
                } else {
                    echo '<strong>Websites Searched:</strong> All<br />';
                }
                echo '<strong>Max Videos:</strong> ' . $max_videos . '<br />';
                if ($identifier_title !== '') {
                    echo '<strong>Require Identifier in Title:</strong> ' . esc_html(str_replace('_title', '', ucwords($identifier_title))) . '<br />';
                }
                if ($identifier_title !== '' && $identifier_title !== 'none_title') {
                    echo '<strong>If None Run Anyways:</strong> ' . esc_html($if_not_available_text) . '<br />';
                }
                if ($randomize == '1') {
                    echo '<strong>Randomized Results:</strong> True<br />';
                } else {
                    echo '<strong>Randomized Results:</strong> False<br />';
                }
                // show query used
                if (isset($query)) {
                    echo '<p><strong>Video Query:</strong> ' . esc_html($query) . '</p>';
                }
                echo '<hr />';
            	if ($count > 0) {
                    echo '<div class="api-video-block">';
                    echo '<p><u>Product ID <a href="/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id=' . esc_html($product_id) . '&status&sort_by&per_page&SearchProducts=Search" target="_blank">' . esc_html($product_id) .  '</a></u><br />';
                    echo 'Total Videos Returned: ' . esc_html($count) . ' - <a href="/wp-admin/admin.php?page=api-pv-data-details&keyword&product_id=' . esc_html($product_id) . '&status&sort_by&per_page&SearchProducts=Search" target="_blank">Manage Videos</a><br />';
                    echo '<strong>Product Title:</strong> <a href="' . esc_html($permalink) . '" target="_blank">' . esc_html($title) . '</a> - <a href="/wp-admin/post.php?post=' . esc_html($product_id) . '&action=edit" target="_blank"><u>Edit Page</u></a></p>';
                    // loop through video json
            		for ($i=0; $i < $count; $i++) {
                        if (isset($items[$i]->{'website_name'})) {
                            $website_name = $items[$i]->{'website_name'};
                        } else {
                            $website_name = '';
                        }
                        if (isset($items[$i]->{'title'})) {
                            $video_title = $items[$i]->{'title'};
                            $items[$i]->{'title'} = esc_attr($video_title);
                        } else {
                            $video_title = '';
                        }
                        // if result is a facebook error
                        if (strpos($video_title, 'OPEN') !== false) {
                            if ($website_name == 'facebook') {
                                $message = ($i + 1) . ') <span style="color: red;">Video not embedded.</span> This video url triggered and erro:<br />Video URL: ' . $video_url;
                                echo wp_kses_post($message);
                                continue;
                            }
                        }
                        if (isset($items[$i]->{'image'})) {
                            $video_image = $items[$i]->{'image'};
                        } else {
                            $video_image = '';
                        }
                        if (isset($items[$i]->{'video_url'})) {
                            $video_url = $items[$i]->{'video_url'};
                            if ($website_name == 'facebook') {
                                $facebook_check_invalid = facebook_url_check($video_url);
                                if ($facebook_check_invalid == true) {
                                    $message = '<p>' . ($i + 1) . ') <span style="color: red;">Video not embedded.</span> This video url triggered and error:<br />Video URL: ' . $video_url . '</p>';
                                    echo wp_kses_post($message);
                                    continue;
                                }
                            }
                        } else {
                            $video_url = '';
                        }
                        if($website_name == 'youtube') {
                            if (isset($items[$i]->{'video_url_embed'})) {
                                $video_url_embed = $items[$i]->{'video_url_embed'};
                            } else {
                                $video_url_embed = '';
                            }
                            if (isset($items[$i]->{'video_id'})) {
                                $video_id = $items[$i]->{'video_id'};
                            } else {
                                $video_id = '';
                            }
                            if ($video_url == '' && $video_url_embed !== '') {
                                $video_url = $video_url_embed;
                                $video_url = str_replace('watch?v=', 'embed/', $video_url);
                            }
                            // if we still dont have a video url
                            if ($video_url == '' && $video_id !== '') {
                                $video_url = 'https://www.youtube.com/watch?v=' . $video_id;
                            }
                        } elseif ($website_name == 'vimeo') {
                            $video_url = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $video_url);
                        } elseif ($website_name == 'dailymotion') {
                            $video_url = str_replace('https://www.dailymotion.com/video/', 'https://www.dailymotion.com/embed/video/', $video_url);
                        }
                        if ($video_url == '') {
                            echo ($i + 1) . ') <span style="color: red;">Video not embedded, no video ID returned for this video.</span><br />';
                            continue;
                        } else {
                            $video_title_lower_case = strtolower($video_title);
                            if ($identifier_title == 'sku_title' && $sku !== '') {
                            // if sku is required in the video title
                                if ($title !== '') {
                                    if (strpos($video_title, $sku) !== false) {
                                        $message = ($i + 1) . ') <span style="color: green;">Video Embedded with the Sku (' . esc_html($sku) . ') in the title</span>';
                                    } else {
                                        $message = ($i + 1) . ') <span style="color: red;">Video not embedded, product Sku (' . esc_html($sku) . ') missing from title:</span> ' . esc_html($video_title) . '<br />Video URL: ' . esc_url($video_url);
                                        $bad_url = $video_url;
                                        $video_url = '';
                                    }
                                }
                            } elseif ($identifier_title == 'brand_title' && $brand !== '') {
                                $brand_lower_case = strtolower($brand);
                                $brand_exclude_words = sanitize_text_field($plugin_options['api_pv_brand_exclude_words']);
                                if ($brand_exclude_words !== '') {
                                    $brand_lower_case = api_pv_brand_exclude_words($product_id);
                                }
                                // $brand_lower_case = rtrim(ltrim($brand_lower_case));
                                // $brand_lower_case = preg_replace('/[^\p{L}\p{N}\s]/u', '', $brand_lower_case);
                                $brand_text = ucwords($brand_lower_case);
                                if (strpos($video_title_lower_case, $brand_lower_case) !== false) {
                                    $message = ($i + 1) . ') <span style="color: green;">Video Embedded with the Brand (' . esc_html($brand_text) . ') in the title</span>';
                                } else {
                                    $message = ($i + 1) . ') <span style="color: red;">Video not embedded, product brand (' . esc_html($brand_text) . ') missing from title:</span> ' . $video_title . '<br />';
                                    $bad_url = $video_url;
                                    $video_url = '';
                                }
                            } elseif ($identifier_title == 'part_number_title' && $part_number !== '') {
                                // if part number is required in the video title
                                $part_number_lower_case = strtolower($part_number);
                                if (strpos($video_title_lower_case, $part_number_lower_case) !== false) {
                                    $message = ($i + 1) . ') <span style="color: green;">Video Embedded with the Part Number (' . esc_html($part_number) . ') in the title</span>';
                                } else {
                                    $message = ($i + 1) . ') <span style="color: red;">Video not embedded, product Part Number (' . esc_html($part_number) . ') missing from title:</span> ' . $video_title . '<br />';
                                    $bad_url = $video_url;
                                    $video_url = '';
                                }
                            } else {
                                $message = ($i + 1) . ') <span style="color: green;">Video Embedded</span>';
                            }

                            if ($video_url !== '') {
                                // if the video in loop does match plugin settings and search requirements
                                echo '<div class="api-div-found-video">';
                                    if ($video_image !== '' && $website_name !== 'facebook') {
                                        echo '<img src="' . esc_url($video_image) . '" class="api-success-img alignright" height="75px" width="auto">';
                                    }
                                    $allowed_tags = wp_kses_allowed_html('post');
                                    echo '<p>' . wp_kses($message, $allowed_tags) . '<br />';
                                    echo ' - Title: ' . esc_html($video_title) . '<br />';
                                    echo ' - Site: ' . esc_html(ucwords($website_name)) . '<br>';
                                    echo ' - Video URL: <a href="' . esc_url($video_url) . '" target="_blank">Link</a><br />';
                                    update_post_meta($product_id, 'api_pv_job_status', 'updated');
                                    $video_site_array = array('video_url' => sanitize_text_field($video_url), 'website_name' => sanitize_text_field($website_name), 'video_image' => sanitize_text_field($video_image));
                                    array_push($video_url_array, $video_site_array);
                                    $video_url_array_count = count($video_url_array);
                                echo '</div>';
                                if ($max_videos <= $video_url_array_count) {
                                    break;
                                }
                            } elseif ($video_url == '' && $count !== 0) {
                                $allowed_tags = wp_kses_allowed_html('post');
                                echo '<p>' . wp_kses($message, $allowed_tags);
                                if ($bad_url !== '') {
                                    echo ' - Video URL: <a href="' . esc_url($video_url) . '" target="_blank">Link</a><br />';
                                }
                                echo '</p>';
                                update_post_meta($product_id, 'api_pv_job_status', 'none-selected');
                			} else {
                                update_post_meta($product_id, 'api_pv_job_status', 'failed');
                            }
                        }
                        // reset the video id
                        $video_url = '';
                        $current_video++;
            		}
                    $video_url_array_count = count($video_url_array);
                    echo '<p style="text-align: center;">Total Matcing Videos: ' . esc_html($video_url_array_count) . '</p>';
                    echo '</div>';
                    // save the video json data
                    $video_url_array = json_encode($video_url_array);
                    update_post_meta($product_id, 'api_pv_video_urls', $video_url_array);
                    $result_encode = json_encode($result);
                    update_post_meta($product_id, 'api_pv_json', $result_encode);
            	} else {
                    echo '<strong>Status:</strong> No Videos Found<br />';
                    echo '<strong>Product ID:</strong> ' . esc_html($product_id) . '<br />';
                    // if the video in loop does not match plugin settings and search requirements
            		if ($website !== '') {
                        echo 'There were no videos from ' . esc_html(ucwords($website)) . ' returned for product id ' . esc_html($product_id) . '<br />';
                    } else {
                        echo 'There were no videos returned for product id ' . esc_html($product_id). ', ' . esc_html($title) . '.<br />';
                    }
                    echo 'Please consider manually imputing a query for this product in the Search Term field, or edit the Accuracy Options in the Plugin Settings.<br />';
                    update_post_meta($product_id, 'api_pv_job_status', 'failed');
                    update_post_meta($product_id, 'api_pv_json', '');
            	}
            }
            echo '</div>';
        }
        $current_time = date('Y-m-d H:i:s');
        update_post_meta($product_id, 'api_pv_last_updated', $current_time);
        api_pv_update_modified($product_id, $current_time);
    }
}

    // function to replace the featured image with video
if (!function_exists ('api_pv_replace_featured_image')) {
    function api_pv_replace_featured_image() {
        $product_id = get_the_ID();
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $video_urls_manual = api_pv_get_video_urls($product_id, 'manual');
        $video_urls_manual_count = count($video_urls_manual);
        $video_urls = api_pv_get_video_urls($product_id, 'imported');
        $video_urls_count = count($video_urls);
        $hide_video_tab = sanitize_text_field($plugin_options['api_pv_hide_video_tab']);
        $include_powered_by = sanitize_text_field($plugin_options['api_pv_include_powered_by']);
        $apigenius_user_name = sanitize_text_field($plugin_options['api_pv_apigenius_user_name']);
        // display manual videos
        if ($video_urls_manual_count > 0) {
            foreach ($video_urls_manual as $video_url => $website_name) {
                $video_image = plugin_dir_url(__DIR__) . 'assets/video-icon.png';
                echo '<div data-thumb="' . esc_url($video_image) . '" class="woocommerce-product-gallery__image" >';
                    echo '<div id="api-video-div" class="api-video-div api-video-div-imported">';
                        api_pv_display_single_video($video_url, $website_name);
                    echo '</div>';
                    if ($include_powered_by == '1' && $hide_video_tab !== '1') {
                        echo '<p class="apigenius-powered-by" style="font-size: 14px;color: #33363b;">Powered By <a style="color: #33363b;" href="https://www.apigenius.io/software/product-videos-for-woocommerce/?utm_source=' . $apigenius_user_name . '" target="_blank"><u>ApiGenius</u></a></p>';
                    }
                echo '</div>';
            }
        }
        if ($video_urls_count > 0) {
            foreach ($video_urls as $video_array) {
                if (isset($video_array['video_url'])) {
                    $video_url = $video_array['video_url'];
                } else {
                    $video_url = '';
                }
                if (isset($video_array['website_name'])) {
                    $website_name = $video_array['website_name'];
                } else {
                    $website_name = '';
                }
                if (isset($video_array['video_image']) && $website_name == 'youtube') {
                    $video_image = $video_array['video_image'];
                } else {
                    $video_image = plugin_dir_url(__DIR__) . 'assets/video-icon.png';
                }
                echo '<div data-thumb="' . esc_url($video_image) . '" class="woocommerce-product-gallery__image" >';
                    echo '<div id="api-video-div" class="api-video-div api-video-div-imported">';
                        api_pv_display_single_video($video_url, $website_name);
                    echo '</div>';
                    if ($include_powered_by == '1' && $hide_video_tab == '1') {
                        echo '<p class="apigenius-powered-by" style="font-size: 14px;color: #33363b;">Powered By <a style="color: #33363b;" href="https://www.apigenius.io/software/product-videos-for-woocommerce/?utm_source=' . $apigenius_user_name . '" target="_blank"><u>ApiGenius</u></a></p>';
                    }
                echo '</div>';
            }
        }
    }
    function api_pv_replace_featured_image_check() {
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $featured_video_option = sanitize_text_field($plugin_options['api_pv_featured_video']);
        $video_width = sanitize_text_field($plugin_options['api_pv_video_width']);
        if ($featured_video_option == 1) {
            add_action('woocommerce_product_thumbnails', 'api_pv_replace_featured_image', 10, 0);
        }
        // styles for the product videos
        ?>
            <!-- Load Facebook SDK for JavaScript -->
            <div id="fb-root"></div>
            <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
            <style>
                .api-video-div {
                    margin: 25px auto;
                    width: <?php echo $video_width; ?>px;
                    max-width: 100%;
                    padding: 10px 10px 5px 10px;
                    background: #f8f8f8;
                    border-radius: 5px;
                    border: 1px solid #d8d8d8;
                }
                .api-video-div-facebook iframe {
                    left:0;
                    top:0;
                    height:100%;
                    width:100%;
                    position:absolute;
                }
            </style>
        <?php
    }
    add_filter('woocommerce_before_single_product', 'api_pv_replace_featured_image_check');
}

    // create the video for the product page
if (!function_exists ('api_pv_video_tab')) {
    add_filter('woocommerce_product_tabs', 'api_pv_video_tab');
    function api_pv_video_tab($tabs) {
        // get the tab information from settings
        $product_id = get_the_ID();
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $hide_video_tab = sanitize_text_field($plugin_options['api_pv_hide_video_tab']);
        $tab_name = sanitize_text_field($plugin_options['api_pv_tab_name']);
        if ($tab_name == '') {
            $tab_name = 'Videos';
        }
        $tab_priority = sanitize_text_field($plugin_options['api_pv_tab_priority']);
        if ($tab_priority == '') {
            $tab_priority = 30;
        }
    	$tabs['api_pv_video_tab'] = array(
    		'title' 	=> __($tab_name, 'woocommerce'),
    		'priority' 	=> $tab_priority,
    		'callback' 	=> 'api_pv_video_tab_content'
    	);
        // get the currently displayed videos
        $video_urls_manual = api_pv_get_video_urls($product_id, 'manual');
        $video_urls_manual_count = count($video_urls_manual);
        $video_urls = api_pv_get_video_urls($product_id, 'imported');
        $video_urls_count = count($video_urls);
        $total_videos = $video_urls_manual_count + $video_urls_count;
        if (is_array($video_urls) || is_array($video_urls_manual)) {
            if (!empty($video_urls) || !empty($video_urls_manual)) {
                if ($hide_video_tab !== '1') {
                    return $tabs;
                }
            }
        }
    }
}

    // display the content of the video tab
if (!function_exists ('api_pv_video_tab_content')) {
    function api_pv_video_tab_content() {
        $product_id = get_the_ID();
        // get the plugin options
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $featured_video_option = sanitize_text_field($plugin_options['api_pv_featured_video']);
        $hide_video_tab = sanitize_text_field($plugin_options['api_pv_hide_video_tab']);
        $include_powered_by = sanitize_text_field($plugin_options['api_pv_include_powered_by']);
        $apigenius_user_name = sanitize_text_field($plugin_options['api_pv_apigenius_user_name']);
        $user = wp_get_current_user();
        $allowed_roles = array('manage_woocommerce', 'administrator', 'author');
        // display the edit videos link if they are a Shop Manager or higher user role
        if(array_intersect($allowed_roles, $user->roles)) {
            ?>
                <a class="button" style="margin-bottom: 10px;" href="/wp-admin/admin.php?page=api-pv-data-details&keyword&product_id=<?php echo $product_id; ?>&status&sort_by&per_page&SearchProducts=Search" target="_blank">Edit Videos</a>
                <p style="font-size: 10px;margin: 0;line-height: 10px;">* Only user roles of Shop Manage<br />and hire can see this button.</p>
            <?php
        }

        // display the manually entered videos
        api_pv_display_videos($product_id, 'manual');
        api_pv_display_videos($product_id, 'imported');
        if ($include_powered_by == '1') {
            echo '<p class="apigenius-powered-by" style="font-size: 14px;color: #33363b;float: right;">Powered By <a style="color: #33363b;" href="https://www.apigenius.io/software/product-videos-for-woocommerce/?utm_source=' . $apigenius_user_name . '" target="_blank"><u>ApiGenius</u></a></p>';
        }
    }
}

    // this function queries and displays the totals for all video updates to products, functionality is seen when the Get Update Details button is clicked
if (!function_exists ('api_pv_get_update_totals')) {
    function api_pv_get_update_totals() {
        // Base query args
        $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $query_args = array(
            'post_type'			          =>	'product',
            'paged'                       =>    $paged,
            'post_status'                 =>    'publish',
            'posts_per_page'              =>    1
       );
        $status_array = array(
            'all',
            'updated',
            'never',
            'none_selected',
            'skipped',
            'failed'
       );
        // tool tip
        echo 'Learn More About Statuses';
        echo '<div class="tooltip"><span class="api-about-symbol">?</span>
            <span class="tooltiptext  tooltip-table">
                <u>Update Statuses</u><br />
                <strong>Successfully Updated:</strong> Videos were found, met your settings criteria and are embeded on your product page.<br />
                <strong>Never Updated:</strong> A video search has never been attempted.<br />
                <strong>No Matching:</strong> Videos were found, but did not meet the plugin settings criteria.<br />
                <strong>Skipped:</strong> Either the product was skipped because of one of your plugin setting.<br />
                <strong>No Videos Found:</strong> There were no videos found for this product.<br />
            </span>
        </div>';
        echo '<table width="100%" class="api-update-report-table">';
        echo '<tr>';
        // loop through each status and get totals
        foreach ($status_array as $status) {
            if ($status !== 'all') {
                if($status == 'updated') {
                    $text = 'Successfully Updated';
                    $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status=updated&stock_status&sort_by&per_page&SearchProducts=Search';
                    $status_args = array(
                        'meta_key' => 'api_pv_job_status',
                        'meta_value' => 'updated',
                        'meta_compare' => '=',
                   );
                } elseif($status == 'never') {
                    $text = 'Never Updated';
                    $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status=never&stock_status&sort_by&per_page&SearchProducts=Search';
                    $status_args = array(
                        'meta_key' => 'api_pv_job_status',
                        'meta_compare' => 'NOT EXISTS',
                   );
                } elseif($status == 'none_selected') {
                    $text = 'No Matching';
                    $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status=none-selected&stock_status&sort_by=modified&per_page&SearchProducts=Search';
                    $status_args = array(
                        'meta_key' => 'api_pv_job_status',
                        'meta_value' => 'none-selected',
                        'meta_compare' => '=',
                   );
                } elseif($status == 'skipped') {
                    $text = 'Skipped';
                    $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status=skipped&stock_status&sort_by&per_page&SearchProducts=Search';
                    $status_args = array(
                        'meta_key' => 'api_pv_job_status',
                        'meta_value' => 'skipped',
                        'meta_compare' => '=',
                   );
                } elseif($status == 'failed') {
                    $text = 'No videos found.';
                    $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status=failed&stock_status&sort_by&per_page&SearchProducts=Search';
                    $status_args = array(
                        'meta_key' => 'api_pv_job_status',
                        'meta_value' => 'failed',
                        'meta_compare' => '=',
                   );
                }
                $query_args = array_merge($query_args, $status_args);
            } elseif ($status == 'all') {
                $text = 'All Products';
                $link = '/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status&stock_status&sort_by=modified&per_page&SearchProducts=Search';
            }
            $products = new WP_Query($query_args);
            $total_products = $products->found_posts;
            echo '<td style="text-align: center;">' . esc_html($text) . '<br /><a href="' . esc_url($link) . '" target="_blank">' . esc_html($total_products) . '</a></td>';
        }
        echo '</tr>
        </table>';
        echo '<span style="text-align: center;">* Click the numbered link to filter those products.</span><br />';
    }
}

    // function to update the last modified date
if (!function_exists ('api_pv_modified_data')) {
    function api_pv_modified_data($product_id) {
        // update the last updat
        $current_time = date('Y-m-d H:i:s');
        update_post_meta($product_id, 'api_pv_last_updated', $current_time);
        $product_update = array(
            'ID' => $product_id,
            'post_modified_gmt' => $current_time,
       );
        wp_update_post($product_update);
    }
}
