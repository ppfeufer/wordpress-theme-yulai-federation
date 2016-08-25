<?php
defined('ABSPATH') or die();

\get_header();
?>

<div class="container main">
	<div class="row">
		<div class="col-md-12">
			<?php
			if(\function_exists('\WordPress\Themes\YulaiFederation\yf_breadcrumbs')) {
				\WordPress\Themes\YulaiFederation\yf_breadcrumbs();
			} // END if(\function_exists('\YulaiFederation\yf_breadcrumbs'))
			?>
		</div><!--/.col -->
	</div><!--/.row -->

	<div class="row main-content">
		<div class="<?php echo \WordPress\Themes\YulaiFederation\yf_get_mainContentColClasses(); ?>">
			<div class="content content-index">
				<?php
				if(\have_posts()) {
					if(\get_post_type() === 'post') {
						$uniqueID = \uniqid();

						echo '<div class="gallery-row">';
						echo '<ul class="bootstrap-gallery bootstrap-post-loop-gallery bootstrap-post-loop-gallery-' . $uniqueID . ' clearfix">';
					} // END if(\get_post_type() === 'post')

					while(\have_posts()) {
						\the_post();

						if(\get_post_type() === 'post') {
							echo '<li>';
						}
						\get_template_part('content', \get_post_format());
						if(\get_post_type() === 'post') {
							echo '</li>';
						}
					} // END while (have_posts())

					if(\get_post_type() === 'post') {
						echo '</ul>';
						echo '</div>';

						echo '<script type="text/javascript">
								jQuery(document).ready(function() {
									jQuery("ul.bootstrap-post-loop-gallery-' . $uniqueID . '").bootstrapGallery({
										"classes" : "' . \WordPress\Themes\YulaiFederation\yf_get_loopContentClasses() . '",
										"hasModal" : false
									});
								});
								</script>';
					} // END if(\get_post_type() === 'post')
				} else {

				} // END if(have_posts())

				if(\function_exists('\wp_pagenavi')) {
					\wp_pagenavi();
				} else {
					\WordPress\Themes\YulaiFederation\yf_content_nav('nav-below');
				} // END if(\function_exists('wp_pagenavi'))
				?>
			</div>
		</div><!--/.col -->

		<?php
		if(\WordPress\Themes\YulaiFederation\yf_has_sidebar('sidebar-page')) {
			?>
			<div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
				<?php \get_sidebar('page'); ?>
			</div><!--/.col -->
			<?php
		} // END if(\WordPress\Themes\YulaiFederation\yf_has_sidebar('sidebar-page'))

		if(\WordPress\Themes\YulaiFederation\yf_has_sidebar('sidebar-general')) {
			?>
			<div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
				<?php \get_sidebar('general'); ?>
			</div><!--/.col -->
			<?php
		} // END if(\WordPress\Themes\YulaiFederation\yf_has_sidebar('sidebar-general'))
		?>
	</div> <!--/.row -->
</div><!-- container -->

<?php \get_footer(); ?>