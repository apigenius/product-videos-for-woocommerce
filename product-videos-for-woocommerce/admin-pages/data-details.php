<?php

    // If this file is called directly, abort.
    if (! defined('WPINC')) {
       die;
    }

    // Woocommerce global
    global $product;

    ?>
    <!-- Load Facebook SDK to display videos -->
    <div id="fb-root"></div>
    <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
        <style>
            /* Text */
            .api-center {
                text-align: center;
            }
            /* Blocks */
            .api-video-div {
                padding: 10px 25px 50px;
                border: 1px solid #d8d8d8;
                background: #f8f8f8;
                width: 560px;
                max-width: 100%;
                display: block;
                margin: 25px auto;
            }
            /* Forms */
            .api-form-div {
                padding: 10px 25px 50px;
                border: 1px solid #d8d8d8;
                background: #fff;
                width: 600px;
                max-width: 100%;
                display: block;
                margin: 25px auto;
            }
            .api-form, .api-div-admin-notice {
                padding: 10px;
                border: 1px solid #d8d8d8;
                background: #f8f8f8;
                width: 400px;
                max-width: 100%;
                display: block;
                margin: 10px auto;
            }
            .api-div-admin-notice {
                width: 800px;
                max-width: 100%;
            }
            /* Buttons */
            .button {
                padding: 0px 20px !important;
                margin: 5px 5px 5px 0 !important;
            }
            .button-black {
                background: #33363B !important;
                color: #fff !important;
            }

            /* Table */
            .api-pv-data-table td {
                vertical-align: top;
            }
            /* Product Data Block */
            .api-product-data {
                padding: 10px 25px 25px;
                border: 1px solid #d8d8d8;
                background: #fff;
                margin-top: 10px;
                width: auto;
                max-width: 600px;
                display: block;
                height: 350px;
            }
            .api-product-data h4 {
                font-size: 18px;
                line-height: 20px;
            }
            .api-product-data img {
                max-width: 50%;
                height: 200px;
                border: 1px solid #d8d8d8;
                padding: 5px;
                background: #fff;
                margin: 0 25px 25px 0;
                float: left;
            }
            .api-product-data p {
                font-size: 15px;
            }
            /* Tool Tip */
            .tooltip {
                position: relative;
                display: inline-block;
            }
            .tooltip .tooltiptext {
                visibility: hidden;
                width: 450px;
                background-color: #f8f8f8;
                border: 1px solid #d8d8d8;
                color: #33363B;
                padding: 10px;
                border-radius: 6px;
                position: absolute;
                z-index: 1;
            }
            .tooltip:hover .tooltiptext {
                visibility: visible;
            }
            .api-about-symbol {
                border-radius: 50%;
                padding: 5px;
                margin: 10px 10px 10px 0;
                border: 1px solid #d8d8d8;
                background: #f8f8f8;
            }
        </style>
        <form class="api-form" action="/wp-admin/admin.php" method="get">
            <input type="hidden" name="page" value="api-pv-data-details" />
            <input value="<?php if(isset($_GET['product_id'])) { $product_id = sanitize_text_field($_GET['product_id']); echo esc_html($product_id); } else { $product_id = ''; } ?>" type="text" name="product_id" placeholder="Product ID">
            <input class="button button-black" name="SearchProducts" type="submit" value="Search">
            <a class="button button-grey" href="/wp-admin/admin.php?page=api-pv-data-details">Clear Search</a>
            <?php wp_nonce_field('action_search_products', 'nonce_search_products', false); ?>
        </form>
    <?php
        if($product_id !== '') {
            echo '<h3 class="api-center">Search for a Product</h3>';
            echo '<p class="api-center">To view and edit the videos for a product, enter a product ID.</p>';
            echo '<p class="api-center">You can also do this by clicking on the Manage Videos link next to any product on the <a href="/wp-admin/admin.php?page=api-pv-dashboard" target="_blank">Dashboard Page</a>.</p>';
        }

        if (isset($_GET['nonce_search_products'])) {
            $nonce_search_products = sanitize_text_field($_GET['nonce_search_products']);
        } else {
            $nonce_search_products = wp_create_nonce( 'action_search_products' );
        }
        if (!wp_verify_nonce($nonce_search_products, 'action_search_products')) {
            wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
        }

        if (isset($product_id)) {
            $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

            $query_args = array(
                'post_type'			      =>	'product',
                'paged'                   =>    $paged,
                'orderby'                 =>    'ID',
                'order'                   =>    'desc',
                'posts_per_page'	      =>	1,
                'p'				          =>	$product_id,
           );
            $the_query = new WP_Query($query_args);
            if($the_query->have_POSTs()) {
                while ($the_query->have_POSTs()) {
                    $the_query->the_POST();

                    global $product;
                    $product_id = $product->get_id();

                    // get options and attributes
                    $plugin_options = get_option('api_pv_options', api_pv_default_options());
                    $brand = api_pv_get_identifier($product_id, 'brand');
                    $part_number = api_pv_get_identifier($product_id, 'part_number');

                    // plugin product info
                    $status = get_post_meta($product_id, 'api_pv_job_status', true);
                    $status_text = api_pv_status_text($status);

                    // get product details
                    $product_title = get_the_title($product_id);
                    $sku = get_post_meta($product_id, '_sku', true);
                    $product_permalink = get_permalink($product_id);
                    $product_image_url = get_the_post_thumbnail_url($product_id, $size = 'post-thumbnail');
                    $product_excerpt = get_the_excerpt($product_id);
                    $total_sales = get_post_meta($product_id, 'total_sales', true);
                    $regular_price = get_post_meta($product_id, '_regular_price', true);
                    $sale_price = get_post_meta($product_id, '_sale_price', true);

                    // get saved product video data
                    $import_query = get_post_meta($product_id, 'api_pv_search_query', true);
                    $video_urls = api_pv_get_video_urls($product_id, 'imported');
                    $video_urls_count = count($video_urls);

                    ?>
                        <table class="api-pv-data-table">
                            <tr>
                                <td width="60%">
                                    <div class="api-form-div">
                                        <h4>Manually Enter Videos</h4>
                                        <p>You can manually enter video ids you would like to use by supplying the video url and selecting which platform it is from.  You will see them on this page in the Manage Videos section.</p>
                                        <form class="api-form api-form-manual" method="post">
                                            <input name="video_urls_manual_single" placeholder="Video URL"
                                            value="<?php
                                                        if(isset($_POST['video_urls_manual_single'])) {
                                                            $video_urls_manual_single = sanitize_text_field($_POST['video_urls_manual_single']);
                                                            echo esc_url($video_urls_manual_single);
                                                        }
                                                    ?>" type="text" required></input>
                                            <select name="website_name_submit" required>
                                                <option value="<?php if(isset($_POST['website_name_submit'])) { echo $website_name_submit = sanitize_text_field($_POST['website_name_submit']); } else { $website_name_submit = ''; } ?>">
                                                    <?php
                                                        if ($website_name_submit !== '') {
                                                            echo esc_html($website_name_submit);
                                                        }
                                                    ?>
                                                </option>
                                                <option value="youtube">YouTube</option>
                                                <option value="facebook">Facebook</option>
                                                <option value="dailymotion">Daily Motion</option>
                                                <option value="vimeo">Vimeo</option>
                                            </select>
                                            <div class="tooltip"><span class="api-about-symbol">?</span>
                                                <span class="tooltiptext">
                                                    ex 1: https://www.youtube.com/watch?v=L5sWnh3EkTI<br />
                                                    ex 2: https://www.facebook.com/watch/?v=10153604913354180<br />
                                                    ex 3: https://www.facebook.com/visualmodo/videos/756216584763388/
                                                </span>
                                            </div>
                                            <input class="button button-black" name="AddVideo" type="submit" value="Add Video">
                                        </form>
                                        <br />
                                        <form name="delete_manual" method="post">
                                            <input class="button alignright" name="DeleteManual" type="submit" value="Delete Manually Entered Videos">
                                        </form>
                                    </div>
                                    <div class="api-form-div">
                                        <h4>Find Videos</h4>
                                        <form class="api-form api-form-get-videos" name="api-form-action" method="POST" action="">
                                            <input value="<?php if(isset($_POST['query'])) { $query = sanitize_text_field($_POST['query']); echo esc_html($query); } else { $query = ''; } ?>" type="text" name="query" placeholder="Search Term (optional)">
                                            <div class="tooltip"><span class="api-about-symbol">?</span>
                                                <span class="tooltiptext">
                                                    By default the plugin uses the Product Title to search for videos. If you would instead like to use a search term, enter it here.
                                                </span>
                                            </div>
                                            <select name="website">
                                                <option value="<?php if(isset($_POST['website'])) { echo $website = sanitize_text_field($_POST['website']); } else { $website = ''; } ?>">
                                                    <?php
                                                        if($website !== '') {
                                                            echo esc_html(ucwords($website));
                                                        } else {
                                                            echo 'Website (optional)';
                                                        }
                                                    ?>
                                                </option>
                                                <option value="youtube">YouTube</option>
                                                <option value="facebook">Facebook</option>
                                                <option value="dailymotion">Daily Motion</option>
                                                <option value="vimeo">Vimeo</option>
                                            </select>
                                            <div class="tooltip"><span class="api-about-symbol">?</span>
                                                <span class="tooltiptext tooltiptext-end">
                                                    By default the plugin will search all video websites but you can specify a single site if desired. This will override the plugin settings.
                                                </span>
                                            </div><br />
                                            <label><input class="existing-data" type="checkbox" name="existing_data" value="existing_data">Use Existing Data</label>
                                            <br />
                                            <input class="button button-black"  name="GetVideos" type="submit" value="Get Videos">
                                        </form>
                                    </div>
                                    <hr />
                                    <?php
                                        if(isset($_POST['GetVideos'])) {
                                            echo '<div class="api-div-admin-notice">';
                                                $video_urls = get_post_meta($product_id, 'api_pv_json', true);
                                                $video_urls = json_decode($video_urls, true);
                                                if (is_array($video_urls)) {
                                                    $video_count = count($video_urls);
                                                } else {
                                                    $video_count = 0;
                                                }

                                                if (isset($_POST['existing_data']) && $video_count < 1) {
                                                    echo '<span style="color: red;">Error: You have selected to use existing data but this product has no existing video data. You will nedd to update it without the Use Existing Data box checked.<br />';
                                                } elseif (isset($_POST['existing_data'])) {
                                                    // use existing data
                                                    echo '<p><strong>Existing Data:</strong> True</p>';
                                                    api_pv_get_video($product_id, $query, $website, 'true');
                                                } else {
                                                    // video search
                                                    api_pv_get_video($product_id, $query, $website, 'false');
                                                }
                                            echo '<div>';
                                        }
                                    ?>
                                </td>
                                <td width="40%">
                                    <div class="api-product-data">
                                        <h4><a href="<?php echo esc_url($product_permalink); ?>" target="_blank"><?php echo esc_html($product_title); ?></a></h4>
                                        <p><a href="/wp-admin/post.php?post=<?php echo esc_html($product_id); ?>&action=edit" target="_blank"><u>Edit Page</u></a></p>
                                        <?php
                                            if ($product_image_url !== '') {
                                                echo '<img src="' . esc_url($product_image_url) . '" />';
                                            }
                                        ?>
                                        <p>
                                            <?php
                                                if ($status_text !== '') {
                                                    echo '<strong>Video Update Status:</strong> ' . esc_html(ucwords($status_text)) . '<br />';
                                                }
                                            ?>
                                            <?php
                                                if (is_array($video_urls)) {
                                                    echo '<strong>Import Query:</strong> ' . esc_html($import_query) . '<br />';
                                                }
                                            ?>

                                        </p>
                                        <p>
                                            <?php
                                                if ($sale_price !== '') {
                                                    echo '<strong>Sale Price</strong> $' . esc_html(round($sale_price, 2)) . '<br />';
                                                }
                                                if ($regular_price !== '') {
                                                    echo '<strong>Regular Price</strong> $' . esc_html(round($regular_price, 2)) . '<br />';
                                                }
                                            ?>
                                            <strong>Product ID:</strong> <?php echo esc_html($product_id); ?><br />
                                            <strong>Sku:</strong> <?php echo esc_html($sku); ?><br />
                                            <?php
                                                if ($brand !== '') {
                                                    echo '<strong>Brand:</strong> ' . esc_html($brand) . '<br />';
                                                }
                                                if ($part_number !== '') {
                                                    echo '<strong>Part Number:</strong> ' . esc_html($part_number) . '<br />';
                                                }
                                            ?>
                                        </p>
                                    </div>
                                    <div class="api-form-div">
                                        <h4>Manage Videos</h4>
                                        <p>We use less space in your data base than a PDF document. No videos are downloaded.  All are embeded.<hr /></p>
                                        <form name="api-api-form-action" method="POST" action="">
                                            <table>
                                                <tr>
                                                    <td width="20%">
                                                        <p><input class="button button-black"  name="UpdateAction" type="submit" value="Update Videos"></p>
                                                    </td>
                                                    <td width="80%">
                                                        <p><strong>Please Note:</strong> The settings on this page <u>Will</u> override your <u>Max Videos</u> plugin settings.  If you would like to manually select the videos, check the boxes of the desired videos and click the Update Videos button.  All selected videos <u>Will</u> be displayed on the products page.</p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <?php
                                                $video_site_array_manual = [];
                                                $video_urls_manual = api_pv_get_video_urls($product_id, 'manual');
                                                // display the manually entered videos
                                                echo '<h3>Manually Entered Videos</h3>';
                                                if(is_array($video_urls_manual)) {
                                                    if (!empty($video_urls_manual)) {
                                                        $e = 1;
                                                        foreach ($video_urls_manual as $video_url_manual => $website_name_manual) {
                                                            echo '<div class="api-video-div">';
                                                                echo '<input class="check-box" type="checkbox" name="save_' . $e . '_manual" value="' . $e . '" checked> - Manually Entered<br /><br />';
                                                                api_pv_display_single_video($video_url_manual, $website_name_manual);
                                                            echo '</div>';
                                                            if(isset($_POST['UpdateAction'])) {
                                                                // create the new display videos array
                                                                $videos_checked = 'save_' . $e . '_manual';
                                                                if(isset($_POST[$videos_checked])) {
                                                                    // convert the url to play video link
                                                                    $video_url_manual = api_pv_convert_url_play($video_url_manual, $website_name_manual);
                                                                    $video_site_array_manual_new = array($video_url_manual => $website_name_manual);
                                                                    $video_site_array_manual = array_merge($video_site_array_manual, $video_site_array_manual_new);
                                                                }
                                                            }
                                                            $e++;
                                                        }
                                                    } else {
                                                        echo '<p>There are no manually entered videos for this product.</p>';
                                                    }
                                                }

                                                // get the displayed imported urls
                                                $video_urls = api_pv_get_video_urls($product_id, 'imported');

                                                // get the saved video json
                                                $video_site_array = [];
                                                $result = get_post_meta($product_id, 'api_pv_json', true);
                                                if (! is_wp_error($result)) {
                                                    $result = json_decode($result);
                                                }
                                                if(isset ($result->{'items'})) {
                                                    $count = count($result->{'items'});
                                                    $items = $result->{'items'};
                                                }

                                                echo '<hr />';
                                                echo '<h3>Imported Videos</h3>';
                                                // display the imported videos
                                                if(isset($result->{'items'})) {
                                                    $count = count($result->{'items'});
                                                    if($count > 0) {
                                                        for($i = 0; $i < $count; $i++) {
                                                            if (isset($items[$i]->{'website_name'})) {
                                                                $website_name = $items[$i]->{'website_name'};
                                                            } else {
                                                                $website_name = '';
                                                            }
                                                            if (isset($items[$i]->{'video_url'})) {
                                                                $video_url = $items[$i]->{'video_url'};
                                                            } else {
                                                                continue;
                                                            }
                                                            if (isset($items[$i]->{'image'})) {
                                                                $video_image = $items[$i]->{'image'};
                                                            } else {
                                                                $video_image = '';
                                                            }
                                                            if ($website_name == 'facebook') {
                                                                $facebook_check_invalid = facebook_url_check($video_url);
                                                                if ($facebook_check_invalid == true) {
                                                                    continue;
                                                                }
                                                            }
                                                            echo '<div id="api-video-div-' . $i . '" class="api-video-div">';
                                                                // check to see if the current video url is a saved video
                                                                $is_saved_video = api_pv_is_saved_video($product_id, $video_url, $website_name);
                                                                if (is_array($video_urls)) {
                                                                    if ($is_saved_video == true) {
                                                                        echo '<input class="check-box" type="checkbox" name="save_' . esc_html($i) . '" value="' . esc_html($i) . '" checked> - Video ' . esc_html($i + 1) . '<br />';
                                                                    } else {
                                                                        echo '<input class="check-box" type="checkbox" name="save_' . esc_html($i) . '" value="' . esc_html($i) . '"> - Video ' . esc_html($i + 1) . '<br />';
                                                                    }
                                                                } else {
                                                                    echo '<input class="check-box" type="checkbox" name="save_' . esc_html($i) . '" value="' . esc_html($i) . '"> - Video ' . esc_html($i + 1) . '<br />';
                                                                }
                                                                if (isset($items[$i]->{'title'})) {
                                                                    $video_title = $items[$i]->{'title'};
                                                                    echo '<h4>' . esc_html($video_title) . '</h4>';
                                                                } else {
                                                                    echo '<h4>No Title Provided</h4>';
                                                                }
                                                                api_pv_display_single_video($video_url, $website_name);
                                                            echo '</div>';
                                                            if(isset($_POST['UpdateAction'])) {
                                                                // create the new display videos array
                                                                $videos_checked = 'save_' . $i;
                                                                if(isset($_POST[$videos_checked])) {
                                                                    // convert the url to play video link
                                                                    $video_url = api_pv_convert_url_play($video_url, $website_name);
                                                                    $video_array = array('video_url' => sanitize_text_field($video_url), 'website_name' => sanitize_text_field($website_name), 'video_image' => sanitize_text_field($video_image));
                                                                    print_r($video_array);
                                                                    array_push($video_site_array, $video_array);
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    if ($status == 'failed') {
                                                        echo '<p>There were no videos found for this product.</p>';
                                                    } else {
                                                        echo '<p>There are no imported videos for this product. You can click the Get Videos button to instantly find and embed videos.</p>';
                                                    }
                                                }

                                            ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    <?php
                }
            }

            wp_reset_query();

            // handle add video form submission
            if (isset($_POST['AddVideo'])) {
                $video_urls_manual = api_pv_get_video_urls($product_id, 'manual');
                if (!is_array($video_urls_manual)) {
                    $video_urls_manual = [];
                }
                $video_urls_manual_single = api_pv_convert_url_play($video_urls_manual_single, $website_name_submit);
                $video_urls_manual_new = array(
                    $video_urls_manual_single => $website_name_submit
                );
                // update the videos, plugin status and modified date
                $video_urls_manual = array_merge($video_urls_manual, $video_urls_manual_new);
                $video_urls_manual = json_encode($video_urls_manual);
                update_post_meta($product_id, 'api_pv_video_urls_manual', $video_urls_manual);
                update_post_meta($product_id, 'api_pv_job_status', 'updated');
                $current_time = date('Y-m-d H:i:s');
                update_post_meta($product_id, 'api_pv_last_updated', $current_time );
                api_pv_update_modified($product_id, $current_time);
                header("Refresh:0");
            }

            // update both the manual videos array and the imported videos array
            if(isset($_POST['UpdateAction'])) {
                $current_time = date('Y-m-d H:i:s');
                // update manual videos
                $video_site_array_manual = json_encode($video_site_array_manual);
                update_post_meta($product_id, 'api_pv_video_urls_manual', $video_site_array_manual);
                update_post_meta($product_id, 'api_pv_last_updated', $current_time);
                $product_update = array(
                    'ID' => $product_id,
                    'post_modified_gmt' => $current_time,
                );
                wp_update_post($product_update);
                // update imported videos
                $video_site_array = json_encode($video_site_array);
                update_post_meta($product_id, 'api_pv_video_urls', $video_site_array);
                update_post_meta($product_id, 'api_pv_last_updated', $current_time);
                $product_update = array(
                    'ID' => $product_id,
                    'post_modified_gmt' => $current_time,
                );
                wp_update_post($product_update);
                header("Refresh:0");
            }

            // handle the delete videos form submission
            if (isset($_POST['DeleteManual'])) {
                update_post_meta($product_id, 'api_pv_video_urls_manual', '');
                // update the last modified date
                $current_time = date('Y-m-d H:i:s');
                update_post_meta($product_id, 'api_pv_last_updated', $current_time);
                $product_update = array(
                    'ID' => $product_id,
                    'post_modified_gmt' => $current_time,
                );
                wp_update_post($product_update);
                header("Refresh:0");
            }
        }
