<?php
defined('ABSPATH') or die();

if(\post_password_required()) {
	return;
} // END if(post_password_required())
?>

<div id="comments" class="comments-area">
	<?php
	if(\have_comments()) {
		?>
		<ul class="media-list">
			<?php
			\wp_list_comments(array(
				'callback' => '\WordPress\Themes\YulaiFederation\yf_comment'
			));
			?>
		</ul>

		<?php
		if(\get_comment_pages_count() > 1 && \get_option('page_comments')) {
			?>
			<nav id="comment-nav-below" class="navigation" role="navigation">
				<div class="nav-previous">
					<?php \previous_comments_link(\_e('&larr; Older Comments', 'yulai-federation')); ?>
				</div>
				<div class="nav-next">
					<?php \next_comments_link(\_e('Newer Comments &rarr;', 'yulai-federation')); ?>
				</div>
			</nav>
			<?php
		} // END if(get_comment_pages_count() > 1 && get_option('page_comments'))
	} elseif(!\comments_open() && '0' != \get_comments_number() && \post_type_supports(\get_post_type(), 'comments')) {
		?>
		<p class="nocomments"><?php \_e('Comments are closed.', 'yulai-federation'); ?></p>
		<?php
	} // END if(have_comments())

	\comment_form();
	?>
</div>