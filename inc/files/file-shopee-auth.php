<?php

function shopee_get_access_token() {

    global $shopee_base_url, $shopee_partner_id, $shopee_shop_id;

    $code        = get_option( 'shopee_auth_code', '' ) ?? '';
    $partner_key = get_option( 'shopee_partner_key', '' ) ?? '';
    $path        = "/api/v2/auth/token/get";
    $timestamp   = time();
    // put_program_logs( 'timestamp: ' . $timestamp );
    $base_string = sprintf( "%s%s%s", $shopee_partner_id, $path, $timestamp );
    $sign        = hash_hmac( 'sha256', $base_string, $partner_key );
    // put_program_logs( 'sign: ' . $sign );

    $body = array( "code" => strval( $code ), "shop_id" => intval( $shopee_shop_id ), "partner_id" => intval( $shopee_partner_id ) );

    $url = sprintf( "%s%s?partner_id=%s&timestamp=%s&sign=%s", $shopee_base_url, $path, intval( $shopee_partner_id ), $timestamp, $sign );

    $curl = curl_init();
    curl_setopt_array( $curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_POST           => 1, // Use POST explicitly
        CURLOPT_POSTFIELDS     => json_encode( $body ),
        CURLOPT_HTTPHEADER     => array( 'Content-Type: application/json' ), // Add this header
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    ) );

    $response = curl_exec( $curl );
    // put_program_logs( 'response: ' . $response );
    if ( $response === false ) {
        // Log any cURL error
        $error = curl_error( $curl );
        // put_program_logs( 'cURL error: ' . $error );
    }

    curl_close( $curl );

    // Decode and return response
    $result        = json_decode( $response, true );
    $refresh_token = $result['refresh_token'];
    $access_token  = $result['access_token'];

    // update refresh token and access token to options
    update_option( 'shopee_refresh_token', $refresh_token );
    update_option( 'shopee_access_token', $access_token );
}