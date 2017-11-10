<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

use Moota\SDK\Config as MootaConfig;
use Moota\SDK\PushCallbackHandler;
use Moota\EDD\OrderFetcher;
use Moota\EDD\OrderMatcher;
use Moota\EDD\OrderFullfiler;

if (strtolower($_SERVER['REQUEST_METHOD']) !== 'post') {
    http_response_code(404);
    echo 'Only POST is allowed';
    wp_die();
}

$isTestMode = edd_is_test_mode();

MootaConfig::fromArray(array(
    'apiKey' => $isTestMode
        ? 'testing' : edd_get_option(MOOTA_OPT_APIKEY),
    'apiTimeout' => edd_get_option(MOOTA_OPT_APITIMEOUT),
    'sdkMode' => $isTestMode ? 'testing' : 'production',
));

$handler = PushCallbackHandler::createDefault()
    ->setOrderFetcher(new OrderFetcher)
    ->setOrderMatcher(new OrderMatcher)
    ->setOrderFullfiler(new OrderFullfiler)
;

$statusData = $handler->handle();

wp_send_json($statusData);
