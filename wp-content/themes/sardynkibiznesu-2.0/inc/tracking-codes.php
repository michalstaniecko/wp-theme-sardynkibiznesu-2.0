<?php

add_action('wp_head', 'sb_facebook_pixel');
function sb_facebook_pixel() {
  $code = get_field('facebook_pixel', 'options');
  if (empty($code)) return false;

  ?>
  <!-- Meta Pixel Code -->
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
      t.async = !0;
      t.src = v;
      s = b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
      'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '<?php echo $code ?>');
    fbq('track', 'PageView');
  </script>
  <noscript><img height="1" width="1" style="display:none"
                 src="https://www.facebook.com/tr?id=<?php echo $code ?>&ev=PageView&noscript=1"
    /></noscript>
  <!-- End Meta Pixel Code -->

  <?php
}

add_action('wp_enqueue_scripts', 'sb_google_analytics_script');
function sb_google_analytics_script() {
  $code = get_field('google_analytics', 'options');
  if (empty($code)) return false;
  wp_enqueue_script('gtm-script', 'https://www.googletagmanager.com/gtag/js?id=' . $code, false, false, true);
}

add_action('wp_head', 'sb_google_analytics');
function sb_google_analytics() {
  $code = get_field('google_analytics', 'options');
  if (empty($code)) return false;

  ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', '<?php echo $code ?>');
    setTimeout(function () {
      gtag('event', 'Over 10 seconds', {

        'event_category': 'NoBounce',
      });
    });
  </script>

  <?php
}
