<?php
/**
 * Single Procurement Template
 * Title rendered outside blocks; inside use content-procurement block(s)
 */

get_header();


    $post_title = get_the_title();
    $procurement_id = get_the_ID();
    $procurements_page = get_page_by_path('procurement');
    $procurements_url = $procurements_page ? get_permalink($procurements_page) : home_url('/procurement/');
    $procurement_terms = get_the_terms($procurement_id, 'procurement_status');
    $procurement_term_slugs = $procurement_terms && !is_wp_error($procurement_terms)
        ? wp_list_pluck($procurement_terms, 'slug')
        : [];
?>

	<section class="o-hero-banner s-d-t-4xl s-m-t-2xl">
		<div class="container h-flex-center">
			<div class="m-content h-text-center">
                <p class="a-text a-text--m s-m-b-xs">
                    <a href="<?php echo esc_url($procurements_url); ?>" class="h-link">All Procurements</a>
                </p>
				<h1 class="a-heading a-heading--h4 h-semibold" data-aos="fade"><?php the_title(); ?></h1>
			</div>
		</div>
	</section>
	<section class="m-content s-d-t-m s-d-b-m s-m-b-s s-m-t-s">
		<div class="container h-flex-center">
			<h5 class="a-heading a-heading--h5 h-text-center" data-aos="fade" data-delay-aos="100">Procurement Documents</h5>
		</div>
	</section>

    <?php while ( have_posts() ) : the_post(); ?>
	<?php the_content();?>
<?php endwhile;?>

<section class="m-content s-d-b-m s-m-b-m">
    <div class="container h-flex-center">
        <a href="<?php echo esc_url( $procurements_url ); ?>" class="a-btn a-btn--link h-bold">
            <span class="h-link">
                <?php esc_html_e( 'Back to Procurements', 'mca-kosovo' ); ?>
            </span>
            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
        </a>
    </div>
</section>

<?php get_footer();


