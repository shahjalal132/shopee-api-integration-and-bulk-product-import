<?php

namespace BULK_IMPORT\Inc;

use BULK_IMPORT\Inc\Traits\Program_Logs;
use BULK_IMPORT\Inc\Traits\Singleton;

class Sign_Generator {

    use Singleton;
    use Program_Logs;

    private $base_url;
    private $partner_id;
    private $partner_key;
    private $shop_id;
    private $access_token;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'rest_api_init', [ $this, 'register_api_endpoints' ] );
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
        return 'sign generated';
    }
}