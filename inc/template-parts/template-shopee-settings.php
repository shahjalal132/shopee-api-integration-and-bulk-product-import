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
                <div class="d-flex align-items-center mt-2">
                    <!-- Label and input for base url -->
                    <label class="form-label" for="shopee_base_url">
                        <?php esc_html_e( 'Base Url', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control" style="width: 60% !important; margin-left: 4.7rem;"
                        name="shopee_base_url" id="shopee_base_url" value="<?= esc_attr( $shopee_base_url ); ?>"
                        placeholder="<?= esc_attr_e( 'Base Url', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner ID -->
                    <label class="form-label" for="shopee_partner_id">
                        <?php esc_html_e( 'Partner ID', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important" name="shopee_partner_id"
                        id="shopee_partner_id" value="<?= esc_attr( $shopee_partner_id ); ?>"
                        placeholder="<?php esc_attr_e( 'Partner ID', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner Key -->
                    <label class="form-label" for="shopee_partner_key">
                        <?php esc_html_e( 'Partner Key', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important" name="shopee_partner_key"
                        id="shopee_partner_key" value="<?= esc_attr( $shopee_partner_key ); ?>"
                        placeholder="<?php esc_attr_e( 'Partner Key', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner Key -->
                    <label class="form-label" for="shopee_shop_id">
                        <?php esc_html_e( 'Shop ID', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important" name="shopee_shop_id"
                        id="shopee_shop_id" value="<?= esc_attr( $shopee_shop_id ); ?>"
                        placeholder="<?php esc_attr_e( 'Shop ID', 'bulk-product-import' ); ?>" required>
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner Key -->
                    <label class="form-label" for="shopee_access_token">
                        <?php esc_html_e( 'Access Token', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important"
                        name="shopee_access_token" id="shopee_access_token"
                        value="<?= esc_attr( $shopee_access_token ); ?>"
                        placeholder="<?php esc_attr_e( 'Access Token', 'bulk-product-import' ); ?>">
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner Key -->
                    <label class="form-label" for="shopee_redirect_url">
                        <?php esc_html_e( 'Redirect Url', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important"
                        name="shopee_redirect_url" id="shopee_redirect_url" value="<?= esc_attr( $redirect_url ); ?>"
                        placeholder="<?php esc_attr_e( 'Redirect Url', 'bulk-product-import' ); ?>">
                </div>
                <div class="d-flex align-items-center mt-3">
                    <!-- Label and input for Partner Key -->
                    <label class="form-label" for="shopee_auth_code">
                        <?php esc_html_e( 'Code', 'bulk-product-import' ); ?>
                    </label>
                    <input type="text" class="form-control ms-5" style="width: 60% !important" name="shopee_auth_code"
                        id="shopee_auth_code" value="<?= esc_attr( $auth_code ); ?>"
                        placeholder="<?php esc_attr_e( 'Code', 'bulk-product-import' ); ?>">
                </div>
                <!-- Submit button to save credentials -->
                <input type="submit" class="btn btn-primary mt-3" id="shopee-credential-save"
                    value="<?php esc_attr_e( 'Save', 'bulk-product-import' ); ?>">
            </form>
        </div>
    </div>
</div>