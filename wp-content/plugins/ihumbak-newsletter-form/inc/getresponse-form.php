<?php

add_shortcode('sardynki_getresponse_form', 'sardynki_getresponse_form');
function sardynki_getresponse_form($atts, $content)
{
  $a = shortcode_atts(array(
    'type' => '',
    'title' => false,
    'name_placeholder' => 'Imię',
    'email_placeholder' => 'Adres e-mail',
    'about_url' => false,
    'about_label' => '',
    'thankyou_url' =>'https://sardynkibiznesu.pl/zapis-na-newsletter/podziekowanie/',
    'baner' => false,
    'submit' => "Zapisz się",
    'campaign_id' => get_field('mailerlite_default_token', 'options'),
    'campaign_token' => get_field('getresponse_default_token', 'options'),
    'image' => 'https://sardynkibiznesu.pl/wp-content/uploads/2020/11/strefa-sardynek-baner.jpg'
  ), $atts);

  $is_widget = $a['type'] == 'widget';
  $has_content = $a['title'] || $is_widget || $content;
  $wrapper_class = 'newsletter-form'
    . ($is_widget ? ' newsletter-form--widget' : '')
    . (!$has_content ? ' newsletter-form--form-only' : '');

  ob_start();
  ?>
  <div class="<?= $wrapper_class ?>" data-newsletter-form>
    <?php if ($a['baner']): ?>
      <img src="<?= $a['image'] ?>" alt="" class="newsletter-form__banner" style="margin-bottom: 15px; width: 100%; border-radius: var(--sb-radius-card);" />
    <?php endif; ?>

    <?php if ($has_content): ?>
    <div class="newsletter-form__content">
      <div class="newsletter-form__header">
        <?php if ($a['title']): ?>
          <h3 class="newsletter-form__title"><?= $a['title'] ?></h3>
        <?php elseif ($is_widget): ?>
          <h3 class="newsletter-form__title">Zapisz się do newslettera!</h3>
        <?php endif; ?>

        <?php if ($content): ?>
          <p class="newsletter-form__description"><?= $content ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <form class="newsletter-form__form getresponse-form" action="#" method="post">
      <input type="text" required name="name" placeholder="<?= $a['name_placeholder'] ?>" class="newsletter-form__input"/>
      <input type="email" required name="email" placeholder="<?= $a['email_placeholder'] ?>" class="newsletter-form__input"/>
      <input type="hidden" name="campaignId" value="<?= $a['campaign_token'] ?>"/>
      <input type="hidden" name="thankyou_url" value="<?= $a['thankyou_url'] ?>"/>
      <button type="submit" class="newsletter-form__submit"><?= $a['submit'] ?></button>
    </form>

    <?php newsletter_form_render_messages(); ?>

    <?php if ($a['about_url']): ?>
      <div class="newsletter-form__footer" style="margin-top: var(--sb-space-3);">
        <a href="<?= $a['about_url'] ?>"><strong><?= $a['about_label'] ?></strong></a>
      </div>
    <?php endif; ?>
  </div>
  <?php
  $html = ob_get_clean();

  return $html;
}
