<?php
// Retrieve the base URL of the site
$base_url = get_option( 'home' ) ?? '';
?>

<table>
    <tr>
        <th><?php esc_html_e( 'Endpoint', 'bulk-product-import' ); ?></th>
        <th><?php esc_html_e( 'Name', 'bulk-product-import' ); ?></th>
        <th><?php esc_html_e( 'Action', 'bulk-product-import' ); ?></th>
    </tr>
    <tr>
        <?php
        // Define the server status endpoint
        $server_status = esc_url( $base_url . "/wp-json/bulk-import/v1/server-status" );
        ?>
        <td id="status-api"><?php echo $server_status; ?></td>
        <td><?php esc_html_e( 'Server Status', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="status-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete products endpoint
        $delete_products = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-products" );
        ?>
        <td id="delete-api"><?php echo $delete_products; ?></td>
        <td><?php esc_html_e( 'Delete Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete trash products endpoint
        $delete_trash_products = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-trash-products" );
        ?>
        <td id="delete-trash-api"><?php echo $delete_trash_products; ?></td>
        <td><?php esc_html_e( 'Delete Trash Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-trash-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the delete Woo categories endpoint
        $delete_woo_cats = esc_url( $base_url . "/wp-json/bulk-import/v1/delete-woo-cats" );
        ?>
        <td id="delete-woo-cats-api"><?php echo $delete_woo_cats; ?></td>
        <td><?php esc_html_e( 'Delete Woo Categories', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="delete-woo-cats-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_products = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-products-db" );
        ?>
        <td id="insert-products-api"><?php echo $insert_products; ?></td>
        <td><?php esc_html_e( 'Insert Products DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-products-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_price = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-price-db" );
        ?>
        <td id="insert-price-api"><?php echo $insert_price; ?></td>
        <td><?php esc_html_e( 'Insert Price DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-price-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_stock = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-stock-db" );
        ?>
        <td id="insert-stock-api"><?php echo $insert_stock; ?></td>
        <td><?php esc_html_e( 'Insert Stock DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert-stock-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_categories = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-category-db" );
        ?>
        <td id="insert_category"><?= $insert_categories; ?></td>
        <td><?php esc_html_e( 'Insert Categories DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert_categories_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $sync_products = esc_url( $base_url . "/wp-json/bulk-import/v1/sync-products" );
        ?>
        <td id="sync-products-api"><?php echo $sync_products; ?></td>
        <td><?php esc_html_e( 'Sync Products', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="sync-products-cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $get_access_token = esc_url( $base_url . "/wp-json/bulk-import/v1/get-access-token" );
        ?>
        <td id="get_access_token"><?= $get_access_token; ?></td>
        <td><?php esc_html_e( 'Get Access Token', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="get_access_token_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $refresh_access_token = esc_url( $base_url . "/wp-json/bulk-import/v1/refresh-access-token" );
        ?>
        <td id="refresh_access_token"><?= $refresh_access_token; ?></td>
        <td><?php esc_html_e( 'Refresh Access Token', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="refresh_access_token_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        // Define the sync products endpoint
        $sign_generate = esc_url( $base_url . "/wp-json/bulk-import/v1/sign-generate" );
        ?>
        <td id="sign-generate-api"><?= $sign_generate; ?></td>
        <td><?php esc_html_e( 'Sign Generate', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="sign_generate_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_order_list = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-order-list-db" );
        ?>
        <td id="insert_order_list"><?= $insert_order_list; ?></td>
        <td><?php esc_html_e( 'Insert Order List DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert_order_list_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $insert_order_details = esc_url( $base_url . "/wp-json/bulk-import/v1/insert-order-details-db" );
        ?>
        <td id="insert_order_details"><?= $insert_order_details; ?></td>
        <td><?php esc_html_e( 'Insert Order Details DB', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="insert_order_details_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $sync_orders = esc_url( $base_url . "/wp-json/bulk-import/v1/sync-order" );
        ?>
        <td id="sync_orders"><?= $sync_orders; ?></td>
        <td><?php esc_html_e( 'Sync Orders', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="sync_orders_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
    <tr>
        <?php
        $update_order_status = esc_url( $base_url . "/wp-json/bulk-import/v1/update-order-status" );
        ?>
        <td id="update_order_status"><?= $update_order_status; ?></td>
        <td><?php esc_html_e( 'Update Order Status', 'bulk-product-import' ); ?></td>
        <td>
            <button type="button" id="update_order_status_cp" class="btn btn-primary btn-sm">
                <?php esc_html_e( 'Copy', 'bulk-product-import' ); ?>
            </button>
        </td>
    </tr>
</table>