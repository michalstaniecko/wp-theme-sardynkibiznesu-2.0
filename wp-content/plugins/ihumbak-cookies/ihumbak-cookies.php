<?php

/*
 * Plugin Name: iHumbak Cookies
 */

//add_action('wp_head', 'ihumbak_cookies_print_scripts', 10000);

function ihumbak_cookies_print_scripts()
{
    global $wp_scripts;
    if (!is_super_admin()) {
        return false;
    }

    print_r($wp_scripts);
}

add_action('wp_enqueue_scripts', 'ihumbakCookieConsentScript');

function ihumbakCookieConsentScript()
{
    wp_enqueue_script('ihumbakCookieConsentScript', plugins_url('dist/js/ihumbak-cookies.js', __FILE__), array(), '1.0.0');
    wp_enqueue_style('ihumbakCookieConsentStyle', plugins_url('dist/css/main.css', __FILE__), array(), '1.0.0', 'all');
}

add_filter('bpmj_eddcm_footer_html', 'ihumbakCookieConsentFooterHtml', 10, 1);
function ihumbakCookieConsentFooterHtml($html)
{

    $html .= '<br/>';
    $html .= '<a href="#" type="button" data-cc="show-preferencesModal">Zmień ustawienia cookies</a>';

    return $html;
}


//add_action('wp_footer', 'ihumbakCookieConsentScriptTest');

function ihumbakCookieConsentScriptTest()
{
    if (!is_super_admin()) {
        return false;
    }

    ?>
    <script
            type="text/plain"
            data-category="analytics"
            data-service="ihumbakCookieConsentScript" data-ihumbakCookieConsentScript
    >
        console.log('ihumbakCookieConsentScript');
    </script>
    <?php
}
