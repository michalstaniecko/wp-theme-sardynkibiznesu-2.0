<?php
add_action('wp_head', function() {
    print_r('dupa');
});
add_action('wp_head', 'sb_facebook_pixel');
function sb_facebook_pixel() {
  $code = get_field('facebook_pixel', 'options');
  if (empty($code)) return false;

  ?>
    <!-- Meta Pixel Code -->
    <script>
        (function () {
            var eventID = Date.now().toString() + "<?php echo md5(uniqid(rand(), true)) ?>"
            !function ( f, b, e, v, n, t, s ) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply( n, arguments ) : n.queue.push( arguments )
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement( e );
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName( e )[0];
                s.parentNode.insertBefore( t, s )
            }( window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js' );
            fbq( 'init', '<?php echo $code ?>' );
            fbq( 'track', 'PageView', {}, { eventID: eventID } );
            //window.addEventListener('load', function () {
            //  jQuery.ajax({
            //    method: "post",
            //    dataType: "json",
            //    url: '<?php //echo admin_url('admin-ajax.php') ?>//',
            //    data: {
            //      action: 'fcapi_page_view',
            //      sourceUrl: window.location.href,
            //      eventID: eventID
            //    }
            //  })
            //})
        })();

    </script>

    <!-- End Meta Pixel Code -->

  <?php
}


//add_action('wp_head', 'sb_google_analytics');
function sb_google_analytics() {
  $code = get_field('google_analytics', 'options');
  if (empty($code)) return false;

  ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push( arguments );
        }

        gtag( 'js', new Date() );

        gtag( 'consent', 'default', {
            ad_storage: "denied",
            ad_user_data: "denied",
            ad_personalization: "denied",
            analytics_storage: "denied",
            functionality_storage: "denied",
            personalization_storage: "granted",
            security_storage: "granted",
            wait_for_update: 2000,
        } );
        gtag( "set", "ads_data_redaction", true );
        gtag( "set", "url_passthrough", true );

        gtag( 'config', '<?php echo $code ?>' );

        setTimeout( function () {
            gtag( 'event', 'Over 10 seconds', {

                'event_category': 'NoBounce',
            } );
        }, 10000 );

    </script>

    <meta name="ir-site-verification-token" value="240681581"/>
  <?php
}


add_action('wp_head', 'convertiser_verification');
if (!function_exists('convertiser_verification')) {
  function convertiser_verification() {
    if (is_front_page() || is_home()) {
      echo '<!-- convertiser-verification: b55eaaf8ee0ce6ea653defdd0c523d39fd047abc -->';
    }
  }
}

add_action('wp_head', 'shareasale_verification');
if (!function_exists('shareasale_verification')) {
  function shareasale_verification() {
    echo '<!-- A9768BAF-D66E-43D3-8973-0C2C9B224FF0 -->';
  }
}

add_action('wp_head', 'sb_google_tag_manager');
function sb_google_tag_manager() {
  if (is_user_logged_in()) return false;
  $code = get_field('google_tag_manager', 'options');
  if (empty($code)) return false;

  ?>
    <!-- Google Tag Manager -->
    <script>
        (function ( w, d, s, l, i ) {
            w[l] = w[l] || [];
            w[l].push( {
                'gtm.start':
                    new Date().getTime(), event: 'gtm.js'
            } );
            var f = d.getElementsByTagName( s )[0],
                j = d.createElement( s ), dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore( j, f );
        })( window, document, 'script', 'dataLayer', '<?php echo $code ?>' );
    </script>
    <!-- End Google Tag Manager -->
  <?php
}

add_action('after_body', 'sb_google_tag_manager_noscript');
function sb_google_tag_manager_noscript() {
  if (is_user_logged_in()) return false;
  $code = get_field('google_tag_manager', 'options');
  if (empty($code)) return false;

  ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe
                src="https://www.googletagmanager.com/ns.html?id=<?php echo $code ?>"
                height="0" width="0" style="display:none;visibility:hidden"
        ></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
  <?php
}
