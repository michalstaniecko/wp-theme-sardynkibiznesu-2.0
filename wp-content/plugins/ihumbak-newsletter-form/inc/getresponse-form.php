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
    'submit' => "Zapisz",
    'campaign_token' => 'VjKON',
    'image' => 'https://sardynkibiznesu.pl/wp-content/uploads/2020/11/strefa-sardynek-baner.jpg'
  ), $atts);
  ob_start();
  ?>
  <div style=""
       class=" single-newsletter-form <?= $a['type'] == 'widget' ? 'single-newsletter-form--widget mb-0' : 'mb-5' ?>">
    <?php if ($a['baner']): ?>

    <img src="<?= $a['image'] ?>" alt="" style="margin-bottom: 15px;width: 100%" />

    <?php endif; ?>
    <?php if ($a['type'] == 'widget' && !$a['title']): ?>
      <p>Zapisz się do newslettera!</p>
    <?php endif; ?>

    <?php if ($a['title']): ?>
      <p class="single-newsletter-form-title"><?= $a['title'] ?></p>
    <?php endif; ?>
    <?php if ($content): ?>
      <p class="single-newsletter-form-small"><?= $content ?></p>
    <?php endif; ?>
    <form action="https://app.getresponse.com/add_subscriber.html" accept-charset="utf-8" method="post">


      <div class="row mb-0">
        <div class="<?= $a['type'] == 'widget' ? 'col-md-12 mb-3' : 'col-md-6' ?>">

          <input type="text" name="first_name" placeholder="<?= $a['name_placeholder'] ?>" class="form-control w-100 mb-3 mb-md-0"/>
        </div>
        <div class="<?= $a['type'] == 'widget' ? 'col-md-12' : 'col-md-6' ?>">

          <input type="text" name="email" placeholder="<?= $a['email_placeholder'] ?>" class="form-control w-100 mb-0"/>
        </div>
        <div class="col-md-12 mt-3">

          <button type="submit" class=" w-100 button"><?= $a['submit'] ?></button>
        </div>
      </div>
      <input type="hidden"
             name="campaign_token"
             value="<?= $a['campaign_token'] ?>"/>
      <input
        type="hidden" name="start_day" value="0"/>
      <input type="hidden" name="thankyou_url" value="<?= $a['thankyou_url'] ?>"/>
    </form>
    <?php if ($a['about_url']): ?>

      <div class="" style="margin-top: 10px">
        <a href="<?= $a['about_url'] ?>"><strong><?= $a['about_label'] ?></strong></a>
      </div>

    <?php endif; ?>
  </div>
  <?php
  $html = ob_get_clean();

  return $html;
}
