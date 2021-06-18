<?php
get_header();
$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
global $wp_query;
?>
  <div class="page__wrapper">
    <div class="container-fluid container-fluid-stop page__content">
      <div class="row">
        <main class="col-12 col-md-8 col-lg-9 main" itemprop="mainContentOfPage" itemscope="itemscope"
              itemtype="https://schema.org/Blog">
          <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <article class="article article--listing" itemscope="itemscope" itemtype="https://schema.org/BlogPosting"
                     itemprop="blogPost">
              <div class="row">
                <div class="col-12 col-lg-auto">
                  <a href="<?php the_permalink(); ?>" class="article__image-wrapper">
                    <img
                      src="<?php echo get_the_post_thumbnail_url(null, 'full'); ?>"
                      class="article__image"
                      alt=""
                      srcset="<?php echo get_the_post_thumbnail_url(null, 'article-mobile'); ?> 728w,
<?php echo get_the_post_thumbnail_url(null, 'article-tablet'); ?> 627w,
<?php echo get_the_post_thumbnail_url(null, 'article-desktop'); ?> 240w"
                      sizes="(min-width:992px) 10vw, 100vw"
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
                          href="<?php the_permalink() ?>/#respond"
                          class="comments-link"><?php printf(_n('%s comment', '%s comments', get_comments_number(), 'sb'), number_format_i18n(get_comments_number())); ?></a></span><span
                        class="text-sep text-sep-comment">/</span><span
                        class="blog-categories minor-meta"><?php _e('in', 'sb'); ?><?php the_category(', '); ?></span><span
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
                      <a href="<?php the_permalink(); ?>" class="more-link">Czytaj
                        dalej ></a>
                    </p>
                  </div>
                </div>
              </div>
            </article>
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
