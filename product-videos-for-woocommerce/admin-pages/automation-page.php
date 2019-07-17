<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
   die;
}

?>
<!-- css styles for page -->
<style>
    .api-automation-table {
        width: 100%;
        max-width: 100%;
        margin: auto;
    }
    .api-pv-form-basic, .api-pv-kill-updates {
        padding: 20px;
        border: 1px solid #d8d8d8;
        background: #f8f8f8;
        margin: 10px auto;
        display: block;
        width: 600px;
        max-width: 100%;
    }
    .api-pv-kill-updates {
        border: none;
        background: transparent;
    }
    .api-div-admin-notice {
        padding: 10px;
        border: 1px solid #d8d8d8;
        background: #f8f8f8;
        margin-top: 10px;
        max-width: 100%;
        width: 800px;
        margin: auto;
        display: block;
    }
    .api-table h4 {
        text-align: center;
        font-size: 14px;
        font-family: 'Aldrich', sans-serif;
    }
    .api-table p {
        text-align: center;
        font-size: 15px;
        font-family: 'Aldrich', sans-serif;
    }
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
    .api-update-div {
        padding: 10px;
        border: 1px solid #d8d8d8;
        border-radius: 10px;
        background: #f8f8f8;
        min-height: 150px;
    }
    .tooltip {
        position: relative;
        display: inline;
        border: 1px solid #d8d8d8;
        border-radius: 50px;
        padding: 5px 2.5px 5px 5px;
        margin: 5px;
        background: #fff;
    }
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 500px;
        background-color: #fff;
        border: 1px solid #d8d8d8;
        color: #33363B;
        padding: 10px;
        border-radius: 6px;

        /* Position the tooltip text - see examples below! */
        position: absolute;
        z-index: 1;
    }
    .tooltip:hover .tooltiptext {
        visibility: visible;
    }
</style>
<!-- form that creates the automation -->
<div class="api-pv-settings-div">
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form class="api-pv-form api-pv-form-basic" action="" method="post">
            <br />
            <select id="api-select" class="api-search-input api-search-form-select" name="automation_time" required>
                <option value="<?php
                    if(isset($_POST['automation_time'])) {
                        $automation_time = sanitize_text_field($_POST['automation_time']);
                    } else {
                        $automation_time = 'weekly';
                    }
                    ?>"><?php
                            if(isset($_POST['automation_time']))  {
                                $automation_time = sanitize_text_field($_POST['automation_time']);
                                if($automation_time) {
                                    $automation_time_text = str_replace('_', ' ', $automation_time);
                                    $automation_time_text = ucwords($automation_time_text);
                                    echo esc_html(ucwords($automation_time_text));
                                }
                            } else {
                                echo 'Automation Repeat Time';
                            }
                        ?>
                </option>
                <option value="once">Run Once</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
           </select>
            <select id="api-select" class="api-search-input api-search-form-select" name="product_type" required>
                <option value="<?php
                    if(isset($_POST['product_type'])) {
                        $product_type = sanitize_text_field($_POST['product_type']);
                    } else {
                        $product_type = '';
                    }
                    ?>"><?php
                            if(isset($_POST['product_type']))  {
                                $product_type = sanitize_text_field($_POST['product_type']);
                                if($product_type) {
                                    $product_type_text = str_replace('_', ' ', $product_type);
                                    $product_type_text = ucwords($product_type_text);
                                    echo esc_html(ucwords($product_type_text));
                                }
                            } else {
                                echo 'Products to Update';
                            }
                        ?>
                </option>
                <option value="all">All Products</option>
                <option value="already_updated">Already Updated</option>
                <option value="never_updated">Never Updated</option>
                <option value="no_matching">No Matching</option>
                <option value="none_found">None Found</option>
             </select>
             <div class="tooltip">
                 <span class="api-about-symbol">?</span>
                 <span class="tooltiptext">
                     <strong>All Products:</strong> All products in your store.<br />
                    <strong>Already Updated:</strong> All products that were updated successfully in previous updates.<br />
                    <strong>Never Updated:</strong> All products have never been updated.<br />
                    <strong>No Matching:</strong> All products that were updated, where videos were found, but your plugin settings (i.e. Brand in Title) did not allow for the videos to be used.<br />
                    <strong>None Found:</strong> All products for which no videos were found.<br />
                 </span>
             </div>
             <select id="api-select" class="api-search-input api-search-form-select" name="post_status" required>
                 <option value="<?php
                     if(isset($_POST['post_status'])) {
                         $post_status = sanitize_text_field($_POST['post_status']);
                     } else {
                         $post_status = '';
                     }
                     ?>"><?php
                             if(isset($_POST['post_status']))  {
                                 $post_status = sanitize_text_field($_POST['post_status']);
                                 if($post_status) {
                                     $post_status_text = str_replace('_', ' ', $post_status);
                                     $post_status_text = ucwords($post_status_text);
                                     echo esc_html(ucwords($post_status_text));
                                 }
                             } else {
                                 echo 'Product Status';
                                 $post_status = 'published';
                             }
                         ?>
                 </option>
                 <option value="all">All</option>
                 <option value="publish">Published</option>
                 <option value="draft">Draft</option>
              </select>
            <br /><br />
            <span style="margin-left: 1.25em;"><input class="check-box" type="checkbox" name="existing_data" value="existing_data"></span>
            <label>Only Use Existing Video Data</label>
            <div class="tooltip">
                <span class="api-about-symbol">?</span>
                <span class="tooltiptext">
                    During the automation the plugin will only make API calls for products that have Never been updated.  For all other products the plugin will use the existing video data from previous updates.  This is useful if you are trying different plugin setting configurations and dont want to incure additional API usage while trying different settings.
                </span>
            </div>
           <br /><br />
            <input class="button" name="create_automation" type="submit" value="Create Automation">
            <?php wp_nonce_field('action_automation', 'nonce_automation', false); ?>
        </form>
        <!-- stop updates form -->
        <form class="api-pv-form api-pv-form-button api-pv-kill-updates" action="" method="post">
            <input class="button" style="float: right;background: #fff;min-width: 200px;color: #333;border: 1px solid #d8d8d8 !important;margin: 0px auto;" name="stop_updates" type="submit" value="Stop All Updates">
            <?php wp_nonce_field('action_stop_updates', 'nonce_stop_updates', false); ?>
        </form>
