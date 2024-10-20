<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Program_Logs;
use BULK_IMPORT\Inc\Traits\Singleton;

class Sign_Generator {

    use Singleton;
    use Program_Logs;

    private $shopee_base_url;
    private $shopee_partner_id;
    private $shopee_partner_key;
    private $shopee_shop_id;
    private $shopee_access_token;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // setup hooks
        add_action( 'rest_api_init', [ $this, 'register_api_endpoints' ] );

        // get the shopee credentials
        $this->shopee_base_url     = get_option( 'shopee_base_url', '' ) ?? '';
        $this->shopee_partner_id   = get_option( 'shopee_partner_id', '' ) ?? '';
        $this->shopee_partner_key  = get_option( 'shopee_partner_key', '' ) ?? '';
        $this->shopee_shop_id      = get_option( 'shopee_shop_id', '' ) ?? '';
        $this->shopee_access_token = get_option( 'shopee_access_token', '' ) ?? '';
    }

    public function register_api_endpoints() {
        register_rest_route( 'bulk-import/v1', '/sign-generate', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'sign' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function sign( $request ) {
        return $this->generate_sign();
    }

    public function generate_sign() {

        // define the paths to generate sign
        $paths = [
            "/api/v2/product/get_item_list",
            "/api/v2/product/get_item_base_info",
        ];

        // define the result array
        $result = [];

        // generate sign for each path
        if ( !empty( $paths ) ) {
            foreach ( $paths as $path ) {
                // Call the function to generate the sign for the current path
                $sign_data = $this->generate_sign_for_single_path( $path );

                // Add the result to the array with the path name as the key
                $key          = basename( $path ); // Extract the last part of the path, e.g., get_item_list
                $result[$key] = [
                    'path'         => $path,
                    'access_token' => $this->shopee_access_token,
                    'timestamp'    => $sign_data['timestamp'],
                    'sign'         => $sign_data['sign'],
                ];
            }
        }

        // Encode the result array to JSON
        $result = json_encode( $result, JSON_UNESCAPED_SLASHES );
        // file path
        $file_path = __DIR__ . "/../auth/signs.json";
        // put sign to auth file
        file_put_contents( $file_path, $result );

        // return result
        return "Sign generated successfully.";
    }

    public function generate_sign_for_single_path( $path ) {

        // current timestamp
        $timestamp = time();
        // generate string
        $string = $this->shopee_partner_id . $path . $timestamp . $this->shopee_access_token . $this->shopee_shop_id;
        // generate sign hash
        $hash = hash_hmac( 'sha256', $string, $this->shopee_partner_key );
        // generate result
        $result = [
            'timestamp' => $timestamp,
            'sign'      => $hash,
        ];

        // return result
        return $result;
    }

}