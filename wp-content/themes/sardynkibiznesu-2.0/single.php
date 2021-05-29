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
                  <div class="entry-content" itemprop="text">
                    <?php the_content(); ?>
                  </div>
                </div>
              </div>
            </article>
          <?php endwhile; endif; ?>
          <div class="nav-posts">
            <div class="row ">
              <div href="#" class="col d-flex">
                <?php if (get_next_post()): ?>
                  <?php $related_post = get_next_post(); ?>
                  <a href="<?php echo the_permalink($related_post->ID); ?>"
                     class="d-flex nav-posts__link"
                     title="<?php _e('Article', 'sb'); ?>: <?= $related_post->post_title ?>">
                    <div class="flex-shrink-0">
                      <img
                        src="<?php echo get_the_post_thumbnail_url($related_post->ID, 'article-desktop') ?>"
                        alt="<?php echo $related_post->post_title ?>"
                        class="nav-posts__image"
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
              <div href="#" class="col">
                <?php if (get_previous_post()): ?>
                  <?php $related_post = get_previous_post(); ?>
                  <a href="<?php the_permalink($related_post->ID); ?>"
                     class="d-flex nav-posts__link"
                     title="<?php _e('Article', 'sb'); ?>: <?= $related_post->post_title ?>">
                    <div class="flex-shrink-0">
                      <img
                        src="<?php echo get_the_post_thumbnail_url($related_post->ID, 'article-desktop') ?>"
                        alt="<?php echo $related_post->post_title ?>"
                        class="nav-posts__image"
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
          <div>
            <?php comments_template(); ?>
          </div>
        </main>
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>


<?php get_footer();
