<?php
get_header();
while (have_posts()) : the_post();
    if (!has_block('acf/small-hero', get_the_ID())) {
        get_template_part('template-parts/blocks/small-hero/procurement-hero', null, ['post_id' => get_the_ID()]);
    }
    the_content();
endwhile;
get_footer();
