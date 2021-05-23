<?php get_header(); ?>

  <div class="page__wrapper">
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
                    <div class="post-meta-infos ">
                      <time class="date-container minor-meta updated"><?php echo get_the_date(); ?></time>
                      <span class="text-sep text-sep-date">/</span><span class="comment-container minor-meta"><a
                          href="https://sardynkibiznesu.pl/podcasty/51-jak-byc-dobrym-szefem-2/#respond"
                          class="comments-link"><?php printf( _n( '%s comment', '%s comments', get_comments_number(), 'sb' ), number_format_i18n( get_comments_number() ) ); ?></a></span><span
                        class="text-sep text-sep-comment">/</span><span class="blog-categories minor-meta"><?php _e('in', 'sb'); ?> <a
                          href="https://sardynkibiznesu.pl/kategorie/podcasty/" rel="tag">Podcasty</a></span><span
                        class="text-sep text-sep-cat">/</span><span class="blog-author minor-meta"><?php _e('Author', 'sb'); ?> <span
                          class="entry-author-link"><span class="vcard author"><span class="fn"><a
                                href="<?php echo get_author_posts_url($post->post_author); ?>"
                                title="<?php _e('Posts by','sb'); ?> <?php the_author(); ?>"
                                rel="author"><?php the_author(); ?></a></span></span></span></span></div>
                  </header>
                  <div class="entry-content" itemprop="text">
                    <?php the_content(); ?>
                  </div>
                </div>
              </div>
            </article>
          <?php endwhile; endif; ?>

          <div>
            related posts
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
