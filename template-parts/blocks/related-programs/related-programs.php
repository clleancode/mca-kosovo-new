<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-related-programs',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");

        if ($space) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

[
    'header_tag'         => $tag,
    'header_size'        => $size,
] = Helpers\get_block_typography([
    'header_color' => 'h-dark-blue',
]);

$query = new WP_Query([
    'post_type'      => 'program',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => ['date' => 'DESC', 'ID' => 'DESC'],
]);
?>
<section
    <?php if (!empty($block['anchor'])) : ?>id="<?php echo esc_attr($block['anchor']); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
>
    <div class="container">
        <div class="m-related-programs">
            <div class="m-related-programs__header">
                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?>">
                    <?php echo esc_html(get_field('related_programs_title')); ?>
                </<?php echo tag_escape($tag); ?>>
                <?php if (get_field('related_programs_note')) : ?>
                    <p class="a-text a-text--m">
                        <?php echo esc_html(get_field('related_programs_note')); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ($query->have_posts()) : ?>
                <div class="m-related-programs__grid">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $program_id = get_the_ID();
                        $project = get_field('program_project', $program_id);
                        $project_id = is_object($project) ? absint($project->ID) : absint($project);
                        $project_title = $project_id ? get_the_title($project_id) : '';
                        $project_name = strtolower($project_title);
                        $project_color = 'default';
                        if (strpos($project_name, 'acf') !== false) {
                            $project_color = 'acfd';
                        } elseif (strpos($project_name, 'energy') !== false || strpos($project_name, 'storage') !== false) {
                            $project_color = 'energy-storage';
                        } elseif (strpos($project_name, 'jeta') !== false) {
                            $project_color = 'jeta';
                        }
                        $status = get_field('program_status', $program_id);
                        $status_labels = [
                            'coming-soon' => 'Coming soon',
                            'questionnaire-available' => 'Questionnaire available',
                        ];
                        $status_label = $status_labels[$status] ?? '';
                        $excerpt = get_the_excerpt();
                        ?>
                        <article class="m-related-programs__card">
                            <div class="m-related-programs__meta">
                                <?php if ($project_title) : ?>
                                    <div class="m-related-programs__project m-related-programs__project--<?php echo esc_attr($project_color); ?>">
                                        <span class="a-text a-text--xs h-white"><?php echo esc_html($project_title); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($status_label) : ?>
                                    <div class="m-related-programs__status m-related-programs__status--<?php echo esc_attr($status); ?>">
                                        <span class="a-text a-text--xs"><?php echo esc_html($status_label); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="m-related-programs__copy">
                                <h3 class="a-heading a-heading--h6">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <?php if ($excerpt) : ?>
                                    <p class="a-text a-text--m"><?php echo esc_html($excerpt); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="m-related-programs__action">
                                <a class="a-text a-text--m" href="<?php the_permalink(); ?>">
                                    <span>Learn more</span>
                                    <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</section>
