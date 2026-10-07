<?php
use MCA\Helpers;

$visibility = Helpers\rapture_get_device_visibility();
if (!$visibility['show']) return;

$classes = ['o-compact-projects', 'm-compact-projects', $visibility['visibility_classes'], $block['className'] ?? ''];
foreach (['mobile' => 'm', 'desktop' => 'd'] as $device => $prefix) {
    foreach (['top' => 't', 'bottom' => 'b'] as $side => $suffix) {
        $space = get_field("space_{$device}_{$side}");
        if ($space) $classes[] = "s-{$prefix}-{$suffix}-{$space}";
    }
}

$two_cards = get_field('compact_projects_layout') === 'two';
$label = get_field('compact_projects_label') ?: 'Compact projects';
$title = get_field('compact_projects_title') ?: ($two_cards ? 'Other Compact projects' : "Three Projects.\nOne Transformative Compact.");
$description = get_field('compact_projects_description');
$projects = get_field('compact_projects_items') ?: [];
$projects = array_slice($projects, 0, $two_cards ? 2 : 3);

$heading_tag = get_field('header_tag') ?: 'h2';
if (!in_array($heading_tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) $heading_tag = 'h2';

$heading_size = get_field('header_size') ?: 'h2';
if (!in_array($heading_size, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) $heading_size = 'h2';

$description_type = get_field('text_type') ?: 'm';
if (!in_array($description_type, ['xs', 's', 'm', 'l', 'xl', 'xxl'], true)) $description_type = 'm';
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-compact-projects__intro">
            <div class="m-compact-projects__heading">
                <?php if ($label): ?>
                    <p class="a-badge h-blue h-semibold"><?php echo esc_html($label); ?></p>
                <?php endif; ?>
                <<?php echo tag_escape($heading_tag); ?> class="a-heading a-heading--<?php echo esc_attr($heading_size); ?> <?php echo esc_attr(get_field('header_color') ?: 'h-white'); ?> a-compact-projects__title">
                    <?php echo nl2br(esc_html($title)); ?>
                </<?php echo tag_escape($heading_tag); ?>>
            </div>
            <?php if ($description): ?>
                <p class="a-text a-text--<?php echo esc_attr($description_type); ?> <?php echo esc_attr(get_field('text_color') ?: 'h-white'); ?> a-compact-projects__description">
                    <?php echo nl2br(esc_html($description)); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php if ($projects): ?>
            <div class="m-compact-projects__grid<?php echo $two_cards ? ' m-compact-projects__grid--two' : ''; ?>">
                <?php foreach ($projects as $index => $project): ?>
                    <?php
                    $number = sprintf('%02d', $index + 1);
                    $layout = $index === 0 ? 'feature' : 'compact';
                    $tone = $index === 1 ? 'purple' : ($index === 2 ? 'green' : 'blue');
                    $image = $project['image'] ?? 0;
                    $project_title = $project['title'] ?? '';
                    if (!$project_title && $index === 1) $project_title = 'JETA';
                    if (!$project_title && $index === 2) $project_title = 'ACFD';
                    $side_description = $project['short_description'] ?? '';
                    if (!$side_description && $index === 1) $side_description = 'Just and Equitable Transition Acceleration';
                    if (!$side_description && $index === 2) $side_description = 'American Catalyst Facility for Development';
                    if (!$side_description) $side_description = $project['description'] ?? '';
                    $project_description = ($project['description'] ?? '') ?: $side_description;
                    $project_link = $project['link'] ?? [];
                    $project_tags = $project['tags'] ?? [];
                    if (!is_array($project_tags)) {
                        $project_tags = explode(',', (string) $project_tags);
                    }
                    $project_tags = array_slice(array_filter(array_map(static function ($tag) {
                        if (is_array($tag)) $tag = $tag['tag'] ?? '';
                        return trim((string) $tag);
                    }, $project_tags)), 0, 3);
                    ?>
                    <?php if ($two_cards):
                        $pair_tone = in_array($project['tone'] ?? '', ['blue', 'purple', 'green'], true) ? $project['tone'] : ($index === 0 ? 'purple' : 'green');
                        ?>
                        <article class="m-compact-projects__card m-compact-projects__card--<?php echo esc_attr($pair_tone); ?>">
                            <?php if ($image) echo wp_get_attachment_image($image, 'large', false, ['class' => 'm-compact-projects__image', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                            <div class="m-compact-projects__overlay"></div>
                            <div class="m-compact-projects__pair-label"><span class="a-text a-text--xs h-semibold"><?php echo esc_html(($project['category'] ?? '') ?: 'Project ' . sprintf('%02d', $index + 2)); ?></span></div>
                            <div class="m-compact-projects__pair-content">
                                <h3 class="a-heading a-heading--h3 h-white"><?php echo esc_html($project_title); ?></h3>
                                <?php if (!empty($project['short_description'])): ?><p class="a-text a-text--xs h-white h-semibold"><?php echo esc_html($project['short_description']); ?></p><?php endif; ?>
                                <?php if (!empty($project['description'])): ?><p class="a-text a-text--l h-regular h-white"><?php echo nl2br(esc_html($project['description'])); ?></p><?php endif; ?>
                            </div>
                            <?php if (!empty($project_link['url'])): ?><a class="a-compact-projects-pair-link" href="<?php echo esc_url($project_link['url']); ?>" target="<?php echo esc_attr(($project_link['target'] ?? '') ?: '_self'); ?>" aria-label="<?php echo esc_attr('Explore ' . $project_title); ?>"<?php if (($project_link['target'] ?? '') === '_blank'): ?> rel="noopener noreferrer"<?php endif; ?>><i class="icon-arrow-right-up a-icon" aria-hidden="true"></i></a><?php endif; ?>
                        </article>
                        <?php continue; endif; ?>
                    <article class="m-compact-projects__card m-compact-projects__card--<?php echo esc_attr($layout); ?> m-compact-projects__card--<?php echo esc_attr($tone); ?>">
                        <?php if ($image): ?>
                            <?php echo wp_get_attachment_image($image, 'large', false, ['class' => 'm-compact-projects__image', 'loading' => 'lazy', 'decoding' => 'async']); ?>
                        <?php endif; ?>
                        <div class="m-compact-projects__overlay"></div>
                        <p class="a-text a-text--s m-compact-projects__number">
                            <span class="m-compact-projects__collapsed-number"><?php echo esc_html($number); ?></span>
                            <span class="m-compact-projects__category-text"><?php echo esc_html(($project['category'] ?? '') ?: $number); ?></span>
                        </p>
                            <div class="m-compact-projects__feature-content"<?php if ($index !== 0): ?> hidden<?php endif; ?>>
                                <?php if ($project_title): ?>
                                    <h3 class="a-heading a-heading--h2 h-white a-compact-projects__project-title s-d-b-s s-m-b-xs"><?php echo esc_html($project_title); ?></h3>
                                <?php endif; ?>
                                <?php if ($project_description): ?>
                                    <p class="a-text a-text--xl h-white a-compact-projects__project-description"><?php echo nl2br(esc_html($project_description)); ?></p>
                                <?php endif; ?>
                                <?php if ($project_tags): ?>
                                    <ul class="m-compact-projects__tags s-d-b-s s-m-t-xs s-m-b-xs s-d-t-s s-d-b-s">
                                        <?php foreach ($project_tags as $tag): ?>
                                            <li class="a-text a-text--m"><?php echo esc_html($tag); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <?php if (!empty($project_link['url'])): ?>
                                    <a class="m-compact-projects__button a-text a-text--m" href="<?php echo esc_url($project_link['url']); ?>" target="<?php echo esc_attr(($project_link['target'] ?? '') ?: '_self'); ?>"<?php if (($project_link['target'] ?? '') === '_blank') echo ' rel="noopener noreferrer"'; ?>>
                                        <?php echo esc_html(($project_link['title'] ?? '') ?: 'Explore the project'); ?>
                                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <button class="m-compact-projects__open" type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr('Open ' . $project_title); ?>">+</button>
                            <?php if ($project_title): ?>
                                <h3 class="a-heading a-heading--h2 h-white a-compact-projects__side-title"><?php echo esc_html($project_title); ?></h3>
                            <?php endif; ?>
                            <?php if ($side_description): ?>
                                <p class="a-text a-text--xs h-white a-compact-projects__side-description"><?php echo esc_html($side_description); ?></p>
                            <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
