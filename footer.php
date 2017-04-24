<?php defined('ABSPATH') or die(); ?>

		</main>
		<footer>
			<div class="footer-wrapper">
				<div class="row">
					<!--<div class="footer-divider"></div>-->
					<div class="container container-footer">
						<?php
						if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-1') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-2') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-3') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-4')) {
							\get_sidebar('footer');
						} // END if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-1') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-2') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-3') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('footer-column-4'))
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
									<?php
//									$options = \get_option('yulai_theme_options', \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getThemeDefaultOptions());
//
//									if($options['footertext'] != '') {
//										echo '<p>';
//										echo stripslashes($options['footertext']);
//										echo '</p>';
//									} else {
										?>
										<ul class="credit">
											<li>&copy; <?php echo date('Y'); ?> <a href="<?php echo \esc_url(\home_url()); ?>"><?php \bloginfo(); ?></a></li>
											<!--<li><?php \_e('Proudly powered by ', 'yulai-federation') ?> <a href="<?php echo \esc_url(\__('http://wordpress.org/', 'yulai-federation')); ?>" ><?php \_e('WordPress', 'yulai-federation') ?></a>.</li>-->
											<li>(<?php \printf(\__('Design and Programming by Rounon Dax', 'yulai-federation')); ?>)</li>
										</ul><!-- end .credit -->
										<?php
//									} // END if($options['footertext'] != '')
									?>
								</div>

								<div class="footer-menu-wrapper">
									<?php
									if(\has_nav_menu('footer-menu')) {
										\wp_nav_menu(array(
											'menu' => '',
											'theme_location' => 'footer-menu',
											'depth' => 1,
											'container' => false,
											'menu_class' => 'footer-menu footer-navigation',
											'fallback_cb' => '\WordPress\Themes\YulaiFederation\Addons\BootstrapMenuWalker::fallback',
											'walker' => new \WordPress\Themes\YulaiFederation\Addons\BootstrapMenuWalker
										));
									} // END if(has_nav_menu('footer-menu'))
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
								<p>EVE Online and the EVE logo are the registered trademarks of CCP hf. All rights are reserved worldwide. All other trademarks are the property of their respective owners. EVE Online, the EVE logo, EVE and all associated logos and designs are the intellectual property of CCP hf. All artwork, screenshots, characters, vehicles, storylines, world facts or other recognizable features of the intellectual property relating to these trademarks are likewise the intellectual property of CCP hf. <!-- CCP hf. has granted permission to the Yulai Federation to use EVE Online and all associated logos and designs for promotional and information purposes on its website but does not endorse, and is not in any way affiliated with, the Yulai Federation. CCP is in no way responsible for the content on or functioning of this website, nor can it be liable for any damage arising from the use of this website. --></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<a href="#pagetop" tabindex="-1" class="totoplink">
				<i class="icon icon-totop"></i>
				<span class="sr-hint">
					<?php \_e('back to top', 'yulai-federation'); ?>
				</span>
			</a>
		</footer>
		<?php \wp_footer(); ?>
	</body>
</html>
