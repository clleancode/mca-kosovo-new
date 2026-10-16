<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();
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
$project_posts = get_posts([
    'post_type' => 'project',
    'post_status' => 'publish',
    'posts_per_page' => $two_cards ? 2 : 3,
    'orderby' => ['menu_order' => 'ASC', 'ID' => 'ASC'],
    'post__not_in' => $two_cards && is_singular('project') ? [get_queried_object_id()] : [],
]);
$find_project_hero = static function (array $blocks) use (&$find_project_hero) {
    foreach ($blocks as $project_block) {
        if (($project_block['blockName'] ?? '') === 'acf/projects-hero') return $project_block['attrs']['data'] ?? [];
        $nested = $find_project_hero($project_block['innerBlocks'] ?? []);
        if ($nested) return $nested;
    }
    return [];
};
$projects = [];
$project_order = get_posts(['post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'orderby' => ['menu_order' => 'ASC', 'ID' => 'ASC']]);
$block_tags = [];
$block_labels = [];
foreach ((get_field('compact_projects_tag_groups') ?: []) as $tag_group) {
    $tag_project_id = absint($tag_group['project'] ?? 0);
    if (!$tag_project_id) continue;
    $block_labels[$tag_project_id] = trim((string) ($tag_group['card_label'] ?? ''));
    $block_tags[$tag_project_id] = array_values(array_filter(array_map(static function ($row) {
        return trim((string) ($row['tag'] ?? ''));
    }, $tag_group['tags'] ?: [])));
}
foreach ($project_posts as $project_post) {
    $hero = $find_project_hero(parse_blocks($project_post->post_content));
    $image = $hero['small_hero_image'] ?? get_post_thumbnail_id($project_post->ID);
    if (is_array($image)) $image = $image['ID'] ?? $image['id'] ?? 0;
    $summary = wp_strip_all_tags($project_post->post_excerpt ?: ($hero['small_hero_description'] ?? ''));
    $card_tags = [];
    $tag_rows = $hero['projects_hero_card_tags'] ?? 0;
    if (is_array($tag_rows)) {
        foreach ($tag_rows as $row) {
            $value = is_array($row) ? ($row['tag'] ?? '') : $row;
            if (is_string($value) && trim($value) !== '') $card_tags[] = trim($value);
        }
    } else {
        for ($tag_index = 0; $tag_index < min(3, absint($tag_rows)); $tag_index++) {
            $value = trim((string) ($hero['projects_hero_card_tags_' . $tag_index . '_tag'] ?? ''));
            if ($value !== '') $card_tags[] = $value;
        }
    }
    $projects[] = [
        'number' => sprintf('%02d', array_search($project_post->ID, $project_order, true) + 1),
        'subtitle' => wp_strip_all_tags($hero['small_hero_label'] ?? ''),
        'title' => get_the_title($project_post),
        'image' => absint($image) ?: get_post_thumbnail_id($project_post->ID),
        'description' => $summary,
        'short_description' => wp_trim_words($summary, 12, '…'),
        'category' => $block_labels[$project_post->ID] ?? '',
        'tone' => $hero['projects_hero_tone'] ?? 'blue',
        'link' => ['url' => get_permalink($project_post), 'title' => __('Explore the project', 'MCA')],
        'tags' => array_slice($block_tags[$project_post->ID] ?? $card_tags, 0, 3),
    ];
}

$heading_tag = get_field('header_tag') ?: 'h2';
if (!in_array($heading_tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) $heading_tag = 'h2';

$heading_size = get_field('header_size') ?: 'h2';
if (!in_array($heading_size, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) $heading_size = 'h2';

$description_type = get_field('text_type') ?: 'm';
if (!in_array($description_type, ['xs', 's', 'm', 'l', 'xl', 'xxl'], true)) $description_type = 'm';
?>
<section <?php if (!empty($block['anchor'])): ?>id="<?php echo esc_attr($block['anchor']); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-compact-projects__intro s-d-b-s s-m-b-xs">
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
                    $tone = in_array($project['tone'] ?? '', ['blue', 'purple', 'green'], true) ? $project['tone'] : 'blue';
                    $image = $project['image'] ?? 0;
                    $project_title = $project['title'] ?? '';
                    $side_description = $project['short_description'] ?? '';
                    if (!$side_description) $side_description = $project['description'] ?? '';
                    $project_description = ($project['description'] ?? '') ?: $side_description;
                    $project_link = $project['link'] ?? [];
                    $project_tags = $project['tags'] ?? [];
                    if (is_wp_error($project_tags)) $project_tags = [];
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
                            <div class="m-compact-projects__pair-label"><span class="a-text a-text--xs h-semibold"><?php echo esc_html($project['category'] ?: 'Project ' . $project['number']); ?></span></div>
                            <div class="m-compact-projects__pair-content">
                                <h3 class="a-heading a-heading--h3 h-white"><?php echo esc_html($project_title); ?></h3>
                                <?php if (!empty($project['subtitle'])): ?><p class="a-compact-projects-caption a-text a-text--xs h-white h-regular"><?php echo esc_html($project['subtitle']); ?></p><?php endif; ?>
                                <?php if (!empty($project['description'])): ?><p class="a-text a-text--s h-regular h-white"><?php echo esc_html(wp_trim_words($project['description'], 20)); ?></p><?php endif; ?>
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
                            <span class="m-compact-projects__category-text a-text a-text--xs h-semibold"><?php echo esc_html($project['category']); ?></span>
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
                                            <li class="a-text a-text--m h-semibold"><?php echo esc_html($tag); ?></li>
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
                                <h3 class="a-heading a-heading--h2 h-white a-compact-projects__side-title<?php echo mb_strlen($project_title) > 12 ? ' a-compact-projects__side-title--long' : ''; ?>"><?php echo mb_strlen($project_title) > 12 ? implode('<br>', array_map('esc_html', preg_split('/\s+/', trim($project_title)))) : esc_html($project_title); ?></h3>
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
