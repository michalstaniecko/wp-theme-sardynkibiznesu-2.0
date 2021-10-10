<?php
/**
 *
 * Plugin Name: iHumbak Analytics
 *
 */

add_action( 'wp_head', 'ga_code' );
add_action( 'wp_head', 'convertiser_verification' );
function ga_code() {
  ?>
  <!-- Facebook Pixel Code -->
  <script>
    !function (f, b, e, v, n, t, s) {
      if (f.fbq) return;
      n = f.fbq = function () {
        n.callMethod ?
          n.callMethod.apply(n, arguments) : n.queue.push(arguments)
      };
      if (!f._fbq) f._fbq = n;
      n.push = n;
      n.loaded = !0;
      n.version = '2.0';
      n.queue = [];
      t = b.createElement(e);
      t.defer = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
      'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '2076220602684583');
    fbq('track', 'PageView');
  </script>

  <!-- End Facebook Pixel Code -->

  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script defer src="https://www.googletagmanager.com/gtag/js?id=UA-62814019-14"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-62814019-14');
    setTimeout(function () {
      gtag('event', 'Over 10 seconds', {

        'event_category': 'NoBounce',
      });
    });
  </script>

  <meta name="ir-site-verification-token" value="240681581"/>

  <?php
}


function convertiser_verification() {
  if (is_front_page() || is_home()) {
    echo '<!-- convertiser-verification: b55eaaf8ee0ce6ea653defdd0c523d39fd047abc -->';
  }
}
