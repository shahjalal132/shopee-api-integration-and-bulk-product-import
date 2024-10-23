<?php

/**
 * Import Products to WooCommerce template
 */

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

function sync_order_with_woocommerce() {
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

        // get pending order details from database
        $sql   = "SELECT * FROM $table_name WHERE status = 'pending' LIMIT 1";
        $order = $wpdb->get_row( $sql );

        if ( !empty( $order ) ) {
            // retrieve order information
            $serial_id     = $order->id;
            $order_sn      = $order->order_sn;
            $order_status  = $order->order_status;
            $order_details = json_decode( $order->order_details, true );

            $payment_method    = $order_details['payment_method'];
            $recipient_address = $order_details['recipient_address'] ?? [];
            $name              = $recipient_address['name'] ?? '';
            $first_name        = '';
            $last_name         = '';
            $address_1         = $recipient_address['full_address'] ?? '';
            $city              = $recipient_address['city'] ?? '';
            $state             = $recipient_address['state'] ?? '';
            $post_code         = $recipient_address['zipcode'] ?? 0;
            $country           = $recipient_address['region'] ?? '';
            $email             = 'email@gmail.com';
            $phone             = $recipient_address['phone'] ?? 0;

            // get item list
            $item_list = $order_details['item_list'] ?? [];
            // initialize line_items
            $line_items = [];
            if ( !empty( $item_list ) ) {
                foreach ( $item_list as $item ) {
                    $product_name = $item['item_name'] ?? '';
                    $product_id   = $item['item_id'] ?? 0;
                    $quantity     = $item['model_quantity_purchased'] ?? 0;
                    $line_items[] = [
                        'name'       => $product_name,
                        'product_id' => $product_id,
                        'quantity'   => $quantity,
                    ];
                }
            }

            $shipping_carrier = $order_details['shipping_carrier'] ?? '';
            // convert to lowercase and remove spaces
            $method_id    = strtolower( str_replace( ' ', '-', $shipping_carrier ) );
            $method_title = $shipping_carrier;
            $total        = $order_details['total_amount'] ?? 0;

            // generate order creation data
            $order_creation_data = [
                'payment_method'       => $payment_method,
                'payment_method_title' => $payment_method,
                'set_paid'             => true,
                'billing'              => [
                    'first_name' => $name,
                    'last_name'  => $name,
                    'address_1'  => $address_1,
                    'address_2'  => '',
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
                    'address_2'  => '',
                    'city'       => $city,
                    'state'      => $state,
                    'postcode'   => $post_code,
                    'country'    => $country,
                ],
                'line_items'           => $line_items,
                'shipping_lines'       => [
                    [
                        'method_id'    => $method_id,
                        'method_title' => $method_title,
                        'total'        => strval( $total ),
                    ],
                ],
            ];

            // create order
            $client->post( 'orders', $order_creation_data );

            // update order status
            $wpdb->update(
                $table_name,
                [
                    'status' => 'completed',
                ],
                [
                    'id' => $serial_id,
                ]
            );

            // return success message
            return 'Order created successfully.';
        } else {
            return "No order found";
        }

    } catch (HttpClientException $e) {

        echo '<pre><code>' . print_r( $e->getMessage(), true ) . '</code><pre>'; // Error message.
        echo '<pre><code>' . print_r( $e->getRequest(), true ) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.

        return 'Order creation failed.';
    }
}