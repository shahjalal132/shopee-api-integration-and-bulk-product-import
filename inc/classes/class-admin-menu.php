<?php

namespace BULK_IMPORT\Inc;

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

use BULK_IMPORT\Inc\Traits\Singleton;

class Admin_Menu {

    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
        // add_action( 'admin_menu', [ $this, 'register_csv_import_menu' ] );
        // add_action( 'admin_menu', [ $this, 'register_sheet_import_menu' ] );
        add_action( 'wp_ajax_save_client_credentials', [ $this, 'save_client_credentials' ] );
        add_action( 'wp_ajax_save_table_prefix', [ $this, 'save_table_prefix' ] );
        add_action( 'wp_ajax_save_shopee_credentials', [ $this, 'save_shopee_credentials' ] );
        add_action( 'wp_ajax_shopee_shop_authentication', [ $this, 'shopee_shop_authentication_callback' ] );
    }

    public function register_admin_menu() {
        add_menu_page(
            __( 'Shopee Settings', 'bulk-product-import' ),
            __( 'Shopee Settings', 'bulk-product-import' ),
            'manage_options',
            'bulk_product_import',
            [ $this, 'bulk_product_import_page_html' ],
            'dashicons-cloud-upload',
            80
        );
    }

    public function register_csv_import_menu() {
        add_submenu_page(
            'bulk_product_import',
            'CSV Import',
            'CSV Import',
            'manage_options',
            'bulk_product_csv_import',
            [ $this, 'bulk_product_csv_import_page_html' ]
        );
    }

    public function register_sheet_import_menu() {
        add_submenu_page(
            'bulk_product_import',
            'Sheet Import',
            'Sheet Import',
            'manage_options',
            'bulk_product_sheet_import',
            [ $this, 'bulk_product_sheet_import_page_html' ]
        );
    }

    public function bulk_product_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'Shopee API Integration & WooCommerce Bulk Product Import', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div id="be-tabs" class="mt-5">
            <div id="tabs">

                <ul class="nav nav-pills">
                    <li class="nav-item"><a href="#api"
                            class="nav-link be-nav-links"><?php esc_html_e( 'API', 'bulk-product-import' ); ?></a></li>
                    <li class="nav-item"><a href="#shopee-api"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Shopee Settings', 'bulk-product-import' ); ?></a>
                    </li>
                    <li class="nav-item"><a href="#shopee-auth"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Authentication', 'bulk-product-import' ); ?></a>
                    </li>
                    <li class="nav-item"><a href="#tables"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Table Prefix', 'bulk-product-import' ); ?></a></li>
                    <li class="nav-item"><a href="#endpoints"
                            class="nav-link be-nav-links"><?php esc_html_e( 'Endpoints', 'bulk-product-import' ); ?></a></li>
                </ul>

                <div id="api">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-api.php'; ?>
                </div>

                <div id="shopee-api">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-shopee-settings.php'; ?>
                </div>

                <div id="shopee-auth">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-shopee-auth.php'; ?>
                </div>

                <div id="tables">
                    <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-tables.php'; ?>
                </div>

                <div id="endpoints">
                    <div id="api-endpoints" class="common-shadow">
                        <h4>
                            <?php _e( 'API Endpoints', 'bulk-product-import' ); ?>
                        </h4>

                        <div id="api-endpoints-table">
                            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-endpoints.php'; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }

    public function bulk_product_csv_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'WooCommerce Bulk Product Import CSV', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div class="wrap">
            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-csv.php'; ?>
        </div>

        <?php
    }

    public function bulk_product_sheet_import_page_html() {
        ?>

        <div class="entry-header">
            <h1 class="entry-title text-center mt-3" style="color: #2271B1">
                <?php esc_html_e( 'WooCommerce Bulk Product Import Sheet', 'bulk-product-import' ); ?>
            </h1>
        </div>

        <div class="wrap">
            <?php include BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/template-parts/template-sheet.php'; ?>
        </div>

        <?php
    }

    public function save_client_credentials() {
        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        $client_id     = sanitize_text_field( $_POST['client_id'] );
        $client_secret = sanitize_text_field( $_POST['client_secret'] );

        update_option( 'be-client-id', $client_id );
        update_option( 'be-client-secret', $client_secret );

        wp_send_json_success( __( 'Credentials saved successfully', 'bulk-product-import' ) );
    }

    public function save_table_prefix() {

        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        $table_prefix = sanitize_text_field( $_POST['table_prefix'] );
        update_option( 'be-table-prefix', $table_prefix );

        wp_send_json_success( __( 'Table prefix saved successfully', 'bulk-product-import' ) );
    }

    public function save_shopee_credentials() {

        // check nonce
        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        // check if user can manage options
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        // get the shopee credentials
        $shopee_base_url     = sanitize_text_field( $_POST['shopee_base_url'] );
        $shopee_partner_id   = sanitize_text_field( $_POST['shopee_partner_id'] );
        $shopee_partner_key  = sanitize_text_field( $_POST['shopee_partner_key'] );
        $shopee_shop_id      = sanitize_text_field( $_POST['shopee_shop_id'] );
        $shopee_access_token = sanitize_text_field( $_POST['shopee_access_token'] );
        $shopee_redirect_url = sanitize_text_field( $_POST['shopee_redirect_url'] );
        $shopee_auth_code    = sanitize_text_field( $_POST['shopee_auth_code'] );

        // save the shopee credentials
        update_option( 'shopee_base_url', $shopee_base_url );
        update_option( 'shopee_partner_id', $shopee_partner_id );
        update_option( 'shopee_partner_key', $shopee_partner_key );
        update_option( 'shopee_shop_id', $shopee_shop_id );
        update_option( 'shopee_access_token', $shopee_access_token );
        update_option( 'shopee_redirect_url', $shopee_redirect_url );
        update_option( 'shopee_auth_code', $shopee_auth_code );

        // return success response
        wp_send_json_success( __( 'Credentials saved successfully', 'bulk-product-import' ) );
    }

    public function shopee_shop_authentication_callback() {

        // check nonce
        check_ajax_referer( 'bulk_product_import_nonce', 'nonce' );

        // check if user can manage options
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'Unauthorized user', 'bulk-product-import' ) );
        }

        // get the shopee credentials
        $path               = "/api/v2/shop/auth_partner";
        $shopee_partner_id  = get_option( 'shopee_partner_id', '' ) ?? '';
        $shopee_partner_key = get_option( 'shopee_partner_key', '' ) ?? '';
        $shopee_base_url    = get_option( 'shopee_base_url', '' ) ?? '';
        $timestamp          = time();
        $redirect_url       = get_option( 'shopee_redirect_url' );
        $baseString         = sprintf( "%s%s%s", $shopee_partner_id, $path, $timestamp );
        $sign               = hash_hmac( 'sha256', $baseString, $shopee_partner_key );
        // generate auth rul
        $url = sprintf( "%s%s?partner_id=%s&timestamp=%s&sign=%s&redirect=%s", $shopee_base_url, $path, $shopee_partner_id, $timestamp, $sign, $redirect_url );

        // return success response
        wp_send_json_success( $url );
    }
}
