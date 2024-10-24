<?php

/**
 * Template for Shopee Auth
 */

?>

<!-- Shopee Settings -->
<div class="container-fluid api-credentials">
    <div class="row">
        <div class="col-sm-12">
            <h4><?php esc_html_e( 'Authenticate with shop', 'bulk-product-import' ); ?></h4>

            <!-- Auth button -->
             <button type="button" id="shopee-shop-authentication" class="btn btn-primary mt-3 d-flex align-items-center justify-content-between gap-2">
             <?php esc_html_e( 'Authenticate', 'bulk-product-import' ); ?>
             <span class="shopee-auth-wrapper"></span>
            </button>
        </div>
    </div>
</div>