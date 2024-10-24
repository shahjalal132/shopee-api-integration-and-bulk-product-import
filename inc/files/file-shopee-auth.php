<?php

function shopee_get_access_token() {
    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // Retrieve Shopee credentials and partner details
    $code        = get_option( 'shopee_auth_code', '' ) ?? '';
    $partner_key = get_option( 'shopee_partner_key', '' ) ?? '';
    $path        = "/api/v2/auth/token/get";
    $timestamp   = time();

    // Prepare the base string for signature and calculate the sign
    $base_string = sprintf( "%s%s%s", intval( $shopee_partner_id ), $path, $timestamp );
    $sign        = hash_hmac( 'sha256', $base_string, $partner_key );

    // Prepare request body and URL
    $body = array(
        "code"       => strval( $code ),
        "shop_id"    => intval( $shopee_shop_id ),
        "partner_id" => intval( $shopee_partner_id ),
    );

    $url = sprintf( "%s%s?partner_id=%s&timestamp=%s&sign=%s", $shopee_base_url, $path, intval( $shopee_partner_id ), $timestamp, $sign );

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array( $curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => json_encode( $body ),
        CURLOPT_HTTPHEADER     => array( 'Content-Type: application/json' ),
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    ) );

    // Execute cURL and capture the response
    $response = curl_exec( $curl );

    // put_program_logs( 'Shopee Access Token: ' . $response );

    // Check for cURL errors
    if ( $response === false ) {
        $error = curl_error( $curl );
        curl_close( $curl );
        return "cURL Error: " . $error;
    }

    // Close the cURL session
    curl_close( $curl );

    // Decode the API response
    $result = json_decode( $response, true );

    // Check for errors in the API response
    if ( isset( $result['error'] ) && !empty( $result['error'] ) ) {
        return "API Error: " . $result['message'] . " (Error code: " . $result['error'] . ")";
    }

    // Extract tokens from the API response
    if ( isset( $result['access_token'] ) && isset( $result['refresh_token'] ) ) {
        $access_token  = $result['access_token'];
        $refresh_token = $result['refresh_token'];

        // Update tokens in the options table
        update_option( 'shopee_refresh_token', $refresh_token );
        update_option( 'shopee_access_token', $access_token );

        return "Access Token and Refresh Token retrieved and updated successfully.";
    } else {
        // Handle unexpected response format
        return "Unexpected API response: " . $response;
    }
}

function shopee_refresh_access_token() {
    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    // Retrieve tokens and partner details
    $refreshToken = get_option( 'shopee_refresh_token' ) ?? '';
    $partner_key  = get_option( 'shopee_partner_key' ) ?? '';
    $path         = "/api/v2/auth/access_token/get";
    $timestamp    = time();

    // Prepare body and base string for signature
    $body = array(
        "partner_id"    => intval( $shopee_partner_id ),
        "shop_id"       => intval( $shopee_shop_id ),
        "refresh_token" => $refreshToken,
    );

    $baseString = sprintf( "%s%s%s", intval( $shopee_partner_id ), $path, $timestamp );
    $sign       = hash_hmac( 'sha256', $baseString, $partner_key );

    // Prepare the full URL
    $url = sprintf( "%s%s?partner_id=%s&timestamp=%s&sign=%s", $shopee_base_url, $path, intval( $shopee_partner_id ), $timestamp, $sign );

    // Initialize cURL
    $c = curl_init( $url );
    curl_setopt( $c, CURLOPT_POST, 1 );
    curl_setopt( $c, CURLOPT_POSTFIELDS, json_encode( $body ) );
    curl_setopt( $c, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
    curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );

    // Execute cURL and capture response
    $result = curl_exec( $c );

    // Check for cURL errors
    if ( curl_errno( $c ) ) {
        $curl_error = curl_error( $c );
        curl_close( $c );
        return "cURL Error: " . $curl_error;
    }

    // Close cURL after execution
    curl_close( $c );

    // Decode JSON response
    $ret = json_decode( $result, true );

    // Check for errors in the API response
    if ( isset( $ret['error'] ) && !empty( $ret['error'] ) ) {
        return "API Error: " . $ret['message'] . " (Error code: " . $ret['error'] . ")";
    }

    // Extract tokens from the API response
    if ( isset( $ret['access_token'] ) && isset( $ret['refresh_token'] ) ) {
        $accessToken     = $ret['access_token'];
        $newRefreshToken = $ret['refresh_token'];

        // Update options in the database
        update_option( 'shopee_refresh_token', $newRefreshToken );
        update_option( 'shopee_access_token', $accessToken );

        return "Access Token and Refresh Token updated successfully.";
    } else {
        // Handle unexpected API response format
        return "Unexpected API response: " . $result;
    }
}
