<?php use WordPress\Themes\YulaiFederation\Addons\BootstrapMenuWalker;
use WordPress\Themes\YulaiFederation\Helper\ThemeHelper;

defined('ABSPATH') or die(); ?>

        </main>
        <footer>
            <div class="footer-wrapper">
                <div class="row">
                    <!--<div class="footer-divider"></div>-->
                    <div class="container container-footer">
                        <?php
                        if (ThemeHelper::getInstance()->hasSidebar('footer-column-1') || ThemeHelper::getInstance()->hasSidebar('footer-column-2') || ThemeHelper::getInstance()->hasSidebar('footer-column-3') || ThemeHelper::getInstance()->hasSidebar('footer-column-4')) {
                            get_sidebar('footer');
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="copyright-wrapper">
                <div class="row ">
                    <div class="container container-footer">
                        <div class="row copyright">
                            <div class="col-md-12">
                                <div class="pull-left copyright-text">
                                    <ul class="credit">
                                        <li>&copy; <?php echo date('Y'); ?> <a href="<?php echo esc_url(home_url()); ?>"><?php bloginfo(); ?></a>
                                        </li>
                                        <li>
                                            (<?php printf(__('Design and Programming by Rounon Dax', 'yulai-federation')); ?>)
                                        </li>
                                    </ul><!-- end .credit -->
                                </div>

                                <div class="footer-menu-wrapper">
                                    <?php
                                    if (has_nav_menu('footer-menu')) {
                                        wp_nav_menu([
                                            'menu' => '',
                                            'theme_location' => 'footer-menu',
                                            'depth' => 1,
                                            'container' => false,
                                            'menu_class' => 'footer-menu footer-navigation',
                                            'fallback_cb' => '\WordPress\Themes\YulaiFederation\Addons\BootstrapMenuWalker::fallback',
                                            'walker' => new BootstrapMenuWalker
                                        ]);
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="legal-wrapper">
                <div class="row ">
                    <div class="container container-footer">
                        <div class="row copyright">
                            <div class="col-md-12">
                                <h5>CCP Copyright Notice</h5>
                                <p>
                                    EVE Online and the EVE logo are the registered trademarks of
                                    CCP hf. All rights are reserved worldwide. All other
                                    trademarks are the property of their respective owners. EVE
                                    Online, the EVE logo, EVE and all associated logos and
                                    designs are the intellectual property of CCP hf. All
                                    artwork, screenshots, characters, vehicles, storylines,
                                    world facts or other recognizable features of the
                                    intellectual property relating to these trademarks are
                                    likewise the intellectual property of CCP hf.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#pagetop" tabindex="-1" class="totoplink">
                <i class="icon icon-totop"></i>
                <span class="sr-hint">
                    <?php _e('back to top', 'yulai-federation'); ?>
                </span>
            </a>
        </footer>
        <?php wp_footer(); ?>
    </body>
</html>
