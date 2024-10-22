<?php

/**
 * Import Products to WooCommerce template
 */

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

if ( file_exists( BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/vendor/autoload.php' ) ) {
    require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/vendor/autoload.php';
}

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

/**
 * Function to insert products into WooCommerce
 * Fetch product data from database
 * Process product data and insert into WooCommerce
 * 
 */
function products_import_woocommerce() {
    try {
        // Get global $wpdb object
        global $wpdb;

        // get table prefix
        $table_prefix = get_option( 'be-table-prefix' ) ?? '';

        // define products table
        $products_table = $wpdb->prefix . $table_prefix . 'sync_products';

        // define price table
        $price_table = $wpdb->prefix . $table_prefix . 'sync_price';

        // define stock table
        $stock_table = $wpdb->prefix . $table_prefix . 'sync_stock';

        // WooCommerce store information
        $website_url     = home_url();
        $consumer_key    = get_option( 'be-client-id' ) ?? '';
        $consumer_secret = get_option( 'be-client-secret' ) ?? '';

        // SQL query
        $sql = "SELECT id, product_number, updated_time FROM $products_table WHERE status = 'pending' LIMIT 1";

        // Retrieve pending products from the database
        $products = $wpdb->get_results( $sql );

        if ( !empty( $products ) && is_array( $products ) ) {
            foreach ( $products as $product ) {

                // Retrieve product data
                $serial_id = $product->id;
                $sku       = $product->product_number;

                // get product info from api
                $product_info = get_single_product_base_info( $sku );
                // put_program_logs( 'Product Info:' . $product_info );
                $product_info = json_decode( $product_info, true );
                $product_data = [];
                if ( isset( $product_info['response'] ) ) {
                    $response     = $product_info['response'];
                    $product_data = $response['item_list'][0];
                }

                if ( !empty( $product_data ) ) {
                    // get product title
                    $title       = $product_data['item_name'];
                    $item_sku    = $product_data['item_sku'];
                    // $sku         = $item_sku;
                    $description = '';

                    // init stock
                    $quantity = 0;

                    // get stock info
                    if ( isset( $product_data['stock_info_v2'] ) ) {
                        $stock_info = $product_data['stock_info_v2'];

                        // Check if 'seller_stock' key exists within 'stock_info_v2'
                        if ( isset( $stock_info['seller_stock'] ) ) {
                            $seller_stock = $stock_info['seller_stock'];
                        }

                        if ( $seller_stock ) {
                            // get product stock
                            $quantity = $seller_stock[0]['stock'];
                        }
                    }

                    // get images
                    $image_id_list  = $product_data['image']['image_id_list'];
                    $image_url_list = $product_data['image']['image_url_list'];

                    // Retrieve product images
                    $images = $image_url_list;

                    // Retrieve product dimension
                    $weight         = $product_data['weight'];
                    $dimension      = (array) $product_data['dimension'];
                    $package_length = $dimension['package_length'];
                    $package_width  = $dimension['package_width'];
                    $package_height = $dimension['package_height'];

                    // Retrieve logistic_info
                    $logistic_info = (array) $product_data['logistic_info'];
                    // convert to json
                    $logistic_info = json_encode( $logistic_info );

                    // Retrieve pre order info
                    $pre_order_info = (array) $product_data['pre_order'];
                    // convert to json
                    $pre_order_info = json_encode( $pre_order_info );

                    $condition      = (string) $product_data['condition'];
                    $item_status    = (string) $product_data['item_status'];
                    $has_model      = $product_data['has_model'];
                    $brands         = (array) $product_data['brand'];
                    $item_dangerous = $product_data['item_dangerous'];

                    $description_info = (array) $product_data['description_info'];
                    $description_type = (string) $product_data['description_type'];
                    $description      = get_description_based_on_type( $description_info, $description_type );

                    // get category id
                    $category_id = $product_data['category_id'];
                    // Retrieve product category
                    $category = (array) get_category_name_by_id( $category_id );

                    $category_name        = '';
                    $parent_category_name = '';
                    if ( !empty( $category ) ) {
                        // Retrieve the category names
                        $category_name        = (string) $category['category_name'];
                        $parent_category_name = (string) $category['parent_category_name'];
                    }

                    // Retrieve product tags
                    $tags = '';

                    $price_info = [];
                    if ( isset( $product_data['price_info'] ) ) {
                        // get price info
                        $price_info = $product_data['price_info'][0];
                    }

                    $regular_price = null;
                    $sale_price    = null;
                    if ( !empty( $price_info ) ) {
                        // Extract prices
                        $regular_price = $price_info['original_price'];
                        $sale_price    = $price_info['current_price'];
                    }

                    // generate additional info
                    $additional_info = [
                        'item_sku'       => $item_sku,
                        'image_id_list'  => $image_id_list,
                        'weight'         => $weight,
                        'package_length' => $package_length,
                        'package_width'  => $package_width,
                        'package_height' => $package_height,
                        'logistic_info'  => $logistic_info,
                        'pre_order'      => $pre_order_info,
                        'condition'      => $condition,
                        'item_status'    => $item_status,
                        'has_model'      => $has_model,
                        'brands'         => $brands,
                        'item_dangerous' => $item_dangerous,
                    ];

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

                    // Check if the product already exists in WooCommerce
                    $args = array(
                        'post_type'  => 'product',
                        'meta_query' => array(
                            array(
                                'key'     => '_sku',
                                'value'   => $sku,
                                'compare' => '=',
                            ),
                        ),
                    );

                    // Check if the product already exists
                    $existing_products = new WP_Query( $args );

                    if ( $existing_products->have_posts() ) {
                        $existing_products->the_post();

                        // Get product id
                        $_product_id = get_the_ID();

                        // Update the status of the processed product in your database
                        $wpdb->update(
                            $products_table,
                            [ 'status' => 'completed' ],
                            [ 'id' => $serial_id ]
                        );

                        // Update the simple product if it already exists
                        $product_data = [
                            'name'        => $title,
                            'sku'         => $sku,
                            'type'        => 'simple',
                            'description' => $description,
                            'attributes'  => [],
                        ];

                        // Update product
                        $client->put( 'products/' . $_product_id, $product_data );

                        // Update product prices
                        update_post_meta( $_product_id, '_regular_price', $regular_price );
                        update_post_meta( $_product_id, '_price', $sale_price );

                        // Check if both category and parent category exist
                        if ( !empty( $category_name ) && !empty( $parent_category_name ) ) {
                            // Save both parent and child categories
                            wp_set_object_terms( $_product_id, [ $parent_category_name, $category_name ], 'product_cat' );
                        } elseif ( !empty( $category_name ) ) {
                            // Save only the child category if no parent category
                            wp_set_object_terms( $_product_id, $category_name, 'product_cat' );
                        } elseif ( !empty( $parent_category_name ) ) {
                            // Save only the parent category if no child category
                            wp_set_object_terms( $_product_id, $parent_category_name, 'product_cat' );
                        }

                        // update product additional information
                        update_product_additional_info( $_product_id, $additional_info );

                        // Return success response
                        return new \WP_REST_Response( [
                            'success' => true,
                            'message' => 'Product updated successfully',
                        ] );

                    } else {
                        // Create a new simple product if it does not exist
                        $_product_data = [
                            'name'        => $title,
                            'sku'         => $sku,
                            'type'        => 'simple',
                            'description' => $description,
                            'attributes'  => [],
                        ];

                        // Create the product
                        $_products  = $client->post( 'products', $_product_data );
                        $product_id = $_products->id;

                        // Set product information
                        wp_set_object_terms( $product_id, 'simple', 'product_type' );
                        update_post_meta( $product_id, '_visibility', 'visible' );

                        // Update product stock
                        update_post_meta( $product_id, '_stock', $quantity );

                        // Update product prices
                        update_post_meta( $product_id, '_regular_price', $regular_price );
                        update_post_meta( $product_id, '_price', $sale_price );

                        // Check if both category and parent category exist
                        if ( !empty( $category_name ) && !empty( $parent_category_name ) ) {
                            // Save both parent and child categories
                            wp_set_object_terms( $product_id, [ $parent_category_name, $category_name ], 'product_cat' );
                        } elseif ( !empty( $category_name ) ) {
                            // Save only the child category if no parent category
                            wp_set_object_terms( $product_id, $category_name, 'product_cat' );
                        } elseif ( !empty( $parent_category_name ) ) {
                            // Save only the parent category if no child category
                            wp_set_object_terms( $product_id, $parent_category_name, 'product_cat' );
                        }

                        // Update product tags
                        wp_set_object_terms( $product_id, $tags, 'product_tag' );

                        // Display out of stock message if stock is 0
                        if ( $quantity <= 0 ) {
                            update_post_meta( $product_id, '_stock_status', 'outofstock' );
                        } else {
                            update_post_meta( $product_id, '_stock_status', 'instock' );
                        }
                        update_post_meta( $product_id, '_manage_stock', 'yes' );

                        // Set product image gallery and thumbnail
                        if ( $images ) {
                            set_product_images( $product_id, $images );
                        }

                        // update product additional information
                        update_product_additional_info( $product_id, $additional_info );

                        // Update the status of product in database
                        $wpdb->update(
                            $products_table,
                            [ 'status' => 'completed' ],
                            [ 'id' => $serial_id ]
                        );

                        // Return success response
                        return new \WP_REST_Response( [
                            'success' => true,
                            'message' => 'Product import successfully',
                        ] );
                    }
                } else {

                    return new \WP_REST_Response( [
                        'success' => false,
                        'message' => 'No product found. Invalid timestamp.',
                    ] );
                }
            }
        }
    } catch (HttpClientException $e) {

        echo '<pre><code>' . print_r( $e->getMessage(), true ) . '</code><pre>'; // Error message.
        echo '<pre><code>' . print_r( $e->getRequest(), true ) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r( $e->getResponse(), true ) . '</code><pre>'; // Last response data.

        return new \WP_REST_Response( [
            'success' => false,
            'message' => 'Product import failed.',
        ] );
    }
}

/**
 * Get Category Name from db by id
 *
 * @param int $category_id
 * @return array
 */
function get_category_name_by_id( $category_id ) {
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_category';

    // sql query
    $sql = "SELECT category_id, parent_category_id, category_name FROM $table_name WHERE category_id = $category_id";

    // get results
    $category_result = $wpdb->get_results( $sql );

    if ( !empty( $category_result ) ) {
        // get parent category id
        $parent_category_id = $category_result[0]->parent_category_id;

        $parent_category_name = '';
        if ( $parent_category_id > 0 ) {
            // get parent category name by id
            $parent_category_name = get_parent_category_name_by_id( $parent_category_id );
        }

        // get category name
        $category_name = $category_result[0]->category_name;

        // generate result
        $result = [
            'parent_category_name' => $parent_category_name,
            'category_name'        => $category_name,
        ];

        // return result
        return $result;
    }

    // return empty result
    return [];
}

/**
 * Get Parent Category Name from db by id
 * @param int $category_id
 * @return string
 */
function get_parent_category_name_by_id( $category_id ) {
    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_category';

    // sql query
    $sql = "SELECT category_name FROM $table_name WHERE category_id = $category_id";

    // get results
    $category_result = $wpdb->get_results( $sql );

    $parent_category_name = '';
    if ( !empty( $category_result ) ) {
        // get category name
        $parent_category_name = $category_result[0]->category_name;
    }

    // return parent category name
    return $parent_category_name;
}

/**
 * Update product additional information.
 *
 * @param int   $_product_id      The product ID.
 * @param array $additional_info  The array containing additional product information.
 */
function update_product_additional_info( $_product_id, $additional_info ) {
    // Update weight
    if ( isset( $additional_info['weight'] ) ) {
        update_post_meta( $_product_id, '_weight', $additional_info['weight'] );
    }

    // Update package length
    if ( isset( $additional_info['package_length'] ) ) {
        update_post_meta( $_product_id, '_length', $additional_info['package_length'] );
    }

    // Update package width
    if ( isset( $additional_info['package_width'] ) ) {
        update_post_meta( $_product_id, '_width', $additional_info['package_width'] );
    }

    // Update package height
    if ( isset( $additional_info['package_height'] ) ) {
        update_post_meta( $_product_id, '_height', $additional_info['package_height'] );
    }

    // Update logistic info (if it's a serialized array or other data type)
    if ( isset( $additional_info['logistic_info'] ) ) {
        update_post_meta( $_product_id, '_logistic_info', maybe_serialize( $additional_info['logistic_info'] ) );
    }

    // Update pre-order info
    if ( isset( $additional_info['pre_order'] ) ) {
        update_post_meta( $_product_id, '_pre_order', $additional_info['pre_order'] );
    }

    // Update condition
    if ( isset( $additional_info['condition'] ) ) {
        update_post_meta( $_product_id, '_condition', $additional_info['condition'] );
    }

    // Update item status
    if ( isset( $additional_info['item_status'] ) ) {
        update_post_meta( $_product_id, '_item_status', $additional_info['item_status'] );
    }

    // Update has model
    if ( isset( $additional_info['has_model'] ) ) {
        update_post_meta( $_product_id, '_has_model', $additional_info['has_model'] );
    }

    // Update brands
    if ( isset( $additional_info['brands'] ) ) {
        update_post_meta( $_product_id, '_brands', maybe_serialize( $additional_info['brands'] ) );
    }

    // Update item dangerous flag
    if ( isset( $additional_info['item_dangerous'] ) ) {
        update_post_meta( $_product_id, '_item_dangerous', $additional_info['item_dangerous'] );
    }

    // Update item SKU
    if ( isset( $additional_info['item_sku'] ) ) {
        update_post_meta( $_product_id, '_item_sku', $additional_info['item_sku'] );
    }

    // Update image ID list (assuming it's a serialized array)
    if ( isset( $additional_info['image_id_list'] ) ) {
        update_post_meta( $_product_id, '_image_id_list', maybe_serialize( $additional_info['image_id_list'] ) );
    }
}

/**
 * Get description based on the description type.
 *
 * @param array  $description_info Array containing the description information.
 * @param string $description_type The type of the description (e.g., 'extended', 'short', 'technical').
 * @return string The actual description text based on the type or a fallback message.
 */
function get_description_based_on_type( $description_info, $description_type ) {
    // Initialize the description text as empty or a default message
    $description_text = '';

    // Check if the description info and type are available
    if ( !empty( $description_info ) && !empty( $description_type ) ) {
        switch ($description_type) {
            case 'extended':
                if ( isset( $description_info['extended_description']['field_list'][0]['text'] ) ) {
                    $description_text = $description_info['extended_description']['field_list'][0]['text'];
                }
                break;

            case 'short':
                if ( isset( $description_info['short_description']['field_list'][0]['text'] ) ) {
                    $description_text = $description_info['short_description']['field_list'][0]['text'];
                }
                break;

            case 'technical':
                if ( isset( $description_info['technical_description']['field_list'][0]['text'] ) ) {
                    $description_text = $description_info['technical_description']['field_list'][0]['text'];
                }
                break;

            // Add other cases for different description types as needed
            default:
                $description_text = '';
                break;
        }
    }

    return $description_text;
}

/**
 * Get the base information of a single product from shopee api.
 * @param mixed $product_id
 * @return bool|string
 */
function get_single_product_base_info( $product_id ) {

    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // get the shopee credentials file path
    $file_path = BULK_PRODUCT_IMPORT_PLUGIN_URL . "/inc/auth/signs.json";
    // decode the json file
    $signs_data = json_decode( file_get_contents( $file_path ), true );
    // get the item list
    $item_data = $signs_data['get_item_base_info'];

    // get the access token
    $access_token = $item_data['access_token'];
    // get the timestamp
    $timestamp = $item_data['timestamp'];
    $sign      = $item_data['sign'];

    $url = sprintf( "%s/api/v2/product/get_item_base_info?access_token=%s&partner_id=%s&shop_id=%s&sign=%s&timestamp=%s&item_id_list=%s&need_tax_info=true&need_complaint_policy=true", $shopee_base_url, $access_token, $shopee_partner_id, $shopee_shop_id, $sign, $timestamp, $product_id );

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
    // put_program_logs( 'Response for get_single_product_base_info: ' . $response );

    curl_close( $curl );
    return $response;
}

/**
 * Set Product Images
 *
 * @param int $product_id
 * @param array $images
 * @return void
 */
function set_product_images( $product_id, $images ) {
    if ( !empty( $images ) && is_array( $images ) ) {
        foreach ( $images as $image ) {

            // Extract image name
            $image_name = basename( $image );

            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();

            // Download the image from URL and save it to the upload directory
            $image_data = file_get_contents( $image );

            if ( $image_data !== false ) {
                $image_file = $upload_dir['path'] . '/' . $image_name;
                file_put_contents( $image_file, $image_data );

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $image_name;
                $file_name = basename( $file_path );

                // Insert the image as an attachment
                $attachment = [
                    'post_mime_type' => mime_content_type( $file_path ),
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                // Add the image to the product gallery
                $gallery_ids   = get_post_meta( $product_id, '_product_image_gallery', true );
                $gallery_ids   = explode( ',', $gallery_ids );
                $gallery_ids[] = $attach_id;
                update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

                // Set the image as the product thumbnail
                set_post_thumbnail( $product_id, $attach_id );

                // if not set post-thumbnail then set a random thumbnail from gallery
                if ( !has_post_thumbnail( $product_id ) ) {
                    if ( !empty( $gallery_ids ) ) {
                        $random_attach_id = $gallery_ids[array_rand( $gallery_ids )];
                        set_post_thumbnail( $product_id, $random_attach_id );
                    }
                }

            }
        }
    }
}

/**
 * Set product images with unique image name
 *
 * @param int $product_id
 * @param array $images
 * @return void
 */
function set_product_images_with_unique_image_name( $product_id, $images ) {
    if ( !empty( $images ) && is_array( $images ) ) {

        $first_image = true;
        $gallery_ids = get_post_meta( $product_id, '_product_image_gallery', true );
        $gallery_ids = !empty( $gallery_ids ) ? explode( ',', $gallery_ids ) : [];

        foreach ( $images as $image_url ) {
            // Extract image name and generate a unique name using product_id
            $image_name        = basename( $image_url );
            $unique_image_name = $product_id . '-' . time() . '-' . $image_name;

            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();

            // Download the image from URL and save it to the upload directory
            $image_data = file_get_contents( $image_url );

            if ( $image_data !== false ) {
                $image_file = $upload_dir['path'] . '/' . $unique_image_name;
                file_put_contents( $image_file, $image_data );

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $unique_image_name;
                $file_name = basename( $file_path );

                // Insert the image as an attachment
                $attachment = [
                    'post_mime_type' => mime_content_type( $file_path ),
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];

                $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                // You need to generate the attachment metadata and update the attachment
                require_once( ABSPATH . 'wp-admin/includes/image.php' );
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
                wp_update_attachment_metadata( $attach_id, $attach_data );

                // Add the image to the product gallery
                $gallery_ids[] = $attach_id;

                // Set the first image as the featured image
                if ( $first_image ) {
                    set_post_thumbnail( $product_id, $attach_id );
                    $first_image = false;
                }
            }
        }

        // Update the product gallery meta field
        update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );
    }
}