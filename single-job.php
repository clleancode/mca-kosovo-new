<?php
/*
 * single.php
 * Template for single post (blog/news).
 */
get_header();


    $post_title = get_the_title();
    $job_deadline = get_post_meta(get_the_ID(), 'deadline', true);

    $status_terms   = get_the_terms(get_the_ID(), 'job_status');
    $status_label   = '';
    $status_slug    = '';
    if ($status_terms && !is_wp_error($status_terms)) {
        $status_label = esc_html($status_terms[0]->name);
        $status_slug  = strtolower($status_terms[0]->slug);
    }
    $icon_color_class = ($status_slug === 'closed') ? 'h-red' : 'h-light-green';

    if ($job_deadline) {
        $timestamp = strtotime($job_deadline);
        $job_deadline = date('j F Y', $timestamp);
    }
?>

<section class="o-hero-banner o-hero-banner--left  s-d-t-2xl s-d-b-l  s-m-t-2xl s-m-b-m">
    <div class="container">
        <div class="m-content h-full">
            <span class="a-tag <?php echo ($status_slug === 'closed') ? ' a-tag--red' : ''; ?>" data-aos="fade-up">
                <?php echo $status_label; ?>
            </span>
            <h1 class="a-heading a-heading--h3" data-aos="fade-up"><?php echo wp_kses_post( $post_title ); ?></h1>
            <div class="m-apply" data-aos="fade-up" data-aos-delay="100">
                <p class="a-text a-text--xl h-no-margin">Deadline: <?php echo esc_html( $job_deadline); ?></p>
                <a href="<?php echo site_url('/job-application/?position=' . sanitize_title(get_the_title())); ?>" class="a-btn a-btn--border-blue a-btn--smaller"><span><?php echo ($status_slug === 'closed') ? 'View' : 'Apply Now'; ?> <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i></span></a>
            </div>
        </div>
    </div>
</section>

<?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>
<?php
get_footer();
?>