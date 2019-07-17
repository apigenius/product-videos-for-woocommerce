<?php

    // If this file is called directly, abort.
    if (! defined('WPINC')) {
       die;
    }

?>

    <h1 style="text-align: center;"><?php echo esc_html(get_admin_page_title()); ?></h1>

<style>
    /* css for form area */
    .api-form, .api-div-admin-notice {
        padding: 10px;
        border: 1px solid #d8d8d8;
        background: #fff;
        margin: auto;
        display: block;
        width: 625px;
        max-width: 100%;
    }
    .api-div-admin-notice {
        width: 1000px;
    }
    .api-form-action {
        width: 450px;
        max-width: 100%;
    }
    .api-button-black {
        background: #33363B !important;
        color: #fff !important;
    }
    .api-button-refresh {
        float: right;
    }
    /* css for product table */
    .api-table-dashboard-forms, .api-dashboard-table, .api-sort-table {
        width: 90%;
        max-width: 100%;
        margin: 25px auto;
    }
    .api-dashboard-table {
        width: 1400px;
        max-width: 100%;
        margin: 25px auto;
        background: #fff;
        border: 1px solid #d8d8d8;
    }
    .api-dashboard-table th, .api-dashboard-table td {
        border: 1px solid #d8d8d8 !important;
        padding: 5px 15px;
    }
    .api-select-all {
        margin: 10px 0 0 5px !important;
        display: inline-block;
    }
    .div-navigation {
        margin: auto !important;
        display: block;
        width: 1200px;
        max-width: 100%;
    }
    .div-navigation a {
        padding: 10px;
        border: 1px solid #d8d8d8;
        background: #fff;
        margin: auto 10px;
    }
    .api-total-products {
        padding: 10px;
        border: 1px solid #d8d8d8;
        background: #fff;
        border-radius: 5px;
        width: 125px;
        text-align: center;
    }
    .tooltip {
        position: relative;
        display: inline-block;
    }
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 250px;
        background-color: #f8f8f8;
        border: 1px solid #d8d8d8;
        color: #33363B;
        padding: 10px;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
    }
    .tooltip .tooltiptext-end {
        width: 150px;
    }
    .tooltip-table {
        width: 500px !important;
    }
    .tooltip:hover .tooltiptext {
        visibility: visible;
    }
    .api-about-symbol {
        border-radius: 50%;
        padding: 5px;
        margin-right: 15px;
        border: 1px solid #d8d8d8;
        background: #f8f8f8;
    }
    .api-video-block {
        border: 1px solid #d8d8d8;
        padding: 20px;
        margin: 10px auto;
        background: #fff;
        height: auto;
    }
    .api-table-image {
        border: 1px solid #d8d8d8;
        padding: 5px;
        background: #f8f8f8;
        max-height: 200px;
        width: 100px;
        height: auto;
    }
    /* css for the total updates report table */
    .api-update-stats-div {
        width: 90%;
        max-width: 1650px;
        margin: 25px auto;
    }
    .api-update-report-table {
        margin: 25px auto;
    }
    .api-update-report-table td {
        border: 1px solid #d8d8d8;
        background: #fff;
        padding: 10px;
    }
    /* css for update information */
    .api-div-found-video {
        height: 95px;
        border-top: 1px solid #d8d8d8;
        border-bottom: 1px solid #d8d8d8;
    }
    .api-success-img {
        margin: 5px;
        border: 1px solid #d8d8d8;
        padding: 5px;
        background: #f8f8f8;
        width: auto;
        height: 70px;
    }
