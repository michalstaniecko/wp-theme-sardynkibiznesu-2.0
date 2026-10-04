<?php
add_filter('the_content', 'single_newsletter_form');

/**
 * Render inline message templates for newsletter forms
 */
function newsletter_form_render_messages() {
  ?>
  <!-- Success Message -->
  <div class="newsletter-form__message newsletter-form__message--success" data-message="success">
    <div class="newsletter-form__message-content">
      <div class="newsletter-form__message-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="newsletter-form__message-text">
        <p class="newsletter-form__message-title"><?php _e('Dziękujemy za zapis!', 'ihumbak-newsletter-form'); ?></p>
        <p class="newsletter-form__message-description"><?php _e('Sprawdź swoją skrzynkę email i potwierdź zapis.', 'ihumbak-newsletter-form'); ?></p>
      </div>
    </div>
  </div>

  <!-- Error Message -->
  <div class="newsletter-form__message newsletter-form__message--error" data-message="error">
    <div class="newsletter-form__message-content">
      <div class="newsletter-form__message-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="newsletter-form__message-text">
        <p class="newsletter-form__message-title"><?php _e('Ups! Coś poszło nie tak', 'ihumbak-newsletter-form'); ?></p>
        <p class="newsletter-form__message-description"><?php _e('Wystąpił błąd. Sprawdź dane i spróbuj ponownie.', 'ihumbak-newsletter-form'); ?></p>
      </div>
    </div>
  </div>
  <?php
}

function single_newsletter_form($content) {
  switch (get_field('default_newsletter', 'options')) {
    case 'getresponse':
      $campaignId = get_field('getresponse_default_token', 'options');
      break;
    case 'mailerlite':
      $campaignId = get_field('mailerlite_default_token', 'options');
      break;
  }
  ob_start();
  ?>
  <div class="newsletter-form" data-newsletter-form>
    <div class="newsletter-form__content">
      <div class="newsletter-form__header">
        <h3 class="newsletter-form__title"><?php _e('Podobał Ci się ten artykuł?', 'ihumbak-newsletter-form'); ?></h3>
        <p class="newsletter-form__description"><?php _e('Zapisz się do newslettera, otrzymuj informacje o nowych artykułach, odbierz dostęp do ponad 60 wzorów dokumentów, szablonów, grafik i Exceli.', 'ihumbak-newsletter-form'); ?></p>
      </div>
    </div>
    <form class="newsletter-form__form getresponse-form" action="#" method="post">
      <input class="newsletter-form__input" type="text" name="name" id="newsletter-name"
             placeholder="<?php _e('Imię', 'ihumbak-newsletter-form'); ?>" required/>
      <input class="newsletter-form__input" type="email" name="email" id="newsletter-email"
             placeholder="E-mail" required/>
      <input type="hidden" name="thankyou_url"
             value="<?php echo get_field('getresponse_default_thankyou', 'options') ?>"/>
      <input type="hidden" value="<?php echo $campaignId ?>"
             name="campaignId"/>
      <button type="submit" class="newsletter-form__submit"><?php _e('Zapisz się', 'ihumbak-newsletter-form') ?></button>
    </form>
    <?php newsletter_form_render_messages(); ?>
  </div>
  <?php
  $newsletter = ob_get_clean();
  if (is_single()) {

    $content = $content . $newsletter;
  }

  return $content;
}

?>
