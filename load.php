<?php

/**
 * File loader file
 * all files in this folder will be loaded
 */

defined( "ABSPATH" ) || exit( "Direct Access Not Allowed" );

require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-db-table-create.php';
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-import-products-woo.php';
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-insert-products-db.php';
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-api-endpoints.php';
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-sync-order.php';
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/files/file-shopee-auth.php';

// include helper functions
require_once BULK_PRODUCT_IMPORT_PLUGIN_PATH . '/inc/helpers/helper-helper-functions.php';