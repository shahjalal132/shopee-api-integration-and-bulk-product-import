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
        add_action( 'admin_head', [ $this, 'add_custom_status_colors' ] );
    }

    public function register_custom_order_statuses() {
        $statuses = [
            'unpaid'             => 'Unpaid',
            'ready-to-ship'      => 'Ready to Ship',
            'processed'          => 'Processed',
            'shipped'            => 'Shipped',
            'to-confirm-receive' => 'To Confirm Receive',
            'retry-ship'         => 'Retry Ship',
            'to-return'          => 'To Return',
            'in-cancel'          => 'In Cancel',
        ];

        foreach ( $statuses as $status_key => $status_label ) {
            register_post_status( 'wc-' . $status_key, [
                'label'                     => _x( $status_label, 'Order status', 'bulk-product-import' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( $status_label . ' <span class="count">(%s)</span>', $status_label . ' <span
                class="count">(%s)</span>', 'bulk-product-import' ),
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

    public function add_custom_status_colors() {
        echo '<style>
    mark.order-status.status-unpaid {
        background-color: #FFDD57 !important;
    }

    mark.order-status.status-ready-to-ship {
        background-color: #57D9FF !important;
    }

    mark.order-status.status-processed {
        background-color: #28A745 !important;
    }

    mark.order-status.status-shipped {
        background-color: #007BFF !important;
    }

    mark.order-status.status-to-confirm-receive {
        background-color: #FFC107 !important;
    }

    mark.order-status.status-retry-ship {
        background-color: #FF5733 !important;
    }

    mark.order-status.status-to-return {
        background-color: #FF6961 !important;
    }

    mark.order-status.status-in-cancel {
        background-color: #6C757D !important;
    }
</style>';
    }
}