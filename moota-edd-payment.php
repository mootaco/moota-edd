<?php

require_once __DIR__ . '/lib/vendor/autoload.php';
require_once __DIR__ . '/functions.php';

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
const MOOTA_OPT_APIKEY = 'moota_api_key';
const MOOTA_OPT_APITIMEOUT = 'moota_api_timeout';

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
add_filter('edd_settings_gateways', function($gatewaySettings) {
    $mootaApiKey = edd_get_option(MOOTA_OPT_APIKEY);

    $isTestMode = edd_is_test_mode();

    $mootaApiKey = $isTestMode ? 'testing' : $mootaApiKey;

    $mootaSettingsUrl = 'https://app.moota.co/settings?tab=api';
    $mootaApiKeyDesc = '<br>' . (
        $isTestMode
            ? ___('Dalam mode testing (sandbox), menggunakan api key sandbox')
            : ___('Dapatkan API Key melalui: ')
                . '<a href="' . $mootaSettingsUrl . '" '
                . 'target="_new">' . $mootaSettingsUrl . '</a>'
    );

    $apiKeySettings = array(
        'id' => MOOTA_OPT_APIKEY,
        'name' => ___('*Api Key'),
        'desc' => $mootaApiKeyDesc,
        'type' => 'text',
        'size' => 'large',
    );

    if ($isTestMode) {
        $apiKeySettings['faux'] = true;
        $apiKeySettings['std'] = $mootaApiKey;
    } else {
        $apiKeySettings['required'] = 'required';
        $apiKeySettings['value'] = $mootaApiKey;
    }


    $gatewaySettings['moota'] = array(
        array(
            'id' => 'moota_header',
            'name' => _s('MootaPay Settings'),
            'desc' => _desc('Configure the gateway settings'),
            'type' => 'header',
        ),
        $apiKeySettings,
        array(
            'id' => MOOTA_OPT_APITIMEOUT,
            'name' => ___('Api Timeout'),
            'desc' => ___('API Timeout (dalam detik)'),
            'type' => 'text',
            'size' => 'small',
            'value' => edd_get_option(MOOTA_OPT_APITIMEOUT),
        ),
        array(
            'id' => 'moota_push_notif_url',
            'name' => ___('Push Notif Callback url'),
            'desc' => _desc(
                'Masuk halaman edit bank di moota > tab notifikasi '
                    . '> edit "API Push Notif" '
                    . '> lalu masukkan url ini'
            ),
            'type' => 'text',
            'size' => 'large',
            'std' => get_site_url(
                null,
                '/wp-admin/admin-ajax.php?action=moota_push'
            ),
            'faux'     => true,
        ),
    );

    return $gatewaySettings;
}, 2, 1);

add_action('edd_settings_sections_gateways', function ($sections) {
    return array_merge($sections, array(
        'moota' => 'MootaPay'
    ));
});

// MootaPay does not need a CC form, so remove it.
// edd_<name>_cc_form is only executed when
// `name` is the selected payment method
add_action('edd_moota_cc_form', '__return_false');

register_activation_hook( __FILE__, function () {
    moota_init_options();
} );

function moota_init_options() {
    if ( empty( edd_get_option( MOOTA_OPT_APITIMEOUT ) ) ) {
        edd_update_option(MOOTA_OPT_APITIMEOUT, 30);
    }
}

// `wp_ajax_nopriv_*` is for non logged-in user
add_action('wp_ajax_nopriv_moota_push', function () {
    require_once __DIR__ . '/notification.php';
});
