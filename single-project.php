<?php
/*
 * single-project.php
 * Template for single project.
 */
get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();
?>
