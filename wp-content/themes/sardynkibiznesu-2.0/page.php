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
          <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <article class="article" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                     itemprop="blogPost">
              <div class="row">
                <div class="col-12">
                  <a href="<?php echo get_the_post_thumbnail_url(null, 'full'); ?>" class="article__image-wrapper">
                    <img
                      src="<?php echo get_the_post_thumbnail_url(null, 'full'); ?>"
                      class="article__image"
                      alt=""
                    />
                  </a>
                </div>
                <div class="col-12">
                  <div class="entry-content article__content" itemprop="text">
                    <?php the_content(); ?>
                  </div>
                </div>
              </div>
            </article>
          <?php endwhile; endif; ?>

          <div class="mt-5">
            <div class="h3" style="color: #800000"><?php _e('Leave comment', 'sb'); ?></div>
            <?php comments_template(); ?>
          </div>
        </main>
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>


<?php get_footer();
