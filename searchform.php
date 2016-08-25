<?php defined('ABSPATH') or die(); ?>

<form action="/" method="GET" id="s" role="search">
	<div class="input-group">
		<label class="sr-only" for="search"><?php echo \__('Search', 'yulai-federation') ?></label>
		<input type="text" class="form-control" id="search" name="s" placeholder="<?php echo \__('Search', 'yulai-federation') ?>" value="<?php \the_search_query(); ?>">
		<div class="input-group-btn">
			<button type="submit" class="btn btn-default">
				<span class="glyphicon glyphicon-search"></span>
			</button>
		</div>
	</div>
</form>