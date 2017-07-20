<?php defined('ABSPATH') or die(); ?>

<article id="post-<?php the_ID(); ?>" <?php \post_class('clearfix content-image'); ?>>
	<header class="entry-header">
		<h2 class="entry-title">
			<a href="<?php \the_permalink(); ?>" title="<?php \printf(\esc_attr__('Permalink to %s', 'yulai-federation'), \the_title_attribute('echo=0')); ?>" rel="bookmark">
				<?php \the_title(); ?>
			</a>
		</h2>
		<aside class="entry-details">
			<p class="meta">
				<?php
				echo \WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getPostMetaInformation();
				\WordPress\Themes\YulaiFederation\Helper\PostHelper::getInstance()->getPostCategoryAndTags();
				\edit_post_link(\__('Edit', 'yulai-federation'));
				?>
			</p>
		</aside><!--end .entry-details -->
	</header><!--end .entry-header -->

	<section class="post-content clearfix">
<!--		<div class="row">
			<div class="col-md-12">-->
				<?php
				if(\is_search()) { // Only display excerpts without thumbnails for search.
					?>
					<div class="entry-summary clearfix">
						<?php \the_excerpt(); ?>
					</div><!-- end .entry-summary -->
					<?php
				} else {
					?>
					<div class="entry-content clearfix">
						<?php
						$options = \get_option('yulai_theme_options', \WordPress\Themes\YulaiFederation\Helper\ThemeHelper::getInstance()->getThemeDefaultOptions());

						if(\has_post_thumbnail()) {
							?>
							<a href="<?php \the_permalink(); ?>" title="<?php \the_title_attribute('echo=0'); ?>">
								<?php
								if(isset($options['featured_img_arch_size'])) {
									switch($options['featured_img_arch_size']) {
										case 1:
											$thumbnail_size = "thumbnail";
											break;
										case 2:
											$thumbnail_size = "medium";
											break;
										case 3:
											$thumbnail_size = "large";
											break;
										default:
											$thumbnail_size = "thumbnail";
											break;
									} // END switch($options['featured_img_arch_size'])

									\the_post_thumbnail($thumbnail_size);
								} // END if(isset($options['featured_img_arch_size']))
								?>
							</a>
							<?php
						} // END if(has_post_thumbnail())

						if(isset($options['excerpts'])) {
							echo \the_excerpt();
						} else {
							echo \the_content('<span class="read-more">Read more</span>', 'yulai-federation');
						} // END if(isset($options['excerpts']))
						?>
					</div><!-- end .entry-content -->
					<?php
				} // END if(is_search())
				?>
<!--			</div> end .col
		</div> end .row -->
	</section>
</article><!-- /.post-->
<hr>
