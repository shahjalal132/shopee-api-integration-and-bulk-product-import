<?php

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

function sync_order_with_woocommerce() {

    $limit = get_option( 'shopee_how_many_create_orders' ) ?? 10;

    try {
        // WooCommerce store information
        $website_url     = home_url();
        $consumer_key    = get_option( 'be-client-id' ) ?? '';
        $consumer_secret = get_option( 'be-client-secret' ) ?? '';

        // Set up the API client with WooCommerce store URL and credentials
        $client = new Client(
            $website_url,
            $consumer_key,
            $consumer_secret,
            [
                'verify_ssl' => false,
                'wp_api'     => true,
                'version'    => 'wc/v3',
                'timeout'    => 400,
            ]
        );

        global $wpdb;
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';
        $table_name   = $wpdb->prefix . $table_prefix . 'sync_order_details';

        // Get up to 10 orders that have not been created in WooCommerce
        $orders = $wpdb->get_results( "SELECT * FROM $table_name WHERE woo_order_created = 0 LIMIT $limit" );

        if ( empty( $orders ) ) {
            return "No orders found or orders already created.";
        }

        foreach ( $orders as $order ) {
            // Retrieve order information
            $serial_id    = $order->id;
            $order_sn     = $order->order_sn;
            $order_status = strtolower( str_replace( [ '_', ' ' ], '-', $order->order_status ) );

            $order_details     = json_decode( $order->order_details, true );
            $payment_method    = $order_details['payment_method'];
            $recipient_address = $order_details['recipient_address'] ?? [];
            $customer_note     = $order_details['note'] ?? '';
            $currency          = $order_details['currency'] ?? '';
            $shipping_fee      = $order_details['estimated_shipping_fee'] ?? 0;

            $name      = $recipient_address['name'] ?? '';
            $address_1 = $recipient_address['full_address'] ?? '';
            $city      = $recipient_address['city'] ?? '';
            $state     = $recipient_address['state'] ?? '';
            $post_code = $recipient_address['zipcode'] ?? '';
            $country   = $recipient_address['region'] ?? '';
            $email     = 'email@gmail.com';
            $phone     = $recipient_address['phone'] ?? '';

            // Prepare line items
            $item_list  = $order_details['item_list'] ?? [];
            $line_items = array_map( function ($item) {
                return [
                    'name'       => $item['item_name'] ?? '',
                    'product_id' => $item['item_id'] ?? 0,
                    'quantity'   => $item['model_quantity_purchased'] ?? 0,
                    'sku'        => strval( $item['item_id'] ?? 0 ),
                ];
            }, $item_list );

            $shipping_carrier = $order_details['shipping_carrier'] ?? '';
            $method_id        = strtolower( str_replace( ' ', '-', $shipping_carrier ) );
            $total            = $order_details['total_amount'] ?? 0;

            // Generate order creation data
            $order_creation_data = [
                'payment_method'       => $payment_method,
                'payment_method_title' => $payment_method,
                'status'               => $order_status,
                'customer_note'        => $customer_note,
                'currency'             => $currency,
                'billing'              => [
                    'first_name' => $name,
                    'last_name'  => $name,
                    'address_1'  => $address_1,
                    'city'       => $city,
                    'state'      => $state,
                    'postcode'   => $post_code,
                    'country'    => $country,
                    'email'      => $email,
                    'phone'      => $phone,
                ],
                'shipping'             => [
                    'first_name' => $name,
                    'last_name'  => $name,
                    'address_1'  => $address_1,
                    'city'       => $city,
                    'state'      => $state,
                    'postcode'   => $post_code,
                    'country'    => $country,
                ],
                'line_items'           => $line_items,
                'shipping_lines'       => [
                    [
                        'method_id'    => $method_id,
                        'method_title' => $shipping_carrier,
                        'total'        => strval( $shipping_fee ),
                    ],
                ],
                'total'                => strval( $total ),
            ];

            // Create the order
            $order_created = $client->post( 'orders', $order_creation_data );
            $order_id      = $order_created->id;

            // Update order total amount
            update_order_total_amount( $order_id, $total );

            // Update the status in the database
            $wpdb->update(
                $table_name,
                [
                    'order_id'          => $order_id,
                    'status'            => 'completed',
                    'woo_order_created' => 1,
                ],
                [
                    'id' => $serial_id,
                ]
            );
        }

        return 'Orders created successfully.';

    } catch (HttpClientException $e) {
        // Handle the exception and log errors
        // echo '<pre><code>' . print_r( $e->getMessage(), true ) . '</code><pre>'; // Error message.
        // echo '<pre><code>' . print_r( $e->getRequest(), true ) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.

        return 'Order creation failed.';
    }
}

function update_order_total_amount( $order_id, $total_amount ) {

    global $wpdb;
    $table_name = $wpdb->prefix . 'wc_orders';

    // update total amount
    $wpdb->update(
        $table_name,
        [
            'total_amount' => $total_amount,
        ],
        [
            'id' => $order_id,
        ]
    );
}

