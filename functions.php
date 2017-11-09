<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if (! function_exists('_s')) {
    function _s($str) { return "<strong>{$str}</strong>"; }
}

if (! function_exists('_desc')) {
    function _desc($str) {
        return '<br>' . ___($str);
    }
}

if (! function_exists('___')) {
    function ___($str) {
        return __($str, 'moota-edd');
    }
}

if (! function_exists('stupid_payment_to_array')) {
    function stupid_payment_to_array(\EDD_Payment $payment) {
        return array(
            'id' => $payment->ID,
            'number' => $payment->number,
            'mode' => $payment->mode,
            'key' => $payment->key,
            'total' => $payment->total,
            'date' => $payment->date,
            'completed_date' => $payment->completed_date,
            'status' => $payment->status,
            'customer_id' => $payment->customer_id,
            'user_id' => $payment->user_id,
            'first_name' => $payment->first_name,
            'last_name' => $payment->last_name,
            'email' => $payment->email,
            'payment_meta' => $payment->payment_meta,
            'address' => $payment->address,
            'transaction_id' => $payment->transaction_id,
            'downloads' => $payment->downloads,
            'ip' => $payment->ip,
            'gateway' => $payment->gateway,
            'currency' => $payment->currency,
            'cart_details' => $payment->cart_details,
        );
    }
}
