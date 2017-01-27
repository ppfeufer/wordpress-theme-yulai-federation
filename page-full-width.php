<?php
defined('ABSPATH') or die();

/**
 * Template Name: Default Page (Full Width)
 */

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

	<?php
	if(\have_posts()) {
		while(\have_posts()) {
			\the_post();
			?>
			<div class="row main-top">
				<div class="col-lg-12 col-md-12 col-sm-12 col-12">
					<header>
						<h1>
							<?php
							if(\is_front_page()) {
								?>
								<h1><?php echo \get_bloginfo('name'); ?></h1>
								<?php
							} else {
								?>
								<h1><?php \the_title(); ?></h1>
								<?php
							} // END if(is_front_page())
							?>
						</h1>
					</header>
				</div><!--/.col -->
			</div><!--/.row -->

			<div class="row main-content">
				<div class="col-lg-12 col-md-12 col-sm-12 col-12">
					<div class="content main">
						<article class="post clearfix" id="post-<?php \the_ID(); ?>">
							<?php echo \the_content(); ?>
						</article>
					</div> <!-- /.content -->
				</div> <!-- /.col -->
			</div> <!--/.row -->
			<?php
		}
	} // END if(have_posts())
	?>
</div><!-- /.container -->

<?php \get_footer(); ?>