function update_order_status() {

    $limit = get_option( 'shopee_how_many_update_orders' ) ?? 10;

    try {
        // WooCommerce store information
        $website_url     = home_url();
        $consumer_key    = get_option( 'be-client-id' ) ?? '';
        $consumer_secret = get_option( 'be-client-secret' ) ?? '';
        $offset          = get_option( 'shopee_order_offset_id' ) ?? 0;

        // Set up the API client with WooCommerce store URL and credentials
        $client = new Client(
            $website_url,
            $consumer_key,
            $consumer_secret,
            [
                'verify_ssl' => false,
                'wp_api'     => true,
                'version'    => 'wc/v3',
                'timeout'    => 400,
            ]
        );

        global $wpdb;
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';
        $table_name   = $wpdb->prefix . $table_prefix . 'sync_order_details';

        // Get multiple incomplete orders that have been created in WooCommerce
        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, order_sn, order_id FROM $table_name WHERE order_status != 'COMPLETED' AND woo_order_created = 1 ORDER BY id ASC LIMIT %d OFFSET %d",
                intval( $limit ),
                $offset
            )
        );

        $updated_orders = [];
        $failed_orders  = [];

        if ( !empty( $orders ) ) {
            foreach ( $orders as $order ) {
                $serial_id = $order->id;
                $order_sn  = $order->order_sn;
                $order_id  = $order->order_id;

                $order_details = get_single_order_details_from_api( $order_sn );
                $order_details = json_decode( $order_details, true );

                if ( !empty( $order_details ) ) {
                    $response         = $order_details['response'];
                    $order_list       = $response['order_list'];
                    $new_order_status = '';

                    if ( !empty( $order_list ) ) {
                        foreach ( $order_list as $order_item ) {
                            $new_order_status = $order_item['order_status'];
                        }
                    }

                    $original_order_status = $new_order_status;
                    $new_order_status      = strtolower( str_replace( '_', '-', $new_order_status ) );
                    $new_order_status      = str_replace( ' ', '-', $new_order_status );

                    // Data to update status on WooCommerce
                    $order_update_data = [
                        'status' => $new_order_status,
                    ];

                    try {
                        // Update the order status on WooCommerce
                        $client->put( 'orders/' . $order_id, $order_update_data );

                        // Update order status in the custom database
                        $wpdb->update(
                            $table_name,
                            [
                                'order_status' => $original_order_status,
                                'status'       => 'status updated',
                            ],
                            [ 'id' => $serial_id ]
                        );

                        // Track successful updates
                        $updated_orders[] = "Order SN: $order_sn (WooCommerce ID: $order_id) - Updated to status: $original_order_status";

                        // Update the offset ID for tracking progress
                        update_option( 'shopee_order_offset_id', $serial_id );
                    } catch (Exception $update_exception) {
                        // Track failed updates due to WooCommerce update failure
                        $failed_orders[] = "Order SN: $order_sn (WooCommerce ID: $order_id) - Failed to update";
                    }
                } else {
                    // Track orders with missing details from the API
                    $failed_orders[] = "Order SN: $order_sn - No details found in API response";
                }
            }

            // Check if there are still incomplete orders
            $remaining_orders = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE order_status != 'COMPLETED' AND woo_order_created = 1" );

            if ( $remaining_orders == 0 ) {
                // Reset offset if no incomplete orders remain
                update_option( 'shopee_order_offset_id', 0 );
            }

            // Return a summary message
            $result_message = "Order Status Update Summary:\n\n";

            if ( !empty( $updated_orders ) ) {
                $result_message .= "Successfully Updated Orders:\n" . implode( "\n", $updated_orders ) . "\n\n";
            }

            if ( !empty( $failed_orders ) ) {
                $result_message .= "Failed to Update Orders:\n" . implode( "\n", $failed_orders ) . "\n";
            }

            return nl2br( $result_message );

        } else {
            // No orders found, reset offset for future checks
            update_option( 'shopee_order_offset_id', 0 );
            return "No incomplete orders found to update.";
        }

    } catch (HttpClientException $e) {
        // Log error response details if available
        echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.

        return 'Order status update failed due to client error.';
    }
}

function get_single_order_details_from_api( $order_sn ) {
    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // get the shopee credentials file path
    $file_path = BULK_PRODUCT_IMPORT_PLUGIN_URL . "/inc/auth/signs.json";
    // decode the json file
    $signs_data = json_decode( file_get_contents( $file_path ), true );
    // get the item list
    $item_data = $signs_data['get_order_detail'];

    // get the access token
    $access_token = $item_data['access_token'];
    // get the timestamp
    $timestamp = $item_data['timestamp'];
    $sign      = $item_data['sign'];

    $url = sprintf( "%s/api/v2/order/get_order_detail?access_token=%s&order_sn_list=%s&partner_id=%s&response_optional_fields=total_amount,buyer_user_id,buyer_username,estimated_shipping_fee,recipient_address,note,note_update_time,item_list,pickup_done_time,package_list,shipping_carrier,payment_method,invoice_data&shop_id=%s&sign=%s&timestamp=%s", $shopee_base_url, $access_token, $order_sn, $shopee_partner_id, $shopee_shop_id, $sign, $timestamp );

    $curl = curl_init();
    curl_setopt_array( $curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(),
    ) );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}