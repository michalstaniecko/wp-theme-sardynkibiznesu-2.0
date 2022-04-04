<?php

/*
 * Template Name: Listing with recommended
 */

get_header(); ?>
  <div class="page__wrapper page__content--listing-recommended page__content--with-background">
    <div class="container-fluid container-fluid-stop page__content ">
      <main class="main" itemprop="mainContentOfPage" itemscope="itemscope"
            itemtype="https://schema.org/Blog">
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
          <?php $cards = get_field('card'); ?>
          <article class="article p-4" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                   itemprop="blogPost">
            <h1 class="fw-bold text-center p-4"><?php the_title(); ?></h1>
            <div class="row mb-n4">
              <?php if (!empty($cards)): ?>
                <?php foreach ($cards as $card): ?>
                  <div class="col-sm-6 col-lg-4 pb-4">
                    <?php get_template_part('components/product-card', '', $card); ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </article>
        <?php endwhile; endif; ?>
      </main>
    </div>
  </div>


<?php get_footer();
