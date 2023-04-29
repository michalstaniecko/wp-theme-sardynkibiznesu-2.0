<footer class="footer">
  <div class="footer__main">
    <div class=" container-fluid container-fluid-stop">
      <div class="row">
        <div class="col-md-6 col-lg">
          <?php if (is_active_sidebar('footer_about_us')) { ?>
            <?php dynamic_sidebar('footer_about_us'); ?>
          <?php } ?>
        </div>
        <div class="col-md-6 col-lg-auto footer__menu">
          <a href="/start/"><strong>Sklep:</strong></a>
          <ul>
            <li>
              <a href="/regulamin-sklepu/">Regulamin sklepu i strony www</a>
            </li>
            <li>
              <a href="/case-study-reklama-lokalnej-firmy-na-facebooku/">Case Study - Reklama na Facebooku</a>
            </li>
            <li>
              <a href="/umowy/">Wzory umów dla małej firmy</a>
            </li>
          </ul>
        </div>
        <div class="col-md-6 col-lg-auto footer__menu">
          <strong>Darmowe materiały:</strong>
          <ul>
            <li>
              <a href="/polityka-prywatnosci-dla-strony-bloga-pobierz-wzor/">Darmowa polityka prywatności</a>
            </li>
            <li>
              <a href="/zapis-na-newsletter/">Newsletter</a>
            </li>
            <li>
              <a href="/strefa-sardynek/">Strefa Sardynek</a>
            </li>
          </ul>
        </div>
        <div class="col-md-6 col-lg-auto footer__menu">
          <strong>O nas:</strong>
          <ul>
            <li>
              <a href="/kontakt/">Kontakt</a>
            </li>
            <li>
              <a href="/blogu/">O blogu</a>
            </li>
            <li>
              <a href="/tu-zacznij/">Tu zacznij</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer__bottom">
    <div class=" container-fluid container-fluid-stop" itemscope="itemscope" itemtype="https://schema.org/WPFooter">
      <?php if (is_active_sidebar('footer_bottom_bar')) { ?>
        <?php dynamic_sidebar('footer_bottom_bar'); ?>
      <?php } ?>
    </div>
  </div>
</footer>

</div>
<div class="overflow"></div>
<div class="scroll-to-top"><i class="fa fa-chevron-up"></i></div>
<?php wp_footer(); ?>

</body>
</html>
