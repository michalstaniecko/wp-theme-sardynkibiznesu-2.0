<?php
/**
 *
 * Plugin Name: iHumbak Analytics
 *
 */

add_action( 'wp_head', 'ga_code' );
add_action( 'wp_head', 'convertiser_verification' );

add_action('wp_enqueue_scripts', 'ga_script');
function ga_script() {
  wp_enqueue_script('gtm-script', 'https://www.googletagmanager.com/gtag/js?id=UA-62814019-14', false, false,true);
}
function ga_code() {
  ?>
  <!-- Facebook Pixel Code -->
  <script>
    var eventID = Date.now().toString() + "<?php echo md5(uniqid(rand(), true)) ?>"
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
    fbq('track', 'PageView', {}, {eventID: eventID});
    window.addEventListener('load', function() {
      jQuery.ajax({
        method: "post",
        dataType:"json",
        url: '<?php echo admin_url('admin-ajax.php') ?>',
        data: {
          action: 'fcapi_page_view',
          sourceUrl: window.location.href,
          eventID: eventID
        }
      })
    })
  </script>

  <!-- End Facebook Pixel Code -->

  <!-- Global site tag (gtag.js) - Google Analytics -->
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

add_action('wp_head', 'shareasale_verification');
function shareasale_verification() {
  echo '<!-- A9768BAF-D66E-43D3-8973-0C2C9B224FF0 -->';
}
