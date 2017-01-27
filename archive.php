<?php
defined('ABSPATH') or die();

\get_header();
?>

<div class="container main">
	<div class="row">
		<div class="col-md-12">
			<?php
			\WordPress\Themes\YulaiFederation\Helper\NavigationHelper::getBreadcrumbs();
			?>
		</div><!--/.col -->
	</div><!--/.row -->

	<div class="row main-content">
		<div class="<?php echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getMainContentColClasses(); ?>">
			<div class="content content-archive">
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
							} // END if($tag_description)
						} elseif(\is_category()) {
							\printf(\__('Category Archives: %s', 'yulai-federation'), '<span>' . \single_cat_title('', false) . '</span>');
						} else {
							\_e('Blog Archives', 'yulai-federation');
						} //END if(is_day())
						?>
					</h1>
					<?php
					// Show an optional category description
					$category_description = \category_description();
					if($category_description) {
						echo \apply_filters('category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>');
					} // END if($category_description)
					?>
				</header>
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
										"classes" : "' . \WordPress\Themes\YulaiFederation\Helper\PostHelper::geLoopContentClasses() . '",
										"hasModal" : false
									});
								});
								</script>';
					} // END if(\get_post_type() === 'post')
				} // END if(have_posts())

				if(\function_exists('wp_pagenavi')) {
					\wp_pagenavi();
				} else {
					\WordPress\Themes\YulaiFederation\Helper\NavigationHelper::getContentNav('nav-below');
				} // END if(\function_exists('wp_pagenavi'))s
				?>
			</div> <!-- /.content -->
		</div> <!-- /.col -->

		<?php
		if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-page') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-general')) {
			?>
			<div class="col-lg-3 col-md-3 col-sm-3 col-3 sidebar-wrapper">
				<?php
				if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-page')) {
					\get_sidebar('page');
				} // END if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-page'))

				if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-general')) {
					\get_sidebar('general');
				} // END if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-general'))
				?>
			</div><!--/.col -->
			<?php
		} // END if(\WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-page') || \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::hasSidebar('sidebar-general')) {
		?>
	</div> <!--/.row -->
</div><!-- container -->

<?php \get_footer(); ?>