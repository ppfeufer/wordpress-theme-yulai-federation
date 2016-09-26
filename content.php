<?php
defined('ABSPATH') or die();

//$stringHelper = new WordPress\Themes\YulaiFederation\Helper\StringHelper;
?>

<article id="post-<?php the_ID(); ?>" <?php \post_class('clearfix'); ?>>
	<?php
	if(\has_post_thumbnail()) {
		?>
		<a href="<?php \the_permalink(); ?>" title="<?php \the_title_attribute('echo=0'); ?>">
			<figure class="post-loop-thumbnail">
			<?php
			if(\function_exists('\fly_get_attachment_image')) {
				echo \fly_get_attachment_image(\get_post_thumbnail_id(), array(705, 395), true);
			} else {
				\the_post_thumbnail('post-loop-thumbnail');
			} // END if(\function_exists('\fly_get_attachment_image'))
			?>
			</figure>
		</a>
		<?php
	} // END if(has_post_thumbnail())
	?>

	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php \the_permalink(); ?>" title="<?php \printf(\esc_attr__('Permalink to %s', 'yulai-federation'), \the_title_attribute('echo=0')); ?>" rel="bookmark">
				<?php the_title(); ?>
			</a>
		</h2>
		<aside class="entry-details">
			<p class="meta">
				<?php
//				echo \WordPress\Themes\YulaiFederation\yf_posted_on();

				\edit_post_link(__('Edit', 'yulai-federation'));
				?>
				<br/>
				<?php
//				\WordPress\Themes\YulaiFederation\yf_cats_tags();
				?>
			</p>
		</aside><!--end .entry-details -->
	</header><!--end .entry-header -->

	<section class="post-content">
		<div class="row">
			<div class="col-md-12">
				<?php
				if(\is_search()) { // Only display excerpts without thumbnails for search.
					?>
					<div class="entry-summary">
						<?php \the_excerpt(); ?>
					</div><!-- end .entry-summary -->
					<?php
				} else {
					?>
					<div class="entry-content">
						<?php
						echo \wpautop(\do_shortcode(WordPress\Themes\YulaiFederation\Helper\StringHelper::cutString(\get_the_content(), '140')));
						\printf('<a href="%1$s"><span class="read-more">' . \__('Read more', 'yulai-federation') . '</span></a>', \get_the_permalink());

//						if(isset($options['excerpts'])) {
//							echo \the_excerpt();
//						} else {
//							echo \the_content('<span class="read-more">Read more</span>', 'yulai-federation');
//						} // END if(isset($options['excerpts']))
						?>
					</div><!-- end .entry-content -->
					<?php
				} // END if(is_search())
				?>
			</div><!-- end .col -->
		</div><!-- end .row -->
	</section>
</article><!-- /.post-->
<hr>