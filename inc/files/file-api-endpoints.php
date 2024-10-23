<?php

/**
 * API Endpoints file
 */

// create an api endpoint for products
add_action( 'rest_api_init', 'bulk_products_import' );

function bulk_products_import() {

    // add new api endpoint to get products from api and add them to database
    register_rest_route( 'bulk-import/v1', '/sync-products', [
        'methods'             => 'GET',
        'callback'            => 'sync_products_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-products-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_products_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-price-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_price_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-stock-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_stock_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-category-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_category_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-order-list-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_order_list_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/insert-order-details-db', [
        'methods'             => 'GET',
        'callback'            => 'insert_order_details_db_api_callback',
        'permission_callback' => '__return_true',
    ] );

    register_rest_route( 'bulk-import/v1', '/sync-order', [
        'methods'             => 'GET',
        'callback'            => 'sync_order_with_woocommerce_callback',
        'permission_callback' => '__return_true',
    ] );

}

function sync_products_api_callback() {
    return products_import_woocommerce();
}

function insert_products_db_api_callback() {
    return insert_products_db();
}

function insert_price_db_api_callback() {
    return insert_price_db();
}

function insert_stock_db_api_callback() {
    return insert_stock_db();
}

function insert_category_db_api_callback() {
    return insert_category_db();
}

function insert_order_list_db_api_callback() {
    return insert_order_list_to_db();
}

function insert_order_details_db_api_callback() {
    return insert_order_details_to_db();
}

function sync_order_with_woocommerce_callback() {
    return sync_order_with_woocommerce();
}