<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

use Moota\SDK\Config as MootaConfig;
use Moota\EDD\OrderFetcher;
use Moota\EDD\OrderMatcher;

if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
    http_response_code(404);
    echo 'Only POST is allowed';
    wp_die();
}

$isTestMode = edd_is_test_mode();

MootaConfig::fromArray(array(
    'apiKey' => $isTestMode
        ? 'testing' : edd_get_option(MOOTA_OPT_APIKEY),
    'sdkMode' => $isTestMode ? 'testing' : 'production',
    'serverAddress' => $isTestMode ? MOOTA_SANDBOX : MOOTA_LIVE,
));

$handler = Moota\SDK\PushCallbackHandler::createDefault()
    ->setTransactionFetcher(new OrderFetcher)
    ->setPaymentMatcher(new OrderMatcher)
;

$payments = $handler->handle();
$statusData = array(
    'status' => 'not-ok', 'error' => 'No matching order found'
);

if ( count( $payments ) > 0 ) {
    $savedCount = 0;
    foreach ($payments as $payment) {
        $payment['orderModel']->status = 'publish';
        $savedCount += $payment['orderModel']->save() ? 1 : 0;

        edd_set_payment_transaction_id(
            $payment['id'], $payment['transactionId']
        );

        $note = "Payment applied from Moota, MootaID: {$payment['mootaId']}"
            . ", amount: {$payment['mootaAmount']}";

        wp_insert_comment( wp_filter_comment( array(
            'comment_post_ID'      => $payment_id,
            'comment_content'      => $note,
            'user_id'              => 0,
            'comment_date'         => current_time( 'mysql' ),
            'comment_date_gmt'     => current_time( 'mysql', 1 ),
            'comment_approved'     => 1,
            'comment_parent'       => 0,
            'comment_author'       => '',
            'comment_author_IP'    => '',
            'comment_author_url'   => '',
            'comment_author_email' => '',
            'comment_type'         => 'edd_payment_note'
        ) ) );
    }

    $statusData = array('status' => 'ok', 'count' => count($savedCount));
}

wp_send_json($statusData);
