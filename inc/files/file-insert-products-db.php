<?php

// TRUNCATE Table
function truncate_table( $table_name ) {
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE $table_name" );
}

$shopee_base_url     = get_option( 'shopee_base_url', '' ) ?? '';
$shopee_partner_id   = get_option( 'shopee_partner_id', '' ) ?? '';
$shopee_partner_key  = get_option( 'shopee_partner_key', '' ) ?? '';
$shopee_shop_id      = get_option( 'shopee_shop_id', '' ) ?? '';
$shopee_access_token = get_option( 'shopee_access_token', '' ) ?? '';

// fetch products from api
function fetch_products_from_api() {

    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // get the shopee credentials file path
    $file_path = BULK_PRODUCT_IMPORT_PLUGIN_URL . "/inc/auth/signs.json";
    // decode the json file
    $signs_data = json_decode( file_get_contents( $file_path ), true );
    // get the item list
    $item_data = $signs_data['get_item_list'];

    // get the access token
    $access_token = $item_data['access_token'];
    // get the timestamp
    $timestamp = $item_data['timestamp'];
    $sign      = $item_data['sign'];

    // generate the url
    $url = sprintf(
        "%s/api/v2/product/get_item_list?access_token=%s&item_status=NORMAL&offset=0&page_size=100&partner_id=1006814&shop_id=%s&sign=%s&timestamp=%s",
        $shopee_base_url,
        $access_token,
        $shopee_shop_id,
        $sign,
        $timestamp
    );

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

// insert products to database
function insert_products_db() {

    ob_start();

    $api_response        = fetch_products_from_api();
    $api_response_decode = json_decode( $api_response, true );
    $response            = $api_response_decode['response'];
    $products            = $response['item'];

    /* $file             = BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/uploads/products.json';
    $file_data        = file_get_contents( $file );
    $decode_file_data = json_decode( $file_data, true );
    $response         = $decode_file_data['response'];
    $products         = $response['item_list']; */

    // Insert to database
    global $wpdb;
    $table_prefix   = get_option( 'be-table-prefix' ) ?? '';
    $products_table = $wpdb->prefix . $table_prefix . 'sync_products';
    truncate_table( $products_table );

    foreach ( $products as $product ) {

        // get item sku
        $item_sku    = $product['item_id'];
        $item_status = $product['item_status'];
        $update_time = $product['update_time'];

        $wpdb->insert(
            $products_table,
            [
                'product_number' => $item_sku,
                'item_status'    => $item_status,
                'updated_time'   => $update_time,
                'status'         => 'pending',
            ]
        );
    }

    echo '<h4>Products inserted successfully DB</h4>';

    return ob_get_clean();
}


// +++++++++++++++++++++++++++++++++
// fetch price from api
function fetch_price_from_api() {

    $curl = curl_init();
    curl_setopt_array( $curl, [
        CURLOPT_URL            => '',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => [
            '',
        ],
    ] );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;

}

// insert price to database
function insert_price_db() {

    ob_start();

    /* $api_response = fetch_price_from_api();
    $products     = json_decode( $api_response, true );

    // Insert to database
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $price_table  = $wpdb->prefix . $table_prefix . 'sync_price';
    truncate_table( $price_table );

    foreach ( $products as $product ) {

        // extract price

        $wpdb->insert(
            $price_table,
            [
                'product_number' => '',
                'regular_price'  => 0,
                'sale_price'     => 0,
            ]
        );
    } */

    echo '<h4>Prices inserted successfully DB</h4>';

    return ob_get_clean();

}


// +++++++++++++++++++++++++++++++++
// fetch price from api
function fetch_stock_from_api() {

    $curl = curl_init();
    curl_setopt_array( $curl, [
        CURLOPT_URL            => '',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => [
            '',
        ],
    ] );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;

}

// insert stock to database
function insert_stock_db() {

    ob_start();

    /* $api_response = fetch_stock_from_api();
    $products     = json_decode( $api_response, true );

    // Insert to database
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $stock_table  = $wpdb->prefix . $table_prefix . 'sync_stock';
    truncate_table( $stock_table );

    foreach ( $products as $product ) {

        // extract stock

        $wpdb->insert(
            $stock_table,
            [
                'product_number' => '',
                'stock'          => 0,
            ]
        );
    } */

    echo '<h4>Stocks inserted successfully DB</h4>';

    return ob_get_clean();

}

// fetch category from api
function fetch_category_from_api() {

    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // get the shopee credentials file path
    $file_path = BULK_PRODUCT_IMPORT_PLUGIN_URL . "/inc/auth/signs.json";
    // decode the json file
    $signs_data = json_decode( file_get_contents( $file_path ), true );
    // get the item list
    $item_data = $signs_data['get_category'];

    // get the access token
    $access_token = $item_data['access_token'];
    // get the timestamp
    $timestamp = $item_data['timestamp'];
    $sign      = $item_data['sign'];

    $url = sprintf( "%s/api/v2/product/get_category?access_token=%s&language=&partner_id=%s&shop_id=%s&sign=%s&timestamp=%s", $shopee_base_url, $access_token, $shopee_partner_id, $shopee_shop_id, $sign, $timestamp );

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

// insert category to database
function insert_category_db() {

    $api_response        = fetch_category_from_api();
    $api_response_decode = json_decode( $api_response, true );
    $category_list       = [];
    if ( isset( $api_response_decode['response'] ) ) {
        $response      = $api_response_decode['response'];
        $category_list = $response['category_list'];
    }

    // Insert to database
    global $wpdb;
    $table_prefix   = get_option( 'be-table-prefix' ) ?? '';
    $category_table = $wpdb->prefix . $table_prefix . 'sync_category';
    truncate_table( $category_table );

    if ( !empty( $category_list ) ) {
        foreach ( $category_list as $category ) {

            // get item sku
            $category_id        = $category['category_id'];
            $parent_category_id = $category['parent_category_id'];
            $category_name      = $category['original_category_name'];
            $category_data      = json_encode( $category );

            $wpdb->insert(
                $category_table,
                [
                    'category_id'        => $category_id,
                    'parent_category_id' => $parent_category_id,
                    'category_name'      => $category_name,
                    'category_data'      => $category_data,
                ]
            );
        }
        return "<h4>Categories inserted successfully DB</h4>";
    } else {
        return "<h4>No categories found</h4>";
    }

}