</style>
    <a name="top"></a>
    <div class='wrap'>
        <table class="api-table-dashboard-forms">
            <tr>
                <td width="5%">
                    <p><a class="button api-button-black" href="/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id&status&stock_status&sort_by=modified&per_page&SearchProducts=Search">Recently Updated</a></p>
                    <!-- update status form -->
                    <form class="api-pv-form api-pv-form-button api-pv-get-stats" action="" method="post">
                        <p><input class="button api-button-black" name="get_update_stats" type="submit" value="Get Update Details"></p>
                        <?php wp_nonce_field('action_update_stats', 'nonce_update_stats', false); ?>
                    </form>
                    <?php
                        if (isset($_POST['get_update_stats'])) {
                            if (isset($_POST['nonce_update_stats'])) {
                                $nonce_update_stats = sanitize_text_field($_POST['nonce_update_stats']);
                            } else {
                                $nonce_update_stats = false;
                            }
                            if (!wp_verify_nonce($nonce_update_stats, 'action_update_stats')) {
                                wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
                            }
                        }
                    ?>
                </td>
                <td width="5%">
                </td>
                <td width="55%">
                    <h4 style="text-align: center;">Search Products</h4>
                    <!-- search products form -->
                    <form class="api-form api-form-search" action="/wp-admin/admin.php" method="get">
                        <input type="hidden" name="page" value="api-pv-dashboard" />
                        <input value="<?php if(isset($_GET['keyword'])) { $keyword = sanitize_text_field($_GET['keyword']); echo esc_html($keyword); } else { $keyword = ''; } ?>" type="text" name="keyword" placeholder="Keyword">
                        <input value="<?php if(isset($_GET['product_id'])) { $product_id_search = sanitize_text_field($_GET['product_id']); echo esc_html($product_id_search); } else { $product_id_search = ''; } ?>" type="text" name="product_id" placeholder="Product ID">
                        <select name="status">
                            <option value="<?php if(isset($_GET['status'])) { echo $status = sanitize_text_field($_GET['status']); } else { $status = ''; } ?>">
                                <?php
                                    if ($status == 'updated') {
                                        echo 'Updated';
                                    } elseif ($status == 'skipped') {
                                        echo 'Skipped';
                                    } elseif ($status == 'never') {
                                        echo 'Never Updated';
                                    } elseif ($status == 'none-selected') {
                                        echo 'No Matching';
                                    } elseif ($status == 'failed') {
                                        echo 'None Found';
                                    } else {
                                        echo 'Update Status';
                                    }
                                ?>
                            </option>
                            <option value="updated">Updated</option>
                            <option value="never">Never Updated</option>
                            <option value="skipped">Skipped</option>
                            <option value="none-selected">No Matching</option>
                            <option value="failed">None Found</option>
                        </select>
                        <select name="stock_status">
                            <option value="<?php if(isset($_GET['stock_status'])) { echo $stock_status = sanitize_text_field($_GET['stock_status']); } else { $stock_status = ''; } ?>">
                                <?php
                                    if ($stock_status == 'instock') {
                                        echo 'In Stock';
                                    } elseif ($stock_status == 'outofstock') {
                                        echo 'Out of Stock';
                                    } else {
                                        echo 'Stock Status';
                                    }
                                ?>
                            </option>
                            <option value="instock">In Stock</option>
                            <option value="outofstock">Out of Stock</option>
                        </select>
                        <br />
                        <select class="api-select-modified" name="sort_by">
                            <option value="<?php if(isset($_GET['sort_by'])) { echo $sort_by = sanitize_text_field($_GET['sort_by']); } else { $sort_by = ''; } ?>">
                                <?php
                                    if($sort_by !== '') {
                                        if($sort_by == 'modified') {
                                            echo 'Modified';
                                        } elseif($sort_by == 'id') {
                                            echo 'ID';
                                        }
                                    } else {
                                        echo 'Sort By';
                                    }
                                ?>
                            </option>
                            <option value="modified">Modified</option>
                            <option value="id">ID</option>
                        </select>
                        <select name="per_page">
                            <option value="<?php if(isset($_GET['per_page'])) { echo $per_page = sanitize_text_field($_GET['per_page']); } else { $per_page = 10; } ?>">
                                <?php
                                    if($per_page !== '') {
                                        echo esc_html($per_page);
                                    } else {
                                        echo 'Per Page';
                                    }
                                ?>
                            </option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <input class="button api-button-black" name="SearchProducts" type="submit" value="Search">
                        <a class="button" href="/wp-admin/admin.php?page=api-pv-dashboard">Clear Search</a>
                        <?php wp_nonce_field('action_search_products', 'nonce_search_products', false); ?>
                    </form>
                    <?php
                        if (isset($_GET['nonce_search_products'])) {
                            $nonce_search_products = sanitize_text_field($_GET['nonce_search_products']);
                        } else {
                            $nonce_search_products = wp_create_nonce( 'action_search_products' );
                        }
                        if (!wp_verify_nonce($nonce_search_products, 'action_search_products')) {
                            wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
                        }
                    ?>
                </td>
                <td width="5%"></td>
                <td width="30%">
                    <h4 style="text-align: center;">Update Products</h4>
                    <!-- update products form -->
                    <form class="api-form api-form-action" name="api-form-action" method="POST" action="">
                        <div class="div-action-form">
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
                                    By default the plugin will search all video websites but you can specify a single site if desired.
                                </span>
                            </div><br />
                            <label><input class="existing-data" type="checkbox" name="existing_data" value="existing_data">Use Existing Data</label>
                            <hr />
                            <input class="button api-button api-button-black" style="margin-top:5px;"  name="UpdateAction" type="submit" value="Get Videos">
                            <lable class="api-select-all"><input type="checkbox" id="selectall" onClick="selectAll(this)" /> Select All Products On Page</lable>
                            <input class="button api-button api-button-refresh" style="margin-top:5px;"  name="RefreshPage" type="submit" value="Refresh">
                            <?php wp_nonce_field('action_update_products', 'nonce_update_products', false); ?>
                        </div>
                </td>
            </tr>
        </table>
    </div>

    <?php
    if (isset($_POST['UpdateAction'])) {
        if (isset($_POST['UpdateAction'])) {
            $nonce_update_products = sanitize_text_field($_POST['nonce_update_products']);
        } else {
            $nonce_update_products = false;
        }
        if (!wp_verify_nonce($nonce_update_products, 'action_update_products')) {
            wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
        }
    }
    ?>

    <?php
        // handle reshesh buttom click
        if (isset($_POST['RefreshPage'])) {
            header("Refresh:0");
        }
    ?>

    <div class="api-update-stats-div">
        <?php
            if (isset($_POST['get_update_stats'])) {
                api_pv_get_update_totals();
            }
        ?>
    </div>

    <!-- Check box javascript -->
    <script language="JavaScript">
        function selectAll(source) {
            checkboxes = document.getElementsByClassName('check-box');
            for(var i in checkboxes)
                checkboxes[i].checked = source.checked;
        }
    </script>

    <a style="padding:10px;background:#fff;border:1px solid #d8d8d8;border-radius: 5px;text-decoration:none;float:right;margin-right:10px;" href="#bottom">Bottom of Page</a>
    <!-- products table -->
    <table style="font-size: 12px;" class="api-dashboard-table widefat">
        <thead>
            <th><input type="checkbox" id="selectall" onClick="selectAll(this)" /></th>
            <th>
                Statuses;
                <div class="tooltip"><span class="api-about-symbol">?</span>
                    <span class="tooltiptext  tooltip-table">
                        <u>Update Statuses</u><br /><br />
                        <strong>Updated:</strong> Videos were found, met your settings criteria and are embeded on your product page.<br /><br />
                        <strong>Never Updated:</strong> A video search has never been attempted.<br /><br />
                        <strong>No Matching:</strong> Videos were found, but did not meet the plugin settings criteria.<br /><br />
                        <strong>Skipped:</strong> Either the product was skipped because of one of your plugin setting.<br /><br />
                        <strong>No Videos Found:</strong> There were no videos found for this product.<br />
                    </span>
                </div>
            </th>
            <th>Videos</th>
            <th>Product Overview</th>
        </thead>

    <?php

    // Page the results
    $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

    // Base query args
    $query_args = array(
        'post_type'			          =>	'product',
        'paged'                       =>    $paged,
        'post_status'                 =>    'publish',
        'posts_per_page'              =>    $per_page
    );

    if ($product_id_search !== '') {
        $product_id_args = array(
            'p' => $product_id_search
        );
        $query_args = array_merge($query_args, $product_id_args);
    }

    if ($keyword !== '') {
        $product_id_args = array(
            's' => $keyword
      );
       $query_args = array_merge($query_args, $product_id_args);
    }

    // If a keyword was provided, include in query
    $sort_by_args = array(
        'orderby' => $sort_by,
        'order' => 'desc',
    );
    $query_args = array_merge($query_args, $sort_by_args);

    $meta_args_all = [];

    // search by update status
    if($status !== '') {
        if($status == 'updated') {
            $status_args = array(
                'key' => 'api_pv_job_status',
                'value' => 'updated',
                'compare' => '=',
            );
        } elseif($status == 'never') {
            $status_args = array(
                'key' => 'api_pv_job_status',
                'compare' => 'NOT EXISTS',
          );
       } elseif($status == 'none-selected') {
            $status_args = array(
               'key' => 'api_pv_job_status',
               'value' => 'none-selected',
               'compare' => '=',
          );
        } elseif($status == 'failed') {
            $status_args = array(
                'key' => 'api_pv_job_status',
                'value' => 'failed',
                'compare' => '=',
          );
        } elseif($status == 'skipped') {
            $status_args = array(
                'key' => 'api_pv_job_status',
                'value' => 'skipped',
                'compare' => '=',
          );
        }
        array_push($meta_args_all, $status_args);
    }

    if ($stock_status !== '') {
        $stock_status_args = array(
            'key' => '_stock_status',
            'value' => $stock_status
        );
        array_push($meta_args_all, $stock_status_args);
    }

    // insert the relation if needed
    if (is_array($meta_args_all)) {
        $args_count = count($meta_args_all);
        if ($args_count > 2) {
            $meta_args_all = array('relation' => 'AND');
        } elseif ($args_count > 0) {
            $meta_args = array(
                'meta_query' => $meta_args_all
            );
            $query_args = array_merge($query_args, $meta_args);
        }
    }

    $the_query = new WP_Query($query_args);

    // plugin options
    $plugin_options = wp_parse_args(get_option('api_pv_options'), api_pv_default_options());
    $identifier_title = sanitize_text_field($plugin_options['api_pv_identifier_title']);
    $if_not_available = sanitize_text_field($plugin_options['api_pv_if_not_available']);
    $identifier_error = false;
    if($the_query->have_POSTs()) {
        // total products
        $total_products = $the_query->found_posts;
        echo '<h4 class="api-total-products">Total Products: ' . $total_products . '</h4>';
        // product count while loop
        $i = 1;
        while ($the_query->have_POSTs()) {
            $the_query->the_POST();
            global $product;
            $product_id = $product->get_id();
            // if products have been submitted, process them during the loop
            if(isset($_POST['UpdateAction'])) {
                $products_checked = 'update_product_' . $product_id;
                if(isset($_POST[$products_checked])) {
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
                    echo '</div>';
                }
            }

            // product status & stock
            $status = get_post_meta($product_id, 'api_pv_job_status', true);
            $status_text = api_pv_status_text($status);
            $stock = get_post_meta($product_id, '_stock', true);
            // get the manually entered videos
            $video_urls_manual = api_pv_get_video_urls($product_id, 'manual');
            $video_urls_manual_count = count($video_urls_manual);
            // get the embedded videos
            $video_urls = api_pv_get_video_urls($product_id, 'imported');
            $video_urls_count = count($video_urls);
            $import_query = get_post_meta($product_id, 'api_pv_search_query', true);

            $video_count = $video_urls_count + $video_urls_manual_count;

            $product_image_url = get_the_post_thumbnail_url($product_id, $size = 'post-thumbnail');
            if($product_image_url == '' && function_exists('fifu_action_links')) {
                $product_image_url= get_post_meta($product_id, 'fifu_image_url');
            }
            $last_updated = get_post_meta($product_id, 'api_pv_last_updated', true);

            // get product $identifiers
            $sku = get_post_meta($product_id, '_sku', true);

            // display the products table
            ?>
                <tr>
                    <td><input class="check-box" type="checkbox" name="update_product_<?php echo esc_html($product_id); ?>" value="<?php echo esc_html($product_id); ?>"></td>
                    <td>
                        <?php
                            echo esc_html($status_text);
                            if(($status_text) && ($last_updated)) {
                                echo '<br />' . esc_html($last_updated);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if ($status !== '') {
                                echo 'Videos Embedded: ' . esc_html($video_count) . '<br />';
                            }
                            echo '<a href="/wp-admin/admin.php?page=api-pv-data-details&keyword&product_id=' . esc_html($product_id) . '&status&sort_by&per_page&SearchProducts=Search" target="_blank">Manage Videos</a>';
                        ?>
                    </td>
                    <td>
                        <?php
                            if ($product_image_url) {
                                echo '<img class="api-table-image alignright" src="' . esc_url($product_image_url) . '" />';
                            }
                        ?>
                        <a href="<?php echo get_permalink($product_id); ?>" target="_blank"><?php echo get_the_title($product_id); ?></a> - <a href="/wp-admin/post.php?post=<?php echo esc_html($product_id); ?>&action=edit" target="_blank"><u>Edit Page</u></a>
                        <br />
                        <strong>Product ID: </strong> <a href="/wp-admin/admin.php?page=api-pv-dashboard&keyword&product_id=<?php echo esc_html($product_id); ?>&status&sort_by&per_page&SearchProducts=Search" target="_blank"><?php echo esc_html($product_id); ?></a><br />
                        <?php
                            $regular_price = get_post_meta($product_id, '_regular_price', true);
                            $sale_price = get_post_meta($product_id, '_sale_price', true);
                            if ($sale_price !== '') {
                                echo '<strong>Sale Price</strong> $' . esc_html(round($sale_price, 2)) . '<br />';
                            }
                            if ($regular_price !== '') {
                                echo '<strong>Regular Price</strong> $' . esc_html(round($regular_price, 2)) . '<br />';
                            }
                            if ($stock !== '') {
                                echo '<strong>Stock:</strong> ' . esc_html($stock) . '<br />';
                            }
                            if ($sku !== '') {
                                echo '<strong>Sku:</strong> ' . esc_html($sku) . '<br />';
                            }
                            $brand_slug = sanitize_text_field($plugin_options['api_pv_brand_attribute']);
                            $brand = $product->get_attribute('pa_' . $brand_slug);
                            if ($brand !== '') {
                                echo '<strong>Brand:</strong> ' . esc_html($brand) . '<br />';
                            } elseif ($brand_slug == '') {
                                echo '<strong>Brand:</strong> Please set Brand Attribute in Plugin Settings.<br />';
                            }
                            $part_number_slug = sanitize_text_field($plugin_options['api_pv_part_number_attribute']);
                            $part_number = $product->get_attribute('pa_' . $part_number_slug);
                            if ($part_number !== '') {
                                echo '<strong>Part Number:</strong> ' . esc_html($part_number) . '<br />';
                            } elseif ($part_number_slug == '') {
                                echo '<strong>Part Number:</strong> Please set Part Number Attribute in Plugin Settings.<br />';
                            }
                            if ($import_query !== '') {
                                echo '<strong>Import Query:</strong> ' . esc_html($import_query) . '<br />';
                            }
                        ?>
                    </td>
                </tr>
            <?php
        }
        // increase the product while loop counter by 1
        $i++;
    }

    // Reset Query
    wp_reset_query();

    ?>

        </tr>
    </table>

    <!-- Pagination html -->
    <div class="div-navigation tablenav">
        <div class="alignleft tablenav-pages">
              <?php
                  $base = str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999)));
                  $base = htmlspecialchars_decode($base);
                  echo paginate_links(array(
                      'base'         => $base,
                      'total'        => $the_query->max_num_pages,
                      'current'      => $paged,
                      'format'       => '?page=%#%',
                      'show_all'     => false,
                      'type'         => 'plain',
                      'end_size'     => 2,
                      'mid_size'     => 1,
                      'prev_next'    => false,
                      'prev_text'    => sprintf('<i></i> %1$s', __('Newer Posts', 'text-domain')),
                      'next_text'    => sprintf('%1$s <i></i>', __('Older Posts', 'text-domain')),
                      'add_args'     => false,
                      'add_fragment' => '',
                ));
              ?>
            </nav>
        </div>
        </form>
    </div>
    <a style="padding:10px;background:#fff;border:1px solid #d8d8d8;border-radius: 5px;text-decoration:none;float:right;margin-right:10px;" href="#top">Top of Page</a>
    <a name="bottom"></a>
