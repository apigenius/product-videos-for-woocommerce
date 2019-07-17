<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
   die;
}

	// display the plugin settings page
if (!function_exists ('api_pv_display_settings')) {
	function api_pv_display_settings() {
		// check if user is allowed access
		if (! current_user_can('manage_options')) return;
		?>
        <style>
            .wrap {
                padding: 20px;
                border: 1px solid #d8d8d8;
                max-width: 1400px;
                margin: 20px auto;
                display: block;
                background: #f8f8f8;
            }
            .form-table {
                padding: 20px 50px;
                border: 1px solid #d8d8d8;
                background: #fff;
            }
            .form-table th {
                padding-left: 10px;
            }
            tr {
                padding: 20px;
                border: 1px solid #d8d8d8;
            }
            .form-table input[type="text"] {
                background: #f8f8f8;
            }
        </style>
        <div class="api-pv-settings-div">
    		<div class="wrap">
    			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    			<form action="options.php" method="post">
    				<?php
        				// output security fields
        				settings_fields('api_pv_options');
        				// output setting sections
        				do_settings_sections('api-pv-settings');
        				// submit button
        				submit_button();
    				?>
    			</form>
    		</div>
        </div>

		<?php

	}
}
    // display the settings
    api_pv_display_settings();
