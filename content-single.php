<?php defined('ABSPATH') or die(); ?>

<article id="post-<?php the_ID(); ?>" <?php \post_class('clearfix content-single'); ?>>
    <header class="entry-header">
        <h1 class="entry-title">
            <!--<a href="<?php \the_permalink(); ?>" title="<?php \printf(\esc_attr__('Permalink to %s', 'yulai-federation'), \the_title_attribute('echo=0')); ?>" rel="bookmark">-->
                <?php \the_title(); ?>
            <!--</a>-->
        </h1>
        <aside class="entry-details">
            <p class="meta">
                <?php
                echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getPostMetaInformation();
                \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getPostCategoryAndTags();
                \edit_post_link(\__('Edit', 'yulai-federation'));
                ?>
            </p>
        </aside><!--end .entry-details -->
    </header><!--end .entry-header -->

    <section class="post-content clearfix">
        <div class="entry-content clearfix">
            <?php
            echo \the_content();

            if(\function_exists('\WordPress\Themes\YulaiFederation\yf_link_pages')) {
                \WordPress\Themes\YulaiFederation\yf_link_pages([
                    'before' => '<ul class="pagination">',
                    'after' => '</ul>',
                    'before_link' => '<li>',
                    'after_link' => '</li>',
                    'current_before' => '<li class="active">',
                    'current_after' => '</li>',
                    'previouspagelink' => '&laquo;',
                    'nextpagelink' => '&raquo;'
                ]);
            } else {
                \wp_link_pages( [
                    'before' => '<div class="page-links">' . \__('Pages:', 'yulai-federation'),
                    'after'  => '</div>',
                ]);
            }
            ?>
        </div>
    </section>

    <?php
    // AUTHOR INFO
    if(\get_the_author_meta('description')) {
        ?>
        <hr/>
        <div class="author-info clearfix">
            <div class="author-details">
                <h3>
                    <?php
                    echo \__('Written by ', 'yulai-federation');
                    echo \get_the_author();
                    ?>
                </h3>
                <?php echo \get_avatar(\get_the_author_meta('user_email')); ?>
            </div><!-- end .author-details -->
            <div class="author-description">
                <?php echo \wpautop(\get_the_author_meta('description')); ?>
            </div>
        </div><!-- end .author-info -->
        <?php
    }
    ?>
    <hr/>
    <?php \WordPress\Themes\YulaiFederation\Helper\NavigationHelper::getInstance()->getArticleNavigation(true); ?>
    <hr/>
    <?php \comments_template(); ?>
</article><!-- /.post-->
