<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>
    itemscope="itemscope" itemtype="https://schema.org/WebPage"
  >
    <?php do_action('after_body'); ?>
    <div class="wrap__all">
      <header class="container-fluid border-bottom header">
        <div class="container-fluid-stop">
          <div class="row">
            <div class="col d-flex align-items-center">
              <a href="/">
                <img alt="Sardynki Biznesu" class="header__logo"
                  src="https://sardynkibiznesu.pl/wp-content/uploads/2020/11/logo_sardynki_biznesu-e1604254608339.png"/>
              </a>
            </div>
            <div class="col-auto ms-auto navigation">
              <nav class="navigation__nav ">
                <?php wp_nav_menu(array(
                  "menu" => 'primary_menu',
                  "container_class" => "navigation__nav-container"
                )); ?>
              </nav>
              <div class="d-lg-none">
                <button class="navigation__toggle">
                  <span></span>
                  <span></span>
                  <span></span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>
