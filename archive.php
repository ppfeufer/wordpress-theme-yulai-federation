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
        <div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getMainContentColClasses(); ?> content-wrapper">
            <div class="content content-inner content-archive">
                <header class="page-title">
                    <h1>
                        <?php
                        if(\is_day()) {
                            \printf(\__('Daily Archives: %s', 'yulai-federation'), '<span>' . \get_the_date() . '</span>');
                        } elseif(is_month()) {
                            \printf(\__('Monthly Archives: %s', 'yulai-federation'), '<span>' . \get_the_date(\_x('F Y', 'monthly archives date format', 'yulai-federation')) . '</span>');
                        } elseif(is_year()) {
                            \printf(\__('Yearly Archives: %s', 'yulai-federation'), '<span>' . \get_the_date(\_x('Y', 'yearly archives date format', 'yulai-federation')) . '</span>');
                        } elseif(is_tag()) {
                            \printf(\__('Tag Archives: %s', 'yulai-federation'), '<span>' . \single_tag_title('', false) . '</span>');
                            // Show an optional tag description
                            $tag_description = \tag_description();

                            if($tag_description) {
                                echo \apply_filters('tag_archive_meta', '<div class="tag-archive-meta">' . $tag_description . '</div>');
                            }
                        } elseif(\is_category()) {
                            \printf(\__('Category Archives: %s', 'yulai-federation'), '<span>' . \single_cat_title('', false) . '</span>');
                        } else {
                            \_e('Blog Archives', 'yulai-federation');
                        }
                        ?>
                    </h1>
                    <?php
                    // Show an optional category description
                    $category_description = \category_description();

                    if($category_description) {
                        echo \apply_filters('category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>');
                    }
                    ?>
                </header>
                <?php
                if(\have_posts()) {
                    if(\get_post_type() === 'post') {
                        $uniqueID = \uniqid();

                        echo '<div class="gallery-row row">';
                        echo '<ul class="bootstrap-gallery bootstrap-post-loop-gallery bootstrap-post-loop-gallery-' . $uniqueID . ' clearfix">';
                    }

                    while(\have_posts()) {
                        \the_post();

                        if(\get_post_type() === 'post') {
                            echo '<li>';
                        }

                        \get_template_part('content', \get_post_format());

                        if(\get_post_type() === 'post') {
                            echo '</li>';
                        }
                    }

                    if(\get_post_type() === 'post') {
                        echo '</ul>';
                        echo '</div>';

                        echo '<script type="text/javascript">
                                jQuery(document).ready(function() {
                                    jQuery("ul.bootstrap-post-loop-gallery-' . $uniqueID . '").bootstrapGallery({
                                        "classes" : "' . \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getLoopContentClasses() . '",
                                        "hasModal" : false
                                    });
                                });
                                </script>';
                    }
                }

                if(\function_exists('wp_pagenavi')) {
                    \wp_pagenavi();
                } else {
                    \WordPress\Themes\YulaiFederation\Helper\NavigationHelper::getInstance()->getContentNav('nav-below');
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
