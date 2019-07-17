<?php

    // This file contains the hooks that can be used to modify the functionality of the plugin

    // If this file is called directly, abort.
    if (! defined('WPINC')) {
    	die;
    }

    // checks to make sure it is a playable Facebook url
if (!function_exists ('api_pv_convert_url_play')) {
    function facebook_url_check($url) {
        $end = '/videos/';
        return (strpos($url, $end, strlen($url) - strlen($end)) !== false);
    }
}

    // function to convert play urls to embed urls
if (!function_exists ('api_pv_convert_url_play')) {
    function api_pv_convert_url_play($video_url, $website_name) {
        if ($website_name == 'youtube') {
            $video_url = str_replace('embed/', 'watch?v=', $video_url);
            $video_url = str_replace('youtu.be/', 'youtube.com/watch?v=', $video_url);
        } elseif ($website_name == 'vimeo') {
            $video_url = str_replace('player.vimeo.com/video/', 'vimeo.com/', $video_url);
        } elseif ($website_name == 'dailymotion') {
            $video_url = str_replace('dailymotion.com/embed/video/', 'dailymotion.com/video/', $video_url);
        }
        return sanitize_text_field($video_url);
    }
}

// function to convert play urls to embed urls
if (!function_exists ('api_pv_convert_url_embed')) {
    function api_pv_convert_url_embed($video_url, $website_name) {
        if ($website_name == 'youtube') {
            if (strpos($video_url, '&') !== false) {
                $video_url = substr($video_url, 0, strpos($video_url, '&'));
            }
            $video_url = str_replace('watch?v=', 'embed/', $video_url);
            $video_url = str_replace('https://youtu.be/', 'https://www.youtube.com/embed/', $video_url);
        } elseif ($website_name == 'vimeo') {
            $video_url = str_replace('https://vimeo.com/', 'https://player.vimeo.com/video/', $video_url);
        } elseif ($website_name == 'dailymotion') {
            $video_url = str_replace('http://www.dailymotion.com/video/', 'https://www.dailymotion.com/embed/video/', $video_url);
            $video_url = str_replace('https://www.dailymotion.com/video/', 'https://www.dailymotion.com/embed/video/', $video_url);
        }
        return sanitize_text_field($video_url);
    }
}

    // function to check if a video url is a saved one in plugin custom field
if (!function_exists ('api_pv_is_saved_video')) {
    function api_pv_is_saved_video($product_id, $video_url, $website_name) {
        $video_urls = api_pv_get_video_urls($product_id, 'imported');
        $is_saved_video = false;
        foreach ($video_urls as $saved_video_array) {
            $saved_video = $saved_video_array['video_url'];
            $video_url = api_pv_convert_url_embed($video_url, $website_name);
            $saved_video = api_pv_convert_url_embed($saved_video, $website_name);
            if ($saved_video == $video_url) {
                $is_saved_video = true;
            }
        }
        return sanitize_text_field($is_saved_video);
    }
}

// update the last modified date
if (!function_exists ('api_pv_status_text')) {
    function api_pv_status_text($status) {
        if($status == '') {
            $status_text = 'Never Updated';
        } elseif($status == 'updated') {
            $status_text = 'Updated';
        } elseif($status == 'skipped') {
            $status_text = 'Skipped';
        } elseif($status == 'none-selected') {
            $status_text = 'No Matching Videos';
        } elseif($status == 'failed') {
            $status_text = 'No Videos Found';
        } else {
            $status_text = 'Never Updated';
        }
        return $status_text;
    }
}

// update the last modified date
if (!function_exists ('api_pv_update_modified')) {
    function api_pv_update_modified($product_id, $current_time) {
        $product_update = array(
            'ID' => $product_id,
            'post_modified_gmt' => $current_time,
        );
        wp_update_post($product_update);
    }
}

