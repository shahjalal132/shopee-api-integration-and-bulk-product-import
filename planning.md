## Generate sign for required endpoints

## endpoints
 - /api/v2/product/get_item_list
 - /api/v2/product/get_item_base_info

 ## sign generation formula

### Base sting generator
partner_id, api path, timestamp, access_token, shop_id

## Encoding and generate hash

**Calculate the signature using the HMAC-SHA256 algorithm**

```php

// generate string
$string = $partner_id . $api_path . $timestamp . $access_token . $shop_id;

// generate hash
$hash = hash_hmac('sha256', $string, $key); // $key is partner key

```

## Sync order Work Flow

wp_sync_order_details table structure.

id
order_id
order_sn
order_status
order_details
status
woo_order_created
created_at
updated_at