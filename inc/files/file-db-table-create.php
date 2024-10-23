<?php

function sync_products() {
    global $wpdb;

    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        product_number VARCHAR(255) NOT NULL,
        item_status VARCHAR(100) NULL,
        updated_time VARCHAR(100) NULL,
        status VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_products Table when plugin deactivated
function remove_sync_products() {
    global $wpdb;

    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_products';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

function sync_stock() {
    global $wpdb;

    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_stock';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        product_number VARCHAR(255) NOT NULL,
        stock INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_stock Table when plugin deactivated
function remove_sync_stock() {
    global $wpdb;

    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_stock';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

function sync_price() {
    global $wpdb;

    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_price';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        product_number VARCHAR(255) NOT NULL,
        regular_price INT NOT NULL,
        sale_price INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_price Table when plugin deactivated
function remove_sync_price() {
    global $wpdb;

    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_price';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

function create_sync_category() {

    global $wpdb;
    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_category';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        category_id INT NOT NULL,
        parent_category_id INT NULL,
        category_name VARCHAR(255) NOT NULL,
        category_data TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_products Table when plugin deactivated
function remove_sync_category() {

    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_category';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

function create_sync_order_list() {

    global $wpdb;
    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_order_list';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        order_sn VARCHAR(255) NOT NULL UNIQUE,
        status VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_products Table when plugin deactivated
function remove_sync_order_list() {

    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_order_list';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}

function create_sync_order_details() {

    global $wpdb;
    $table_prefix    = get_option( 'be-table-prefix' ) ?? '';
    $table_name      = $wpdb->prefix . $table_prefix . 'sync_order_details';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        order_sn VARCHAR(255) NOT NULL UNIQUE,
        order_status VARCHAR(100) NULL,
        order_details TEXT NOT NULL,
        status VARCHAR(20) NOT NULL,
        woo_order_created INT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Remove sync_products Table when plugin deactivated
function remove_sync_order_details() {

    global $wpdb;
    $table_prefix = get_option( 'be-table-prefix' ) ?? '';
    $table_name   = $wpdb->prefix . $table_prefix . 'sync_order_details';
    $sql          = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query( $sql );
}


function create_db_tables() {
    sync_products();
    sync_stock();
    sync_price();
    create_sync_category();
    create_sync_order_list();
    create_sync_order_details();
}

function remove_db_tables() {
    remove_sync_products();
    remove_sync_stock();
    remove_sync_price();
    remove_sync_category();
    remove_sync_order_list();
    remove_sync_order_details();
}