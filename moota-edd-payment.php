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
const MOOTA_OPT_APIKEY = 'moota_api_key';
const MOOTA_OPT_APITIMEOUT = 'moota_api_timeout';

if (! function_exists('d')) {
    function d($var, $shouldReturn = null) {
        $shouldReturn = empty($shouldReturn) ? false : $shouldReturn;

        $dump = '<pre>' . var_export($var, true) . '</pre>';

        if ($shouldReturn) {
            return $dump;
        }

        echo $dump;
    }
}

if (! function_exists('dd')) {
    function dd($var) {
        d($var);
        wp_die();
    }
}

if (! function_exists('jdd')) {
    function jdd($var) {
        dd(json_encode($var, JSON_PRETTY_PRINT));
        wp_die();
    }
}

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

    $mootaApiKeyDesc = '<br>' . (
        $isTestMode
            ? ___('Dalam mode testing (sandbox), menggunakan api key sandbox')
            : ___('Dapatkan API Key melalui: ')
                . '<a href="https://app.moota.co/settings?tab=api" '
                . 'target="_new">https://app.moota.co/settings?'
                . 'tab=api</a>'
    );

    $apiKeySettings = array(
        'id' => MOOTA_OPT_APIKEY,
        'name' => ___('*Api Key'),
        'desc' => $mootaApiKeyDesc,
        'type' => 'text',
        'size' => 'regular',
        'value' => $mootaApiKey,
    );

    if ($isTestMode) {
        $apiKeySettings['disabled'] = 'disabled';
    } else {
        $apiKeySettings['required'] = 'required';
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
            'desc' => _desc('API Timeout (dalam detik)'),
            'type' => 'text',
            'size' => 'regular',
            'value' => edd_get_option(MOOTA_OPT_APITIMEOUT),
        ),
        array(
            'id' => 'moota_push_notif_url',
            'name' => ___('Push Notif Callback url'),
            'desc' => '<br>URL: <code>' . get_site_url(
                    null,
                    '/wp-admin/admin-ajax.php?action=moota_push'
                ) . '</code>'
                . _desc(
                    'Masuk halaman edit bank di moota > tab notifikasi '
                        . '> edit "API Push Notif" '
                        . '> lalu masukkan url ini'
                )
            ,
            'type' => 'text',
            'size' => 'regular',
            'disabled' => 'disabled',
        ),
    );

    // echo '<pre>', var_export($gatewaySettings, true), '</pre>'; wp_die();

    return $gatewaySettings;
}, 2, 1);

add_action('edd_settings_sections_gateways', function ($sections) {
    return array_merge($sections, array(
        'moota' => 'MootaPay'
    ));
});

register_activation_hook( __FILE__, function () {
    $edd_options = moota_init_options();
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

// MootaPay does not need a CC form, so remove it.
add_action('edd_moota_cc_form', '__return_false');
