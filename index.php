<?php

use WordPress\Themes\YulaiFederation\Helper\NavigationHelper;
use WordPress\Themes\YulaiFederation\Helper\PostHelper;
use WordPress\Themes\YulaiFederation\Helper\ThemeHelper;

defined('ABSPATH') or die();

get_header();
?>

    <div class="container container-main">
        <?php
        $breadcrumbNavigation = NavigationHelper::getInstance()->getBreadcrumbNavigation();
        if (!empty($breadcrumbNavigation)) {
            ?>
            <!--
            // Breadcrumb Navigation
            -->
            <!--<div class="row">-->
            <div class="clearfix">
                <div class="col-md-12 breadcrumb-wrapper">
                    <?php echo $breadcrumbNavigation; ?>
                </div><!--/.col -->
            </div><!--/.row -->
            <?php
        }
        ?>

        <!--<div class="row main-content">-->
        <div class="main-content clearfix">
            <div class="<?php echo PostHelper::getInstance()->getMainContentColClasses(); ?> content-wrapper">
                <div class="content content-inner content-index content-loop">
                    <?php
                    if (have_posts()) {
                        $uniqueID = uniqid('', false);

                        if (get_post_type() === 'post') {
                            echo '<div class="gallery-row row">';
                            echo '<ul class="bootstrap-gallery bootstrap-post-loop-gallery bootstrap-post-loop-gallery-' . $uniqueID . ' clearfix">';
                        }

                        while (have_posts()) {
                            the_post();

                            if (get_post_type() === 'post') {
                                echo '<li>';
                            }

                            get_template_part('content', get_post_format());

                            if (get_post_type() === 'post') {
                                echo '</li>';
                            }
                        }

                        if (get_post_type() === 'post') {
                            echo '</ul>';
                            echo '</div>';

                            echo '<script>
                                jQuery(document).ready(function() {
                                    jQuery("ul.bootstrap-post-loop-gallery-' . $uniqueID . '").bootstrapGallery({
                                        "classes" : "' . PostHelper::getInstance()->getLoopContentClasses() . '",
                                        "hasModal" : false
                                    });
                                });
                                </script>';
                        }
                    } else {
                        // Do nothing apparently ...
                    }

                    if (function_exists('\wp_pagenavi')) {
                        wp_pagenavi();
                    } else {
                        NavigationHelper::getInstance()->getContentNav('nav-below');
                    }
                    ?>
                </div>
            </div><!--/.col -->

            <?php
            if (ThemeHelper::getInstance()->hasSidebar('sidebar-page') || ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                ?>
                <div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
                    <?php
                    if (ThemeHelper::getInstance()->hasSidebar('sidebar-general')) {
                        get_sidebar('general');
                    }

                    if (ThemeHelper::getInstance()->hasSidebar('sidebar-page')) {
                        get_sidebar('page');
                    }
                    ?>
                </div><!--/.col -->
                <?php
            }
            ?>
        </div> <!--/.row -->
    </div><!-- container -->

<?php
get_footer();
