<?php
/*
 * single.php
 * Template for single post (blog/news).
 */
get_header();


    $post_title = get_the_title();
    $deadline = \MCA\Notices\get_deadline(get_the_ID());
    $job_deadline = $deadline ? wp_date('j F Y', $deadline->getTimestamp()) : '';

    $status_terms   = get_the_terms(get_the_ID(), 'job_status');
    $status_label   = '';
    $status_slug    = '';
    if ($status_terms && !is_wp_error($status_terms)) {
        $status_label = esc_html($status_terms[0]->name);
        $status_slug  = strtolower($status_terms[0]->slug);
    }
    if ($deadline) {
        $closed = $deadline < new DateTimeImmutable('today', wp_timezone());
        $status_slug = $closed ? 'closed' : 'ongoing';
        $status_label = $closed ? __('Closed', 'mca') : __('Open', 'mca');
    }
?>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>
<?php
get_footer();
?>
