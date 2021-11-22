<?php

/**
 * Template Name: Boxed without sidebar
 */

get_header(); ?>
  <div class="page__wrapper">
    <div class="container-fluid container-fluid-stop page__content page__content--boxed-without-sidebar">
      <main class="main" itemprop="mainContentOfPage" itemscope="itemscope"
            itemtype="https://schema.org/Blog">
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
          <article class="article" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                   itemprop="blogPost">
            <div class="row">
              <div class="col-12">
                <h1 class="fw-bold text-center p-3 px-md-5 pt-md-5"><?php the_title(); ?></h1>
                <div class="entry-content article__content" itemprop="text">
                  <?php the_content(); ?>
                </div>
              </div>
            </div>
          </article>
        <?php endwhile; endif; ?>
        <?php if (comments_open()): ?>
          <div class="mt-5">
            <div class="h3" style="color: #800000"><?php _e('Leave comment', 'sb'); ?></div>
            <?php comments_template(); ?>
          </div>
        <?php endif; ?>
      </main>
    </div>
  </div>


<?php get_footer();
