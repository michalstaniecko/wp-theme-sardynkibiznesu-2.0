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
          <?php
          if (have_posts()): while (have_posts()): the_post();
            $post_thumbnail_id = get_post_thumbnail_id($post->ID);
            $attachment = wp_get_attachment_image_src($post_thumbnail_id, 'single-desktop');
            $width = $attachment[1];
            $height = $attachment[2];
            ?>
            <article class="article" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                     itemprop="blogPost">
              <div class="row">
                <div class="col-12">
                  <a href="<?php echo get_the_post_thumbnail_url(null, 'single-desktop'); ?>"
                     class="article__image-wrapper">
                    <img
                      src="<?php echo get_the_post_thumbnail_url(null, 'single-desktop'); ?>"
                      srcset="<?php echo wp_get_attachment_image_srcset(get_post_thumbnail_id()) ?>"
                      class="article__image no-lazy-load"
                      alt="<?php the_title(); ?>"
                      width="<?php echo $width ?>"
                      height="<?php echo $height ?>"
                    />
                  </a>
                </div>
                <div class="col-12">
                  <header class="article__header">
                    <h1 class="post-title entry-title article__title" itemprop="headline">
                      <a
                        href="<?php the_permalink(); ?>" rel="bookmark"
                        title="Permanent Link: <?php the_title(); ?>">
                        <?php the_title(); ?>
                      </a>
                    </h1>
                    <div class="d-flex align-items-center border-bottom pb-3 pt-2">
                      <div class="flex-shrink-0">
                        <img src="<?= get_avatar_url($post->post_author) ?>" class="article__header-avatar"
                             width="96"
                             height="96"
                             alt="author: <?= get_user_by('id', $post->post_author)->display_name ?>"/>
                      </div>
                      <div class="post-meta-infos flex-grow-1 ms-3 mt-auto">
                        <time class="date-container minor-meta updated"><?php echo get_the_date(); ?></time>
                        <br/>
                        <span class="blog-author minor-meta"><?php _e('Author', 'sb'); ?> <span
                            class="entry-author-link"><span class="vcard author"><span class="fn"><a
                                  href="<?php echo get_author_posts_url($post->post_author); ?>"
                                  title="<?php _e('Posts by', 'sb'); ?> <?php the_author(); ?>"
                                  rel="author"><?php the_author(); ?></a></span></span></span></span></div>
                    </div>
                  </header>
                  <div class="entry-content article__content" itemprop="text">
                    <?php the_content(); ?>
                  </div>
                </div>
              </div>
            </article>
          <?php endwhile; endif; ?>
          <div class="nav-posts">
            <div class="row ">
              <div href="#" class="col-12 col-sm d-flex mb-3 mb-sm-0">
                <?php if (get_next_post()): ?>
                  <?php $related_post = get_next_post(); ?>
                  <?php
                  $post_thumbnail_id = get_post_thumbnail_id($related_post->ID);
                  $attachment = wp_get_attachment_image_src($post_thumbnail_id, 'article-desktop');
                  $width = $attachment[1];
                  $height = $attachment[2];
                  ?>
                  <a href="<?php echo the_permalink($related_post->ID); ?>"
                     class="d-flex nav-posts__link"
                     title="<?php _e('Article', 'sb'); ?>: <?= $related_post->post_title ?>">
                    <div class="flex-shrink-0">
                      <img
                        data-lazyloaded="1"
                        src="<?php echo get_the_post_thumbnail_url($related_post->ID, 'article-desktop') ?>"
                        alt="<?php echo $related_post->post_title ?>"
                        class="nav-posts__image"
                        width="<?php echo $width ?>"
                        height="<?php echo $height ?>"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <div class="nav-posts__label"><?php _e('Next article', 'sb'); ?></div>
                      <div class="nav-posts__title">
                        <?php echo $related_post->post_title ?>
                      </div>
                    </div>
                  </a>
                <?php endif; ?>
              </div>
              <div href="#" class="col-12 col-sm">
                <?php if (get_previous_post()): ?>
                  <?php $related_post = get_previous_post(); ?>
                  <?php
                  $post_thumbnail_id = get_post_thumbnail_id($related_post->ID);
                  $attachment = wp_get_attachment_image_src($post_thumbnail_id, 'article-desktop');
                  $width = $attachment[1];
                  $height = $attachment[2];
                  ?>
                  <a href="<?php the_permalink($related_post->ID); ?>"
                     class="d-flex nav-posts__link"
                     title="<?php _e('Article', 'sb'); ?>: <?= $related_post->post_title ?>">
                    <div class="flex-shrink-0">
                      <img
                        data-lazyloaded="1"
                        src="<?php echo get_the_post_thumbnail_url($related_post->ID, 'article-desktop') ?>"
                        alt="<?php echo $related_post->post_title ?>"
                        class="nav-posts__image"
                        width="<?php echo $width ?>"
                        height="<?php echo $height ?>"
                      />
                    </div>
                    <div class="flex-grow-1">
                      <div class="nav-posts__label"><?php _e('Previous article', 'sb'); ?></div>
                      <div class="nav-posts__title">
                        <?php echo $related_post->post_title ?>
                      </div>
                    </div>
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php if (comments_open()): ?>
            <div class="mt-5">
              <div class="h3" style="color: #800000"><?php _e('Leave comment', 'sb'); ?></div>
              <?php comments_template(); ?>
            </div>
          <?php endif; ?>
        </main>
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>


<?php get_footer();
