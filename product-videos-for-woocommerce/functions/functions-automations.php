<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// check to see if there is an active automation
if (!function_exists ('api_pv_automation_function')) {
	function api_pv_automation_function() {
		$automation_all = get_option('api_pv_automation_all');
	    $automation_all = json_decode($automation_all, true);

		if (is_array($automation_all)) {
			foreach ($automation_all as $automation) {
				api_pv_automation();
			}
		}
	}
	add_action('api_pv_automation_hook', 'api_pv_automation_function');
}

// run the automation on specified products
if (!function_exists ('api_pv_automation')) {
	function api_pv_automation() {
		// get automation stats
		$total_products_start = get_option('api_pv_total_products_start');
		$automation_all = get_option('api_pv_automation_all');
	    $automation_all = json_decode($automation_all, true);

		if (is_array($automation_all)) {
			foreach ($automation_all as $automation) {
				// specific automation details
				$automation_time = isset($automation['automation_time']) ? sanitize_text_field($automation['automation_time']) : '';
				$product_type = isset($automation['product_type']) ? sanitize_text_field($automation['product_type']) : '';
				$post_status = isset($automation['post_status']) ? sanitize_text_field($automation['post_status']) : '';
				$skip_failures = isset($automation['skip_failures']) ? sanitize_text_field($automation['skip_failures']) : '';
				$existing_data = isset($automation['existing_data']) ? sanitize_text_field($automation['existing_data']) : 'false';

				if ($automation_time !== '') {
					$paged = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
					// how many products processed per automation
					$total_products_store = get_option('api_pv_total_products_store');
					if ($total_products_store < 14000 && $automation_time == 'daily') {
					    $posts_per_page = 50;
					} elseif ($total_products_store > 14001 && $total_products_store < 28000 && $automation_time == 'daily') {
					    $posts_per_page = 100;
					} else {
					    $posts_per_page = 50;
					}

					// testing
					$posts_per_page = 10;

					// Base query args
					$query_args = array(
					   'post_type'			          =>   'product',
					   'paged'                       =>    $paged,
					   'orderby'                     =>    'modified',
					   'order'                       =>    'asc',
					   'posts_per_page'              =>    $posts_per_page
					);

					if ($post_status !== 'all' && $post_status !== '') {
						$post_status_args = array(
							'post_status' => $post_status
						);
						$query_args = array_merge($query_args, $post_status_args);
					}

					// reltion arguments for meta query
					$meta_args_all = [];

					// skip_failures arguments
				    if ($skip_failures == 1) {
				        $meta_args = array(
				            'key' => 'api_pv_job_status',
				            'value' => 'failed',
				            'compare' => '!=',
				        );
				        array_push($meta_args_all, $meta_args);
				    }
					// product_type argumants
					if ($product_type == 'never_updated') {
				        $meta_args = array(
				            'key' => 'api_pv_job_status',
				            'compare' => 'NOT EXISTS',
				        );
				        array_push($meta_args_all, $meta_args);
						echo 'not exist';
				    } elseif ($product_type == 'already_updated') {
				        $meta_args = array(
				            'key' => 'api_pv_job_status',
				            'value' => 'updated',
				            'compare' => '=',
				        );
				        array_push($meta_args_all, $meta_args);
				    } elseif ($product_type == 'no_matching') {
				        $meta_args = array(
				            'key' => 'api_pv_job_status',
				            'value' => 'none-selected',
				            'compare' => '=',
				        );
				        array_push($meta_args_all, $meta_args);
				    } elseif ($product_type == 'none_found') {
				        $meta_args = array(
				            'key' => 'api_pv_job_status',
				            'value' => 'failed',
				            'compare' => '=',
				        );
				        array_push($meta_args_all, $meta_args);
				    }
					// last updated args
					if ($automation_time !== '' && $product_type !== 'never_updated') {
						if( $automation_time == 'daily' ) {
							$date = date( 'Y-m-d', strtotime( '-1 days' ) );
						} elseif( $automation_time == 'weekly' ) {
							$date = date( 'Y-m-d', strtotime( '-7 days' ) );
						} elseif( $automation_time == 'monthly' ) {
							$date = date( 'Y-m-d', strtotime( '-30 days' ) );
						} else {
							$date = '';
						}
						if ($date) {
							$meta_args = array(
								'key' => 'api_pv_last_updated',
								'value' => $date,
								'compare' => '<'
							);
						    array_push($meta_args_all, $meta_args);
						}
					}

					// insert the relation if needed
					if (is_array($meta_args_all)) {
						$args_count = count($meta_args_all);
						if ($args_count > 2) {
							if ($product_type == 'all') {
							   $meta_args_all = array('relation' => 'OR');
							} else {
							   $meta_args_all = array('relation' => 'AND');
							}
						} elseif ($args_count > 0) {
							$args = array(
								'meta_query' => $meta_args_all
							);
							$query_args = array_merge($query_args, $args);
						}
					}

					$the_query = new WP_Query( $query_args );

					$the_query_count = $the_query->found_posts;
					if ($total_products_start == 0) {
						update_option('api_pv_total_products_start', $the_query_count);
					}

					// update the number of products per automation instance
					update_option('api_pv_automation_products_per', $posts_per_page);

					if( $the_query->have_POSTs() ) {
						while ( $the_query->have_POSTs() ) {
							$the_query->the_POST();
							global $product;
							$product_id = $product->get_id();
							$total_products_start = get_option('api_pv_total_products_start');
							$automation_count = get_option('api_pv_automation_count');
							$status = get_post_meta($product_id, 'api_pv_job_status', true);
							$last_updated = get_post_meta($product_id, 'api_pv_last_updated', true);
							$post_status = get_post_status($product_id);
							$query = '';
							$website = '';
							// echo '<br />' . 'Product ID: ' . $product_id . '<br />' . 'Update Status: ' . $status . '<br />' . 'Post Status: ' . $post_status . '<br />' . 'Last Updated: ' . $last_updated . '<br />' . 'Total Start: ' . $total_products_start . '<br />'  . 'Total Products Store: ' . $total_products_store . '<br />' . 'Automation Count: ' . $automation_count . '<hr />';

							// call the automation function
							if ($automation_time == 'once') {
								if ($total_products_start > $automation_count) {
									api_pv_get_video($product_id, $query, $website, $existing_data);
								} else {
							        update_option('api_pv_automation_all', '');
								}
							} else {
								api_pv_get_video($product_id, $query, $website, $existing_data);
							}

							$automation_count++;
							update_option('api_pv_automation_count', $automation_count);
						}
					}
					// Reset Query
					wp_reset_query();
				}
			}
		}
	}
}
