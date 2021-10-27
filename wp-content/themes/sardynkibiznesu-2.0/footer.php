<footer class="footer">
  <div class="footer__main">
    <div class=" container-fluid container-fluid-stop">
      <div class="row">
        <div class="col">
          <?php if (is_active_sidebar('footer_about_us')) { ?>
            <?php dynamic_sidebar('footer_about_us'); ?>
          <?php } ?>
        </div>
        <div class="col">

          <?php if (is_active_sidebar('footer_nav_menu')) { ?>
            <?php dynamic_sidebar('footer_nav_menu'); ?>
          <?php } ?>
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
