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

    <div class="row main-top">
        <div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getMainContentColClasses(); ?>">
            <header>
                <h1>
                    <a href="<?php \the_permalink() ?>" rel="bookmark" title="<?php \the_title(); ?>"><?php \the_title(); ?></a>
                </h1>
            </header><!-- / header -->
        </div><!--/.col -->
    </div><!--/.row -->

    <div class="row main-content">
        <div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getMainContentColClasses(); ?> content-wrapper">
            <div class="content content-inner content-404">
                <header class="page-title">
                    <h1><?php \_e('This is Embarrassing', 'yulai-federation'); ?></h1>
                </header>

                <p class="lead">
                    <?php \_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching, or one of the links below, can help.', 'yulai-federation'); ?>
                </p>

                <div class="well">
                    <?php \get_search_form(); ?>
                </div>

                <h2><?php \_e('All Pages', 'yulai-federation'); ?></h2>

                <?php
                \wp_page_menu();
                \the_widget('WP_Widget_Recent_Posts');
                ?>

                <h2><?php \_e('Categories', 'yulai-federation'); ?></h2>

                <ul>
                    <?php
                    \wp_list_categories([
                        'orderby' => 'count',
                        'order' => 'DESC',
                        'show_count' => 1,
                        'title_li' => '',
                        'number' => 100
                    ]);
                    ?>
                </ul>
            </div> <!-- /.content -->
        </div> <!-- /.col -->

        <?php
        if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-page') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
            ?>
            <div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
                <?php
                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                    \get_sidebar('general');
                }

                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-page')) {
                    \get_sidebar('page');
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
