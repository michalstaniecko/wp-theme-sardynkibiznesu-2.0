<?php
add_filter('the_content', 'single_newsletter_form');
add_action('wp_footer', 'single_newsletter_form_modals');

function single_newsletter_form_modals() {
  ?>

  <div class="single-newsletter-form-modal">
    <div class="modal fade notification error">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Przepraszamy. Błąd podczas zapisu</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12">
                <p>Wystąpił błąd podczas zapisu do newslettera.</p>
                <p>Sprawdź poprawność danych i spróbuj ponownie.</p>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="" data-dismiss="modal">Zamknij</button>

          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
}

function single_newsletter_form($content) {
  ob_start();
  ?>
  <div class="single-newsletter-form">
    <div class="row mb-0">
      <div class="col-md-6">
        <h3 class="title-normal mt-0"
            style="color: #993300"><?php _e('Podobał Ci się ten artykuł?', 'ihumbak-newsletter-form'); ?></h3>
        <p
          style="font-size: 14px; font-weight: 400; line-height: 21px;"><?php _e('Zapisz się do newslettera, otrzymuj informacje o nowych artykułach, odbierz dostęp do ponad 60 wzorów dokumentów, szablonów, grafik i Exceli.', 'ihumbak-newsletter-form'); ?></p>
      </div>
      <div class="col-md-6">
        <form action="#" method="post" class="getresponse-form mb-0">
          <div class="form-group mb-0">
            <div class="input input-group">
              <div class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></div>
              <input class="form-control" type="text" name="name" id="newsletter-name"
                     placeholder="<?php _e('Imię', 'ihumbak-newsletter-form'); ?>" required/>
            </div>
          </div>
          <div class="form-group mb-0">
            <div class="input input-group ">
              <div class="input-group-addon"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></div>
              <input class="form-control" type="email" name="email" id="newsletter-email" placeholder="E-mail"
                     required/>
            </div>
          </div>
          <input type="hidden" name="thankyou_url" value="<?php echo get_field('getresponse_default_thankyou','options') ?>"/>
          <input type="hidden" value="<?php echo get_field('getresponse_default_token', 'options') ?>" name="campaignId"/>
          <div class="input input-submit">
            <button type="submit" class="button w-100"><?php _e('Zapisz', 'ihumbak-newsletter-form') ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
  $newsletter = ob_get_clean();
  if (is_single()) {

    $content = $content . $newsletter;
  }

  return $content;
}

?>
