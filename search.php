<?php
defined('ABSPATH') or die();

\get_header();
?>

<div class="container container-main">
    <?php
    $breadcrumbNavigation = \WordPress\Themes\YulaiFederation\Helper\NavigationHelper::getInstance()->getBreadcrumbNavigation();

    if(!empty($breadcrumbNavigation)) {
        ?>
        <!--
        // Breadcrumb Navigation
        -->
        <div class="row">
            <div class="col-md-12 breadcrumb-wrapper">
                <?php echo $breadcrumbNavigation; ?>
            </div><!--/.col -->
        </div><!--/.row -->
        <?php
    }
    ?>

    <div class="row main-content">
        <div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getMainContentColClasses(); ?>">
            <div class="content content-search">
                <?php
                if(\have_posts()) {
                    ?>
                    <header class="post-title">
                        <h1><?php \printf(\__('Search Results for: %s', 'yulai-federation'), '<span>' . \get_search_query() . '</span>'); ?></h1>
                    </header>
                    <?php
                } else {
                    ?>
                    <header class="post-title">
                        <h1><?php \_e('No Results Found', 'yulai-federation'); ?></h1>
                    </header>
                    <?php
                }

                if(\have_posts()) {
                    while(\have_posts()) {
                        \the_post();
                        \get_template_part('content', \get_post_format());
                    }
                } else {
                    ?>
                    <p class="lead">
                        <?php \_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps you should try again with a different search term.', 'yulai-federation'); ?>
                    </p>

                    <div class="well">
                        <?php \get_search_form(); ?>
                    </div><!--/.well -->
                    <?php
                }
                ?>
            </div> <!-- /.content -->
        </div> <!-- /.col -->

        <?php
        if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-page') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
            ?>
            <div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
                <?php
                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-page')) {
                    \get_sidebar('page');
                }

                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                    \get_sidebar('general');
                }
                ?>
            </div><!--/.col -->
            <?php
        }
        ?>
    </div> <!--/.row -->
</div><!-- container -->

<?php
\get_footer();
