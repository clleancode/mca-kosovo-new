<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package MCA
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="o-not-found">
        <div class="container">
            <div class="m-content">
                <h1 class="a-heading a-heading--h1">404</h1>
                <p class="a-text a-text--m">We're sorry, but the page you're looking for doesn't exist. It might have been moved, deleted, or the URL could be incorrect. You can go back to the homepage or use the search to find what you need.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="a-btn a-btn--blue"><span>Go to home</span></a>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>