// function to get a products brand
if (!function_exists ('api_pv_get_identifier')) {
    function api_pv_get_identifier($product_id, $identifier_type) {
        $product = wc_get_product($product_id);
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $option_slug = 'api_pv_' . $identifier_type . '_attribute';
        $identifier_slug = sanitize_text_field($plugin_options[$option_slug]);
        if ($identifier_slug !== '') {
            $identifier = $product->get_attribute('pa_' . $identifier_slug);
            if ($identifier == '') {
                // if there was no identifier returned
                $identifier = 'none';
            }
        } else {
            $identifier = 'Please set Attributes in Plugin Options.';
        }
        return sanitize_text_field($identifier);
    }
}

if (!function_exists ('api_pv_get_video_urls')) {
    function api_pv_get_video_urls($product_id, $video_type) {
        if ($video_type == 'manual') {
            $custom_field = 'api_pv_video_urls_manual';
        } elseif ($video_type == 'imported') {
            $custom_field = 'api_pv_video_urls';
        }
        // get and display the imported videos
        $video_urls = get_post_meta($product_id, $custom_field, true);
        if ($video_urls !== '') {
            if ( ! is_wp_error($video_urls)) {
                $video_urls = json_decode($video_urls, true);
            } else {
                $video_urls  = [];
            }
        } else {
            $video_urls = [];
        }
        return $video_urls;
    }
}

// functon used to display a single, manually entered video
if (!function_exists ('api_pv_display_single_video_manual')) {
    function api_pv_display_single_video($video_url, $website_name) {
        $video_url = api_pv_convert_url_embed($video_url, $website_name);
        if ($video_url !== '' && $website_name == 'youtube') {
            ?>
                <iframe width="540px" height="320px" src="<?php echo esc_url($video_url); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php
        } elseif ($video_url !== '' && $website_name == 'facebook') {
            ?>
                <div class="fb-video" data-href="<?php echo esc_url($video_url); ?>" data-width="540" data-show-text="false"></div>
            <?php
        } elseif ($video_url !== '' && $website_name == 'vimeo') {
            ?>
            <iframe src="<?php echo esc_url($video_url); ?>" width="540" height="320" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
            <?php
        } elseif ($video_url !== '' && $website_name == 'dailymotion') {
            ?>
                <iframe frameborder="0" width="540" height="320" src="<?php echo $video_url; ?>" allowfullscreen allow="autoplay"></iframe>
            <?php
        }
    }
}

// function to display manually entered and imported videos - used in product tabs
if (!function_exists ('api_pv_display_videos')) {
    function api_pv_display_videos($product_id, $video_type) {
        // plugin options
        $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
        $video_width = sanitize_text_field($plugin_options['api_pv_video_width']);
        $video_width = preg_replace('/[^0-9]/', '', $video_width );
        if (! $video_width) {
            $video_width = 560;
        }
        $video_height = sanitize_text_field($plugin_options['api_pv_video_height']);
        $video_height = preg_replace('/[^0-9]/', '', $video_height );
        if (! $video_height) {
            $video_height = 560;
        }
        // get the specified video urls
        if ($video_type == 'manual') {
            $video_urls = api_pv_get_video_urls($product_id, 'manual');
            $video_urls_count = count($video_urls);
            // display manual videos
            if ($video_urls_count > 0) {
                $i = 1;
                foreach ($video_urls as $video_url => $website_name) {
                    echo '<div id="api-video-manual-div-' . esc_html($i) . '" class="api-video-div api-video-div-manual">';
                        api_pv_display_single_video($video_url, $website_name);
                    echo '</div>';
                    $i++;
                }
            }
        } elseif ($video_type == 'imported') {
            $video_urls = api_pv_get_video_urls($product_id, 'imported');
            $video_urls_count = count($video_urls);
            if ($video_urls_count > 0) {
                $i = 1;
                // get the manually entered videos and display them
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
                    echo '<div id="api-video-imported-div-' . esc_html($i) . '" class="api-video-div api-video-div-imported">';
                        api_pv_display_single_video($video_url, $website_name);
                    echo '</div>';
                    $i++;
                }
            }
        }
    }
}
