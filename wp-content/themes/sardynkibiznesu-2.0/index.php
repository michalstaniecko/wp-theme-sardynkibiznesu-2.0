<?php
get_header();
$index = 0;
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
global $wp_query;
?>
  <div class="page__wrapper">
    <div class="container-fluid container-fluid-stop page__content">
      <div class="row">
        <main class="col-12 col-md-8 col-lg-9 main" itemprop="mainContentOfPage" itemscope="itemscope"
              itemtype="https://schema.org/Blog">
          <?php if (!empty(get_search_query())): ?>
            <div class="h3 mb-5">
              <strong><?php _e('Search query', 'sb') ?>:</strong> <?php echo get_search_query(); ?>
            </div>
          <?php endif; ?>
          <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <article class="article article--listing" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                     itemprop="blogPost">
              <div class="row">
                <div class="col-12 col-lg-auto">
                  <a href="<?php the_permalink(); ?>" class="article__image-wrapper <?php echo ($index === 0 ? 'no-lazy-loading' : '') ?> ">
                    <img
                      src="<?php echo get_the_post_thumbnail_url(null, 'full'); ?>"
                      class="article__image"
                      alt="<?php the_title(); ?>"
                      srcset="<?php echo wp_get_attachment_image_srcset(get_post_thumbnail_id()) ?>"
                    />
                  </a>
                </div>
                <div class="col-12 col-lg">
                  <header class="article__header">
                    <h2 class="post-title entry-title article__title" itemprop="headline">
                      <a
                        href="<?php the_permalink(); ?>" rel="bookmark"
                        title="Permanent Link: <?php the_title(); ?>">
                        <?php the_title(); ?>
                      </a>
                    </h2>

                    <div class="post-meta-infos ">
                      <time class="date-container minor-meta updated"><?php echo get_the_date(); ?></time>
                      <span class="text-sep text-sep-date">/</span><span class="comment-container minor-meta"><a
                          href="<?php the_permalink() ?>#respond"
                          class="comments-link"><?php printf(_n('%s comment', '%s comments', get_comments_number(), 'sb'), number_format_i18n(get_comments_number())); ?></a></span><span
                        class="text-sep text-sep-comment">/</span><span
                        class="blog-categories minor-meta"><?php _e('in', 'sb'); ?> <?php the_category(', '); ?></span><span
                        class="text-sep text-sep-cat">/</span><span
                        class="blog-author minor-meta"><?php _e('Author', 'sb'); ?> <span
                          class="entry-author-link"><span class="vcard author"><span class="fn"><a
                                href="<?php echo get_author_posts_url($post->post_author); ?>"
                                title="<?php _e('Posts by', 'sb'); ?> <?php the_author(); ?>"
                                rel="author"><?php the_author(); ?></a></span></span></span></span></div>
                  </header>
                  <div class="entry-content" itemprop="text">
                    <?php the_excerpt(); ?>
                    <p>
                      <a href="<?php the_permalink(); ?>" class="more-link">
                        <?php _e('Read more', 'sb') ?> >
                      </a>
                    </p>
                  </div>
                </div>
              </div>
            </article>
          <?php $index++; ?>
          <?php endwhile; ?>
            <div class="pagination">
              <div class="pagination__count me-3">
                <?php _e('Page', 'sb') ?> <?= $paged ?> z <?= $wp_query->max_num_pages ?>
              </div>
              <div class="pagination__nav">
                <?php echo paginate_links(array(
                  'current'   => $paged,
                  'prev_next' => false,
                  'prev_text' => false,
                  'next_text' => false
                )) ?>
              </div>
            </div>
          <?php endif; ?>
        </main>
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>

<?php get_footer();
