<?php
	/**
	* Template for displaying search forms in Rapture
	*
	* @package WordPress
	* @since 1.0
	* @version 1.0
	*/
	global $faq_parent_cat;
	global $faq_child_cat;
	global $faq_current_post;
?>
	<div class="m-form m-form--faq">
		<form role="search" method="get" class="m-input m-faq-search" action="<?php echo esc_url(home_url('/')); ?>">
			<input autocomplete="off" type="search" class="a-input--search" placeholder="<?php echo esc_attr_x('Type in your question...', 'placeholder', 'rapture'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			<input type="hidden" value="faq" name="post_type" id="post_type" />
			<input type="hidden" value="<?php echo $faq_parent_cat;?>" name="faq_cat" id="faq_cat"  data-security="<?php echo wp_create_nonce('rapture-search-form'); ?>"/>
			<button type="submit" class="a-icon" aria-label="<?php echo _x('Search', 'submit button', 'rapture'); ?>">
				<span class="screen-reader-text"><?php echo _x('Search', 'submit button', 'rapture'); ?></span>
			</button>    
		</form>
		<ul class="m-suggestions" id="ajax-faq_suggestion"></ul>
	</div>