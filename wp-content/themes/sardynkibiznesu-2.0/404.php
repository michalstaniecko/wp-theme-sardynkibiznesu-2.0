<?php get_header(); ?>
  <div class="page__wrapper">
    <div class="breadcrumbs">
      <div class="container-fluid container-fluid-stop">
        <?php
        if (function_exists('yoast_breadcrumb')) {
          yoast_breadcrumb('<div class="breadcrumbs__wrapper">', '</div>');
        }
        ?>
      </div>
    </div>
    <div class="container-fluid container-fluid-stop page__content">
      <div class="row">
        <main class="col-12 col-md-8 col-lg-9 main" itemprop="mainContentOfPage" itemscope="itemscope"
              itemtype="https://schema.org/Blog">
          <article class="article" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                   itemprop="blogPost">
            <div class="row">
              <div class="col-12">
                <div class="entry-content article__content" itemprop="text">
                  <div class="h2">
                    Nie znaleziono szukanej strony. <a href="/">Wróć do strony głównej</a>.
                  </div>
                </div>
              </div>
            </div>
          </article>
        </main>
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>


<?php get_footer();
