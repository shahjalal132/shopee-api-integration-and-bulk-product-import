<?php

/**
 * Template for Shopee Options
 */

$how_many_create_orders = get_option( 'shopee_how_many_create_orders' ) ?? 10;
$how_many_update_orders = get_option( 'shopee_how_many_update_orders' ) ?? 10;

?>

<!-- Shopee Settings -->
<div class="container-fluid shopee-options">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="text-center"><?php esc_html_e( 'Orders Create/Update Options', 'bulk-product-import' ); ?></h4>
        </div>

        <form id="shopee-options-form">
            <div class="row align-items-center mt-2">
                <!-- Label and input for Create Orders -->
                <div class="col-md-3">
                    <label class="form-label" for="how_many_create_orders"
                        title="How many orders to create every minute">
                        <?php esc_html_e( 'Create Orders', 'bulk-product-import' ); ?>
                    </label>
                </div>
                <div class="col-md-9">
                    <input type="number" class="form-control" style="max-width: 150px;" name="how_many_create_orders"
                        id="how_many_create_orders" value="<?php echo esc_attr( $how_many_create_orders ); ?>">
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <!-- Label and input for Update Orders -->
                <div class="col-md-3">
                    <label class="form-label" for="how_many_update_orders"
                        title="How many orders to update every minute">
                        <?php esc_html_e( 'Update Orders', 'bulk-product-import' ); ?>
                    </label>
                </div>
                <div class="col-md-9">
                    <input type="number" class="form-control" style="max-width: 150px;" name="how_many_update_orders"
                        id="how_many_update_orders" value="<?php echo esc_attr( $how_many_update_orders ); ?>">
                </div>
            </div>

            <!-- Submit button to save options -->
            <div class="row mt-3">
                <div class="col-md-12 text-start">
                    <input type="submit" class="btn btn-primary" id="shopee_options_save"
                        value="<?php esc_attr_e( 'Save', 'bulk-product-import' ); ?>">
                </div>
            </div>
        </form>

    </div>
</div>