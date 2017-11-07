<?php

require_once __DIR__ . '/lib/vendor/autoload.php';

/*
Plugin Name: Moota EDD Payment
Plugin URI: https://moota.co
Description: Moota Payment extension for Easy Digital Downloads (IDR Only)
Version: 1.0
Author: Moota
Author URI: https://moota.co
*/

const MOOTA_SETTING = 'edd_moota_settings';

const MOOTA_TOKEN = 'moota_user_token';
const MOOTA_SANDBOX = 'http://moota.matamerah.com';
const MOOTA_LIVE = 'https://app.moota.co';
const MOOTA_OPT_APIKEY = 'moota[api_key]';
const MOOTA_OPT_APITIMEOUT = 'moota[api_timeout]';

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
        return __($str, 'pw-edd');
    }
}

// registers the gateway
add_filter('edd_payment_gateways', function($gateways) {
    moota_init_options();

    $gateways['moota'] = array(
        'admin_label' => 'MootaPay',
        'checkout_label' => ___('MootaPay')
    );

    return $gateways;
});

// adds the settings to the Payment Gateways section
add_filter('edd_settings_gateways', function($settings) {
    $mootaOpts = edd_get_option('moota');

    return array_merge($settings, array(
        array(
            'id' => 'moota[header]',
            'name' => _s('MootaPay Settings'),
            'desc' => _desc('Configure the gateway settings'),
            'type' => 'header',
        ),
        array(
            'id' => MOOTA_OPT_APIKEY,
            'name' => ___('*Api Key'),
            'desc' => '<br>' . ___('Dapatkan API Key melalui: ')
                . '<a href="https://app.moota.co/settings?tab=api" '
                . 'target="_new">https://app.moota.co/settings?'
                . 'tab=api</a>',
            'type' => 'text',
            'size' => 'regular',
            'required' => 'required',
            'value' => $mootaOpts[ MOOTA_OPT_APIKEY ],
        ),
        array(
            'id' => MOOTA_OPT_APITIMEOUT,
            'name' => ___('Api Timeout'),
            'desc' => _desc('API Timeout (dalam detik)'),
            'type' => 'text',
            'size' => 'regular',
            'value' => $mootaOpts[ MOOTA_OPT_APITIMEOUT ],
        ),
        array(
            'id' => 'moota[push_notif_url]',
            'name' => ___('Push Notif Callback url'),
            'type' => 'text',
            'size' => 'regular',
            'disabled' => 'disabled',
            'readonly' => 'readonly',
        ),
    ));
});

register_activation_hook( __FILE__, function () {
    $edd_options = moota_init_options();
} );

function moota_init_options() {
    if (empty( edd_get_option('moota') )) {
        edd_update_option('moota', array(
            'api_key' => '',
            'api_timeout' => 30,
        ));
    }
}
