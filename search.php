<?php
	/*
	* archive.php
	*/
	
	get_header();
?>
	<div class="defult-bg default-search">
		<?php
			$image = get_field('upload_category_banner', 'option');
			$size = 'extra_large';
			if ($image) {
				echo wp_get_attachment_image($image, $size);
			}
		?>
	</div>
	<section class="search s-d-t-3xl s-m-t-2xl s-d-b-2xl s-m-b-xl">
		<div class="container h-flex-center">
			<h1 class="a-heading a-heading--h3 ">
				<?php
					$search_query = get_search_query();
					if ( $search_query ) {
						printf(
							esc_html__( 'Search results for: %s', 'mca-kosovo' ),
							'<span class="a-text h-blue">' . esc_html( $search_query ) . '</span>'
						);
					}
				?>
			</h1>
			<div class="m-results m-results--search">
				<?php
					$paged = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) );
					$search_term = get_search_query();
					$search_wp = new WP_Query([
						's' => $search_term,
						'posts_per_page' => 6,
						'paged' => $paged,
					]);
				?>
				<?php if ( $search_wp->have_posts() ) : ?>
						<?php while ( $search_wp->have_posts() ) : $search_wp->the_post(); ?>
							<div class="m-faq m-faq--procurement m-results__item">
							<a href="<?php the_permalink(); ?>" class="m-results__link">
								<p class="a-heading a-heading--h6 s-d-b-xs s-m-b-xs h-britanica"><?php the_title(); ?></p>
								<p class="a-text a-text--m"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
							</a>
							<i class="icon-arrow-right-up a-icon a-icon--xs"></i>
					        </div>
						<?php endwhile; wp_reset_postdata(); ?>

					<div class="m-pagination">
						<?php
							$total_pages = $search_wp->max_num_pages;
							if ( $total_pages > 1 ) {
								$base = remove_query_arg( 'paged', get_pagenum_link(1) ) . '%_%';
								$format = '&paged=%#%';
								$pagination_links = paginate_links([
									'base'      => $base,
									'format'    => $format,
									'current'   => $paged,
									'total'     => $total_pages,
									'type'      => 'list',
									'mid_size'  => 1,
									'end_size'  => 1,
									'prev_next' => true,
									'prev_text' => '<i class="icon-arrow-right a-icon" aria-hidden="true" aria-label="Previous page"></i>',
									'next_text' => '<i class="icon-arrow-right a-icon" aria-hidden="true" aria-label="Next page"></i>',
									'add_args'  => [ 's' => $search_term ],
								]);

								if ( $pagination_links ) {
									echo '<nav class="m-pagination s-m-t-m s-d-t-m">' . $pagination_links . '</nav>';
								}
							}
						?>
					</div>
				<?php else : ?>
					<p class="a-text a-text--m"><?php esc_html_e( 'No results found. Please try a different search.', 'mca-kosovo' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php
	get_footer(); ?>