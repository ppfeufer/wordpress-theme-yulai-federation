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
        <!--<div class="row">clearfix-->
        <div class="clearfix">
            <div class="col-md-12 breadcrumb-wrapper">
                <?php echo $breadcrumbNavigation; ?>
            </div><!--/.col -->
        </div><!--/.row -->
        <?php
    }
    ?>

    <!--<div class="row">-->
    <div class="main-content clearfix">
        <div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getMainContentColClasses(); ?>">
            <div class="content single">
                <?php
                if(\have_posts()) {
                    while(\have_posts()) {
                        \the_post();
                        \get_template_part('content-single');
                    }
                }
                ?>
            </div> <!-- /.content -->
        </div> <!-- /.col-lg-9 /.col-md-9 /.col-sm-9 /.col-9 -->

        <?php
        if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-post') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
            ?>
            <div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
                <?php
                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                    \get_sidebar('general');
                }

                if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->hasSidebar('sidebar-post')) {
                    \get_sidebar('post');
                }
                ?>
            </div><!--/.col -->
            <?php
        }
        ?>
    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php
\get_footer();
