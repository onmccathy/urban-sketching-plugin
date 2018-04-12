<?php

/* 
 * Contains template to allow user to select country and locality where the google map will be centered.
 */
// Get all countries and localities.

// Build options list
?>
<li id="categories">
	<h2><?php _e( 'Location:' ); ?></h2>
	<form id="category-select" class="category-select"  method="POST">
		<?php wp_dropdown_categories( 'taxonomy=location&hierarchical=1' ); ?>
		<input type="hidden" name="action" value="sket-display-map" />
                <input type="submit" name="submit" value="view" />
	</form>
</li>
