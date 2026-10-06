<?php
	/*
	* category.php
	*/
	get_header();
	$cat_id = get_queried_object_id();
	$posts_page_id = (int) get_option('page_for_posts');
	$archive_parent_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
	$archive_parent_title = $posts_page_id ? get_the_title($posts_page_id) : __('News', 'MCA');
?>
	<div class="o-hero o-hero--image">
	    <div class="container">
	        <video muted autoplay loop playsinline poster class="a-video">
	            <source src="https://www.rapturecamps.com/wp-content/uploads/2019/10/bgvideo-home.mp4" type="video/mp4">
	        </video>
	        <div class="m-content">
				<nav aria-label="Breadcrumb">
					<div class="m-breadcrumbs s-m-b-m s-d-b-s">
						<span>
							<span>
								<a href="<?php echo esc_url($archive_parent_url); ?>"><?php echo esc_html($archive_parent_title); ?></a>
							</span> / 
							<span class="breadcrumb_last" aria-current="page"><?php single_cat_title(); ?></span>
						</span>
					</div>
				</nav>
				<?php
					$breadcrumbs = [
						"@context" => "https://schema.org",
						"@type"    => "BreadcrumbList",
						"itemListElement" => [
							[
								"@type" => "ListItem",
								"position" => 1,
								"name" => "Home",
								"item" => home_url('/')
							],
							[
								"@type" => "ListItem",
								"position" => 2,
								"name" => $archive_parent_title,
								"item" => $archive_parent_url
							],
							[
								"@type" => "ListItem",
								"position" => 3,
								"name" => single_cat_title('', false),
								"item" => get_category_link(get_queried_object_id())
							],
						]
					];
					echo '<script type="application/ld+json">' . wp_json_encode($breadcrumbs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
				?>
	            <h1 class="a-heading a-heading--l"><?php single_cat_title();?></h1>
	            <div class="a-seperator"></div>
				<?php
					$description = get_the_archive_description();
					echo \MCA\Helpers\rapture_format_recipe_text($description, 'h-white-600');
				?>
	        </div>
	    </div>
	</div>
	<section class="o-blogs s-d-t-m s-m-t-m">
	    <div class="container">
            <?php 
                if ( is_active_sidebar( 'category_sidebar' ) ) :
					dynamic_sidebar( 'category_sidebar' );
				endif;

                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                
                $args = array(
                   'post_type' => 'post',
					'post_status' => 'publish',
					'posts_per_page' => 12,
					'paged' => $paged,
					'cat' => $cat_id
                );
                
                $wp_query = new WP_Query( $args );
                
                if ( $wp_query->have_posts() ):
            ?>
                    <div class="m-blogs">
                        <?php
                            while ( $wp_query->have_posts() ): $wp_query->the_post();
                                $post_id = get_the_ID();
                                $title = get_the_title();
                                $link = get_permalink();
                                $image_url = get_the_post_thumbnail_url($post_id, 'full');
                                $thumb_id = get_post_thumbnail_id($post_id);
                                $alt_text = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
                                $date = get_the_date('j. M Y', $post_id);
                                $date_iso = get_the_date('c', $post_id);
                                $excerpt = wp_trim_words(get_the_excerpt($post_id), 19);
                                $categories = get_the_category($post_id);
                                $category_link = !empty($categories) ? get_category_link($categories[0]->term_id) : '';
                                $category_name = !empty($categories) ? $categories[0]->name : '';
                        ?>
                                <article class="m-card-news" itemscope itemtype="https://schema.org/BlogPosting">
                                    <a href="<?php echo esc_url($link); ?>" class="m-card-news__img" itemprop="url">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($alt_text ?: $title); ?>" class="a-img" loading="lazy" decoding="async" itemprop="image">
                                    </a>
                                    <div class="m-card-news__content">
                                        <h2 class="a-subheading a-subheading--l" itemprop="headline">
                                            <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
                                        </h2>
                                        <p class="a-text a-text--m h-white-700">
                                            <span class="h-white" itemprop="datePublished" content="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date); ?></span>
                                            <?php if (!empty($category_name)) : ?>
                                                <a href="<?php echo esc_url($category_link); ?>" itemprop="about"><?php echo esc_html($category_name); ?></a>
                                            <?php endif; ?>
                                            <br>
                                            <span itemprop="description"><?php echo esc_html($excerpt); ?></span>
                                        </p>
                                    </div>
                                </article>
                        <?php
                            endwhile;
                        ?>
                        <div class="m-pagination">
                            <?php
                                $total_pages = $wp_query->max_num_pages;
                                if ( $total_pages > 1 ) {
                                    $pagination_links = paginate_links( array(
                                        'base'      => trailingslashit( get_pagenum_link( 1 ) ) . '%_%',
                                        'format'    => 'page/%#%/',
                                        'current'   => max( 1, $paged ),
                                        'total'     => $total_pages,
                                        'mid_size'  => 2,
                                        'prev_text' => __('<'),
                                        'next_text' => __('>'),
                                    ) );

                                    if ( $pagination_links ) {
                                        echo $pagination_links;
                                    }
                                }
                            ?>
                        </div>
                    </div>
            <?php
                endif;
                wp_reset_postdata();
            ?>
	    </div>
	</section>

	<!-- <div class="defult-bg default-category">
		<?php
			$image = get_field('upload_category_banner','option');
			$size = 'extra_large';
			if( $image ) {
				echo wp_get_attachment_image( $image, $size );
			}
		?>
	</div>
	<div class="wrapper" data-cat="1">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<div class="breadcrumb">
						<span>
							<span>
								<a href="<?php echo get_the_permalink(260); ?>"><?php echo get_the_title(260); ?></a> / <span><?php single_cat_title(); ?></span>
							</span>
						</span>
					</div>
				</div>
				<div class="col-xs-12">
					<h1 class="main__title"><?php single_cat_title();?></h1>
					<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
				</div>
				<div class="col-sm-3 col-xs-12">
					<?php if ( is_active_sidebar( 'category_sidebar' ) ) : ?>
						<div class="category-listing">
							<?php dynamic_sidebar( 'category_sidebar' ); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-9 col-xs-12" data-cat="2">
					<?php
						$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
						$args = array(
							'post_type' => 'post',
							'post_status' => 'publish',
							'posts_per_page' => 12,
							'paged' => $paged,
							'cat' => $cat_id
						);
						$wp_query = new WP_Query( $args );
						if ( $wp_query->have_posts() ):
					?>
							<div class="blog__list">
								<div class="row flex-wrap">
									<?php
										while ( $wp_query->have_posts() ) : $wp_query->the_post();
											get_template_part('template-parts/layout-post','grid');
										endwhile;
									?>
								</div>
							</div>
					<?php
						endif;
						wp_reset_postdata();
					?>
					<div class="pagination">
						<?php
							echo paginate_links( array(
								'format'  => 'page/%#%',
								'current' => $paged,
								'total'   => $wp_query->max_num_pages,
								'mid_size'        => 2,
								'prev_text'       => __('<'),
								'next_text'       => __('>')
							) );
						?>
					</div>
				</div>
			</div>
		</div>
	</div> -->

<?php get_footer();