<br><br>

<?php

    // handle stop updates form submision
    if(isset($_POST['stop_updates'])) {
        if (isset($_POST['nonce_stop_updates'])) {
            $nonce_stop_updates = sanitize_text_field($_POST['nonce_stop_updates']);
        } else {
            $nonce_stop_updates = false;
        }
        if (!wp_verify_nonce($nonce_stop_updates, 'action_stop_updates')) {
            wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
        } else {
            update_option('api_pv_automation_count', 0);
            update_option('api_pv_total_products_start', 0);
            update_option('api_pv_automation_all', '');
        }
    }

    $automation_info = array();
    $automation_all = '';
    echo '<div class="api-div-admin-notice">';

    // create the automation
    if(isset($_POST['create_automation'])) {
        if (isset($_POST['nonce_automation'])) {
            $nonce_automation = sanitize_text_field($_POST['nonce_automation']);
        } else {
            $nonce_automation = false;
        }
        if (!wp_verify_nonce($nonce_automation, 'action_automation')) {
            wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
        } else {
            $paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
            $product_number_args = array(
                'post_type' => 'product',
                'posts_per_page' => 1
            );
            $product_query = new WP_Query($product_number_args);
            $total_products = $product_query->found_posts;
            update_option('api_pv_total_products_store', $total_products);

            wp_reset_query();

            // update the automation totals
            update_option('api_pv_automation_count', 0);
            update_option('api_pv_total_products_start', 0);

            // build the automation json
            if (isset($_POST['automation_time'])) {
                $automoation_time = sanitize_text_field($_POST['automation_time']);
                $automation_time_array = array(
                    'automation_time' => $automation_time
               );
                $automation_info = array_merge($automation_info, $automation_time_array);
            }
            if (isset($_POST['product_type'])) {
                $automoation_type = sanitize_text_field($_POST['product_type']);
                $product_type_array = array(
                    'product_type' => $product_type
               );
                $automation_info = array_merge($automation_info, $product_type_array);
            }
            if (isset($_POST['post_status'])) {
                $automoation_type = sanitize_text_field($_POST['post_status']);
                $post_status_array = array(
                    'post_status' => $post_status
               );
                $automation_info = array_merge($automation_info, $post_status_array);
            }
            if (isset($_POST['skip_failures'])) {
                $skip_failures = 1;
                $skip_failures_array = array(
                    'skip_failures' => $skip_failures
               );
                $automation_info = array_merge($automation_info, $skip_failures_array);
            } else {
                $skip_failures = '';
            }
            if (isset($_POST['existing_data'])) {
                $existing_data = 'true';
            } else {
                $existing_data = 'false';
            }
            $existing_data_array = array(
                'existing_data' => $existing_data
            );
            $automation_info = array_merge($automation_info, $existing_data_array);

            $automation_all = array(
                'automation' => $automation_info
            );
            // update the automation option
            $automation_all = json_encode($automation_all);
            update_option('api_pv_automation_all', $automation_all);
        }
    }

    // get the saved automation information
    $total_products_start = get_option('api_pv_total_products_start');
    $automation_count = get_option('api_pv_automation_count');
    $automation_products_per = get_option('api_pv_automation_products_per');
    $automation_all = get_option('api_pv_automation_all');
    if ( ! is_wp_error($automation_all) && $automation_all !== 'call_limit_reached') {
        $automation_all = json_decode($automation_all, true);
    }

    if ($automation_all !== '') {
        // code...
    }

    if (is_array($automation_all)) {
        $count = count($automation_all);
    } else {
        $count = 0;
    }

    // display the automation information
    if ($automation_all == 'call_limit_reached') {
        echo '<p>
            <span style="color: red;">Attention:</span> You have exceeded the number of monthly API calls available with your plan.<br />
            1. You will need to go to your <a href="https://www.apigenius.io/account/" target="_blank">Subscriptions Page on ApiGenius.io</a> and upgrade your plan.<br />
            2. You will be given a new API key for your new plan. You will need to enter it on the plugin settings page.<br />
            3. You will need to clear the current automation and create a new one.<br /><br />
            Please feel free to <a href="https://www.apigenius.io/my-tickets/" target="_blank">Open a Support Ticket</a> if you have any questions.
        </p>';
    } elseif ($automation_all == '') {
        ?>
            <h3>About Automations</h3>
            <p>With the automation feature you can automatically run product updates, searching for and embeding videos.</p>
            <p><span style="color: red;">Please Note:</span> We highly recommend that you backup your website site before running an automation.  We also recommend that you test different settings while manually updating products to make sure you have configured the plugin to provide optimal results.  Every store uses different product title configurations and thus different plugin settings will provide better results for stores.</p>
        <?php
    } elseif ($automation_count == $total_products_start && $automation_count > 0) {
        echo '<p>The previously scheduled automation is complete. Click the Stop All Updates button above to schedule another automation.</p>';
    } else {
        if ($count > 0) {
            echo '<h3 style="text-align: center;">Current Automation</h3>';
            foreach ($automation_all as $automation) {
                echo '<hr />';
                if(is_array($automation)) {
                    echo '<p>Please Note:</strong> If your website has little to no traffic, you will need to follow this guilde to set up a manual cron job. <a href="https://www.siteground.com/tutorials/wordpress/real-cron-job/" target="_blank">Replace WP Cron</a><br />
                    - Your hosting provider should be able to do it in <u>less than 5 minutes if needed</u>.<br />';
                    ?>
                        <div class="div-api-automation">
                            <?php
                                // message for stopped automation
                                if(isset($_POST['stop_updates'])) {
                                    echo '<p>The automation was stopped.</p>';
                                }
                            ?>
                            <table class="api-automation-table">
                                <tr>
                                    <td width="70%">
                                        <?php
                                            // automation information
                                            if (isset($automation['automation_time'])) {
                                                $automation_time = sanitize_text_field($automation['automation_time']);
                                                $automation_time_text = ucwords(str_replace('_', ' ', $automation_time));
                                                echo '<strong>Automation Time</strong>: ' . esc_html($automation_time_text) . '<br />';
                                            }
                                            if (isset($automation['product_type'])) {
                                                $product_type = sanitize_text_field($automation['product_type']);
                                                if ($product_type == 'no_matching') {
                                                    $product_type_text = 'Products with no matching videos on previou update.';
                                                } elseif ($product_type == 'already_updated') {
                                                    $product_type_text = 'Products that have been successfully updated.';
                                                } elseif ($product_type == 'never_updated') {
                                                    $product_type_text = 'Products that have never been updated.';
                                                } elseif ($product_type == 'none_found') {
                                                    $product_type_text = 'Products we could not find videos for on the previous update.';
                                                } else {
                                                    $product_type_text = ucwords(str_replace('_', ' ', $product_type)) . ' Products';
                                                }
                                                echo '<strong>Products to Update:</strong> ' . esc_html($product_type_text) . '<br />';
                                            }
                                            if (isset($automation['skip_failures'])) {
                                                $skip_failures = sanitize_text_field($automation['skip_failures']);
                                                if ($skip_failures == 1) {
                                                    $skip_failures_text = 'Yes';
                                                } else {
                                                    $skip_failures_text = 'No';
                                                }
                                                echo '<strong>Skip Failures:</strong> ' . esc_html($skip_failures_text) . '<br />';
                                            }
                                            if (isset($automation['existing_data'])) {
                                                $existing_data = sanitize_text_field($automation['existing_data']);
                                                if ($existing_data == 'true') {
                                                    $existing_data_text = 'Yes';
                                                } else {
                                                    $existing_data_text = 'No';
                                                }
                                                echo '<strong>Use Existing Data:</strong> ' . esc_html($existing_data_text) . '<br />';
                                            }
                                            if (isset($automation['post_status'])) {
                                                $post_status = sanitize_text_field($automation['post_status']);
                                                if ($post_status == 'draft') {
                                                    $post_status_text = 'Draft';
                                                } elseif ($post_status == 'all') {
                                                    $post_status_text = 'All';
                                                } else {
                                                    $post_status_text = 'Published';
                                                }
                                                echo '<strong>Product Status to Update:</strong> ' . esc_html($post_status_text) . '<br />';
                                            }
                                        ?>
                                        <?php
                                            if ($automation_products_per !== '') {
                                                echo '<p>The automation will process ' . esc_html($automation_products_per) . ' products every 5 minutes.</p>';
                                            }
                                        ?>
                                    </td>
                                    <td width="30%">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php
                }
                echo '<hr />';
            }
            if ($automation_count == 0) {
                // displayed before automation is started
                echo '<h4><strong>Update Status:</strong> The automation will begin in 5 minutes or less.</h4>';
                echo '<p><strong>Get Update Details:</strong> At any time you can click the Get Update Details button to see if videos were found, met your settings criteria and filter those products to get more details.</p>';
            } else {
                // displayed while automation is running
                ?>
                    <table width="100%" class="api-table">
                        <tr>
                            <td width="33%">
                                <div class="api-update-div">
                                    <h4>Current Automation<br />Update Count</h4>
                                    <p><?php echo esc_html($automation_count); ?></p>
                                </div>
                            </td>
                            <td width="33%">
                                <div class="api-update-div">
                                    <h4>Remaining Automation Updates</h4>
                                    <p><?php echo esc_html(($total_products_start - $automation_count)); ?></p>
                                <div>
                            </td>
                            <td width="33%">
                                <div class="api-update-div">
                                    <h4>Total Automation<br />Updates Scheduled</h4>
                                    <p><?php echo esc_html($total_products_start); ?></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                <?php
            }
        }
    }

    echo '<div>';

    // form to retrieve update statuses
    ?>
        <form class="api-pv-form api-pv-form-button api-pv-get-stats" action="" method="post">
            <p><input class="button" name="get_update_stats" type="submit" value="Get Update Details"></p>
            <?php wp_nonce_field('action_updat_stats', 'nonce_update_stats', false); ?>
        </form>

        <div class="api-update-stats-div">
            <?php
                if (isset($_POST['get_update_stats'])) {
                    if (isset($_POST['nonce_update_stats'])) {
                        $nonce_update_stats = sanitize_text_field($_POST['nonce_update_stats']);
                    } else {
                        $nonce_update_stats = false;
                    }
                    if (!wp_verify_nonce($nonce_update_stats, 'action_updat_stats')) {
                        wp_die('The nonce submitted by the form is incorrect! Please refresh the page and try again.');
                    } else {
                        api_pv_get_update_totals();
                    }
                }
            ?>
        </div>

    <?php
