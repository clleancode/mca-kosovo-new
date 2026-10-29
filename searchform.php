<?php
	/**
	* Template for displaying search forms in MCA
	*
	* @package WordPress
	* @since 1.0
	* @version 1.0
	*/
	global $faq_parent_cat;
	global $faq_child_cat;
	global $faq_current_post;
	$faq_search_id = wp_unique_id('faq-search-');
?>
	<div class="m-form m-form--faq">
		<form role="search" method="get" class="m-input m-faq-search" action="<?php echo esc_url(home_url('/')); ?>">
			<label class="sr-only" for="<?php echo esc_attr($faq_search_id); ?>"><?php esc_html_e('Search questions', 'mca'); ?></label>
			<input id="<?php echo esc_attr($faq_search_id); ?>" autocomplete="off" type="search" class="a-input--search" placeholder="<?php echo esc_attr_x('Type in your question...', 'placeholder', 'mca'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
			<input type="hidden" value="faq" name="post_type" id="post_type" />
			<input type="hidden" value="<?php echo $faq_parent_cat;?>" name="faq_cat" id="faq_cat"  data-security="<?php echo wp_create_nonce('mca-search-form'); ?>"/>
			<button type="submit" class="a-icon" aria-label="<?php echo _x('Search', 'submit button', 'mca'); ?>">
				<span class="screen-reader-text"><?php echo _x('Search', 'submit button', 'mca'); ?></span>
			</button>    
		</form>
		<ul class="m-suggestions" id="ajax-faq_suggestion"></ul>
	</div>