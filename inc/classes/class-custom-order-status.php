<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Singleton;

class Custom_Order_Status {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'init', [ $this, 'register_custom_order_statuses' ] );
        add_filter( 'wc_order_statuses', [ $this, 'add_custom_order_statuses' ] );
    }

    public function register_custom_order_statuses() {
        $statuses = [
            'unpaid'             => 'Unpaid',
            'ready_to_ship'      => 'Ready to Ship',
            'processed'          => 'Processed',
            'shipped'            => 'Shipped',
            'to_confirm_receive' => 'To Confirm Receive',
            'retry_ship'         => 'Retry Ship',
            'to_return'          => 'To Return',
            'in_cancel'          => 'In Cancel',
        ];

        foreach ( $statuses as $status_key => $status_label ) {
            register_post_status( 'wc-' . $status_key, [
                'label'                     => _x( $status_label, 'Order status', 'bulk-product-import' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( $status_label . ' <span class="count">(%s)</span>', $status_label . ' <span class="count">(%s)</span>', 'bulk-product-import' ),
            ] );
        }
    }

    function add_custom_order_statuses( $order_statuses ) {
        $custom_statuses = [
            'wc-unpaid'             => _x( 'Unpaid', 'Order status', 'bulk-product-import' ),
            'wc-ready-to-ship'      => _x( 'Ready to Ship', 'Order status', 'bulk-product-import' ),
            'wc-processed'          => _x( 'Processed', 'Order status', 'bulk-product-import' ),
            'wc-shipped'            => _x( 'Shipped', 'Order status', 'bulk-product-import' ),
            'wc-to-confirm-receive' => _x( 'To Confirm Receive', 'Order status', 'bulk-product-import' ),
            'wc-retry-ship'         => _x( 'Retry Ship', 'Order status', 'bulk-product-import' ),
            'wc-to-return'          => _x( 'To Return', 'Order status', 'bulk-product-import' ),
            'wc-in-cancel'          => _x( 'In Cancel', 'Order status', 'bulk-product-import' ),
        ];

        return array_merge( $order_statuses, $custom_statuses );
    }
}