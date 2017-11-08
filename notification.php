<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// $id = 696;

// $post = WP_Post::get_instance($id);

// $dl = new EDD_Download($id);

// $price = $dl->get_price();

$eddPayment = array(
    'ID' => 696,
    '_ID' => 696,
    'new' => false,
    'number' => 696,
    'mode' => 'test',
    'key' => '0dbfe3bfed0db0711f9ac16f50409c6a',
    'total' => '30.00',
    'subtotal' => 30,
    'tax' => '0',
    'discounted_amount' => 0,
    'tax_rate' => '0',
    'fees' =>  array (),
    'fees_total' => 0,
    'discounts' => 'none',
    'date' => '2017-11-08 08:58:00',
    'completed_date' => false,
    'status' => 'pending',
    'post_status' => 'pending',
    'old_status' => '',
    'status_nicename' => 'Pending',
    'customer_id' => '1',
    'user_id' => '0',
    'first_name' => 'Kacau',
    'last_name' => 'Lumbantoruan',
    'email' => 'aprilkacau@gmail.com',
    'user_info' => array (
     'first_name' => 'Kacau',
     'last_name' => 'Lumbantoruan',
     'discount' => 'none',
     'email' => 'aprilkacau@gmail.com',
     'address' => array (
       'line1' => '',
       'line2' => '',
       'city' => '',
       'zip' => '',
       'country' => '',
       'state' => '',
     ),
   ),
    'payment_meta' => array (
     'key' => '0dbfe3bfed0db0711f9ac16f50409c6a',
     'email' => 'aprilkacau@gmail.com',
     'date' => '2017-11-08 08:58:00',
     'user_info' => array (
       'email' => 'aprilkacau@gmail.com',
       'first_name' => 'Kacau',
       'last_name' => 'Lumbantoruan',
       'discount' => 'none',
       'address' => array (
         'line1' => '',
         'line2' => '',
         'city' => '',
         'zip' => '',
         'country' => '',
         'state' => '',
       ),
     ),
     'downloads' => array (
       array (
         'id' => 67,
         'quantity' => 1,
         'options' =>
         array (
           'quantity' => 1,
           'price_id' => '2',
         ),
       ),
     ),
     'cart_details' => array (
       array (
         'discount' => 0,
         'fees' =>
         array (
         ),
         'id' => 67,
         'item_number' =>
         array (
           'id' => 67,
           'quantity' => 1,
           'options' =>
           array (
             'quantity' => 1,
             'price_id' => '2',
           ),
         ),
         'item_price' => '30.00',
         'name' => 'Another Sample Product',
         'price' => 30,
         'quantity' => 1,
         'subtotal' => 30,
         'tax' => '0.00',
       ),
     ),
     'fees' => array (
     ),
     'currency' => 'USD',
   ),
    'address' => array (
     'line1' => '',
     'line2' => '',
     'city' => '',
     'country' => '',
     'state' => '',
     'zip' => '',
   ),
    'transaction_id' => '',
    'downloads' => array (
     array (
       'id' => 67,
       'quantity' => 1,
       'options' => array (
         'quantity' => 1,
         'price_id' => '2',
       ),
     ),
   ),
    'ip' => '::1',
    'gateway' => 'paypal',
    'currency' => 'USD',
    'cart_details' => array (
     array (
       'discount' => 0,
       'fees' => array (
       ),
       'id' => 67,
       'item_number' => array (
         'id' => 67,
         'quantity' => 1,
         'options' =>
         array (
           'quantity' => 1,
           'price_id' => '2',
         ),
       ),
       'item_price' => '30.00',
       'name' => 'Another Sample Product',
       'price' => 30,
       'quantity' => 1,
       'subtotal' => 30,
       'tax' => '0.00',
     ),
   ),
    'has_unlimited_downloads' => false,
    'pending' => array (
   ),
    'parent_payment' => 0,
);

jdd($eddPayment);

$query = new EDD_Payments_Query(array(
    'status' => 'pending',
));

$payments = $query->get_payments();
// $payments = json_encode($payments, JSON_PRETTY_PRINT);

// $downloads = array();

// foreach ($query['posts'] as $post) {
//     $downloads[] = $post;
// }

dd($payments[0]);

dd(compact(
    // 'id', 'post', 'dl', 'price'
    'payments'
));
