<?php

/**
 * Template for Shopee Settings
 */

$home_url            = get_option( 'home' );
$shopee_base_url     = get_option( 'shopee_base_url', '' ) ?? '';
$shopee_partner_id   = get_option( 'shopee_partner_id', '' ) ?? '';
$shopee_partner_key  = get_option( 'shopee_partner_key', '' ) ?? '';
$shopee_shop_id      = get_option( 'shopee_shop_id', '' ) ?? '';
$shopee_access_token = get_option( 'shopee_access_token', '' ) ?? '';
$redirect_url        = get_option( 'shopee_redirect_url' ) ?? $home_url;
$auth_code           = get_option( 'shopee_auth_code', '' ) ?? '';

?>

<!-- Shopee Settings -->
<div class="container-fluid api-credentials">
    <div class="row">
        <div class="col-sm-12">
            <!-- Title for the shopee settings section -->
            <h4 class="text-center mb-3">
                <?php esc_html_e( 'Shopee Settings', 'bulk-product-import' ); ?>
            </h4>

            <!-- Form for shopee API credentials -->
            <form id="shopee-credentials-form">
                <div class="row mb-3 align-items-center">
                    <!-- Label and input for Base Url -->
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_base_url">
                            <?php esc_html_e( 'Base Url', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_base_url" id="shopee_base_url"
                               value="<?= esc_attr( $shopee_base_url ); ?>"
                               placeholder="<?= esc_attr_e( 'Base Url', 'bulk-product-import' ); ?>" required>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <!-- Label and input for Partner ID -->
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_partner_id">
                            <?php esc_html_e( 'Partner ID', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_partner_id" id="shopee_partner_id"
                               value="<?= esc_attr( $shopee_partner_id ); ?>"
                               placeholder="<?php esc_attr_e( 'Partner ID', 'bulk-product-import' ); ?>" required>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <!-- Label and input for Partner Key -->
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_partner_key">
                            <?php esc_html_e( 'Partner Key', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_partner_key" id="shopee_partner_key"
                               value="<?= esc_attr( $shopee_partner_key ); ?>"
                               placeholder="<?php esc_attr_e( 'Partner Key', 'bulk-product-import' ); ?>" required>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <!-- Label and input for Shop ID -->
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_shop_id">
                            <?php esc_html_e( 'Shop ID', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_shop_id" id="shopee_shop_id"
                               value="<?= esc_attr( $shopee_shop_id ); ?>"
                               placeholder="<?php esc_attr_e( 'Shop ID', 'bulk-product-import' ); ?>" required>
                    </div>
                </div>

                <!-- Hidden fields for Access Token, Redirect Url, and Code -->
                <div class="row mb-3 align-items-center" style="display: none;">
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_access_token">
                            <?php esc_html_e( 'Access Token', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_access_token" id="shopee_access_token"
                               value="<?= esc_attr( $shopee_access_token ); ?>"
                               placeholder="<?php esc_attr_e( 'Access Token', 'bulk-product-import' ); ?>">
                    </div>
                </div>

                <div class="row mb-3 align-items-center" style="display: none;">
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_redirect_url">
                            <?php esc_html_e( 'Redirect Url', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_redirect_url" id="shopee_redirect_url"
                               value="<?= esc_attr( $redirect_url ); ?>"
                               placeholder="<?php esc_attr_e( 'Redirect Url', 'bulk-product-import' ); ?>">
                    </div>
                </div>

                <div class="row mb-3 align-items-center" style="display: none;">
                    <div class="col-md-3">
                        <label class="form-label" for="shopee_auth_code">
                            <?php esc_html_e( 'Code', 'bulk-product-import' ); ?>
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="shopee_auth_code" id="shopee_auth_code"
                               value="<?= esc_attr( $auth_code ); ?>"
                               placeholder="<?php esc_attr_e( 'Code', 'bulk-product-import' ); ?>">
                    </div>
                </div>

                <!-- Submit button to save credentials -->
                <div class="row mt-3">
                    <div class="col-md-12 text-start">
                        <input type="submit" class="btn btn-primary" id="shopee-credential-save"
                               value="<?php esc_attr_e( 'Save', 'bulk-product-import' ); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
