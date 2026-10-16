<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package MCA
 */

get_header(); ?>

<main id="main" class="site-main">
    <section class="o-404" aria-labelledby="error-title">
        <img class="o-404__lines" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/pics/icons/404-lines.svg'); ?>" alt="" aria-hidden="true" decoding="async">
        <div class="container">
            <div class="m-404">
                <div class="m-404__content">
                    <span class="a-tag a-tag--red a-text a-text--xs h-uppercase"><span><?php esc_html_e('Error 404', 'MCA'); ?></span></span>
                    <h1 id="error-title" class="a-heading a-heading--h3 h-white h-medium s-d-t-s s-m-t-xs"><?php esc_html_e('This page is off the grid', 'MCA'); ?></h1>
                    <p class="a-text a-text--xl h-white s-d-t-s s-m-t-xs s-d-b-s s-m-b-xs"><?php esc_html_e('The page you’re looking for may have moved or no longer exists. Try searching, or head to one of these:', 'MCA'); ?></p>
                    <div class="m-404__actions">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="a-btn a-btn--white"><span><?php esc_html_e('Back to homepage', 'MCA'); ?></span></a>
                        <a href="<?php echo esc_url(home_url('/projects/')); ?>" class="a-btn a-btn--border h-white"><span><?php esc_html_e('Projects', 'MCA'); ?></span></a>
                        <a href="<?php echo esc_url(home_url('/procurement/')); ?>" class="a-btn a-btn--border h-white"><span><?php esc_html_e('Procurement', 'MCA'); ?></span></a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="a-btn a-btn--border h-white"><span><?php esc_html_e('Contact', 'MCA'); ?></span></a>
                    </div>
                </div>
                <span class="m-404__number s-m-t-s" aria-hidden="true">404</span>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
