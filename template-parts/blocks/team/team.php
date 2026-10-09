<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-team',
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
    'header_color'       => $color,
    'header_line_height' => $height,
] = Helpers\get_block_typography();

$layout     = get_field('team_layout');
$section_id = sanitize_title(get_field('team_section_id') ?: ($block['anchor'] ?? ''));
$instance   = sanitize_html_class($block['id'] ?? wp_unique_id('team-'));
$groups     = get_field('team_groups') ?: [];

$tabs = $layout === 'board'
    ? [
        'board'      => 'Board of Directors',
        'management' => 'Senior management',
        'mcc'        => 'MCC Kosovo',
    ]
    : [
        'people' => 'Our people',
    ];

if ($layout === 'board') {
    $tabs = array_filter(
        $tabs,
        static function ($key) use ($groups) {
            foreach ($groups as $group) {
                if (($group['tab'] ?? '') === $key) {
                    return true;
                }
            }

            return false;
        },
        ARRAY_FILTER_USE_KEY
    );
}
?>
<section
    <?php if ($section_id) : ?>id="<?php echo esc_attr($section_id); ?>"<?php endif; ?>
    class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>"
    data-team
>
    <div class="container">
        <div class="m-team m-team--<?php echo esc_attr($layout); ?>">
            <div class="m-team__header s-d-b-s s-m-b-xs">
                <div class="m-team__heading">
                    <?php if (get_field('team_label')) : ?>
                        <p class="a-badge h-dark-blue s-d-b-xs s-m-b-xs h-semibold"><?php echo esc_html(get_field('team_label')); ?></p>
                    <?php endif; ?>

                    <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                        <?php echo esc_html(get_field('team_title')); ?>
                    </<?php echo tag_escape($tag); ?>>
                </div>

                <?php if ($layout === 'board' && count($tabs) > 1) : ?>
                    <div class="m-team__tabs" role="tablist" aria-label="Leadership groups">
                        <?php foreach ($tabs as $key => $label) : ?>
                            <button
                                class="a-text a-text--m"
                                type="button"
                                role="tab"
                                id="<?php echo esc_attr($instance . '-tab-' . $key); ?>"
                                aria-selected="false"
                                data-team-tab="<?php echo esc_attr($key); ?>"
                            >
                                <?php echo esc_html($label); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                <?php
                $panel_groups = $layout === 'board'
                    ? array_filter(
                        $groups,
                        static function ($group) use ($tab_key) {
                            return ($group['tab'] ?? '') === $tab_key;
                        }
                    )
                    : $groups;

                $has_alternates = false;
                $alternate_summaries = [];
                ?>
                <div
                    class="m-team__panel"
                    data-team-panel="<?php echo esc_attr($tab_key); ?>"
                >
                    <?php foreach ($panel_groups as $group) : ?>
                        <?php
                        $members = is_array($group['members'] ?? null) ? array_filter($group['members'], 'is_array') : [];

                        if (!$members) {
                            continue;
                        }

                        $alternate      = !empty($group['alternate']);
                        $has_alternates = $has_alternates || $alternate;
                        if ($alternate) {
                            $group_label = trim((string) ($group['title'] ?? '')) ?: (get_field('team_alternate_label') ?: 'Additional members');
                            $alternate_summaries[] = $group_label . ' (' . count($members) . ')';
                        }
                        $columns        = in_array($group['columns'] ?? '', ['three', 'six'], true) ? $group['columns'] : '';
                        $show_filters   = ($group['show_filters'] ?? '') === 'show';
                        $departments    = [];

                        foreach ($members as $member) {
                            $department = trim((string) ($member['department'] ?? ''));

                            if ($department !== '') {
                                $departments[$department] = true;
                            }
                        }
                        ?>
                        <div
                            class="s-d-b-s s-m-b-xs m-team__group<?php echo $columns ? ' m-team__group--' . esc_attr($columns) : ''; ?>"
                            data-team-group
                            <?php if ($alternate) : ?>data-team-alternate<?php endif; ?>
                        >
                            <?php if (!empty($group['title'])) : ?>
                                <div class="m-team__group-heading">
                                    <h3 class="a-text a-text--xxl h-dark-blue"><?php echo esc_html($group['title']); ?></h3>
                                    <span class="a-text a-text--xs" aria-label="<?php echo esc_attr(count($members) . ' members'); ?>">
                                        <?php echo esc_html(count($members)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($show_filters) : ?>
                                <div class="m-team__filters" aria-label="Filter members by department">
                                    <button class="a-text a-text--s" type="button" data-team-department="" aria-pressed="true">All</button>
                                    <?php foreach (array_keys($departments) as $department) : ?>
                                        <button
                                            class="a-text a-text--m h-medium"
                                            type="button"
                                            data-team-department="<?php echo esc_attr($department); ?>"
                                            aria-pressed="false"
                                        >
                                            <?php echo esc_html($department); ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="m-team__grid">
                                <?php foreach ($members as $member) : ?>
                                    <?php
                                    $link       = $member['link'] ?? [];
                                    $target     = $link['target'] ?? '';
                                    $department = trim((string) ($member['department'] ?? ''));
                                    ?>
                                    <article class="m-team__card" data-team-member-department="<?php echo esc_attr($department); ?>">
                                        <div class="m-team__image">
                                            <?php if (!empty($member['image'])) : ?>
                                                <?php
                                                echo wp_get_attachment_image($member['image'], 'large', false, [
                                                    'loading'  => 'lazy',
                                                    'decoding' => 'async',
                                                ]);
                                                ?>
                                            <?php else : ?>
                                                <div class="m-team__placeholder">
                                                    <span class="a-text a-text--xs h-white">Photo coming soon</span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($layout === 'board' && !empty($member['label'])) : ?>
                                                <div class="m-team__badge">
                                                    <span class="a-text a-text--xs h-dark-blue h-semibold"><?php echo esc_html($member['label']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="m-team__copy">
                                            <?php if ($layout === 'people' && (!empty($member['department']) || !empty($member['label']))) : ?>
                                                <div class="m-team__category">
                                                    <span class="a-text a-text--xs h-semibold">
                                                        <?php echo esc_html(($member['department'] ?? '') ?: ($member['label'] ?? '')); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>

                                            <h4 class="a-text a-text--xxl h-dark-blue h-semibold">
                                                <?php if (!empty($link['url'])) : ?>
                                                    <a
                                                        class="a-team-profile"
                                                        href="<?php echo esc_url($link['url']); ?>"
                                                        target="<?php echo esc_attr($target); ?>"
                                                        <?php if ($target === '_blank') : ?>rel="noopener noreferrer"<?php endif; ?>
                                                    >
                                                        <?php echo esc_html($member['name'] ?? ''); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <?php echo esc_html($member['name'] ?? ''); ?>
                                                <?php endif; ?>
                                            </h4>

                                            <?php if (!empty($member['role'])) : ?>
                                                <p class="a-text a-text--l"><?php echo esc_html($member['role']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($has_alternates) : ?>
                        <div class="m-team__more" data-team-more hidden>
                            <span class="a-text a-text--xxl h-semibold h-dark-blue">
                                <?php echo esc_html(implode(' and ', $alternate_summaries)); ?>
                            </span>
                            <button class="a-btn a-btn--blue a-btn--smallest" type="button" data-team-toggle aria-expanded="false">
                                <span>Show all +</span>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php
            $structure_pdf          = get_field('team_structure_pdf') ?: [];
            $structure_title        = get_field('team_structure_title');
            $structure_description  = get_field('team_structure_description');
            $structure_button_label = get_field('team_structure_button_label');
            ?>
            <?php if (!empty($structure_pdf['url'])) : ?>
                <div class="m-team__structure">
                    <div class="m-team__structure-copy">
                        <?php if ($structure_title) : ?>
                            <h3 class="a-heading a-heading--h6 h-white s-d-b-xs s-m-b-xs"><?php echo esc_html($structure_title); ?></h3>
                        <?php endif; ?>

                        <?php if ($structure_description) : ?>
                            <p class="a-text a-text--m h-white"><?php echo esc_html($structure_description); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($structure_button_label) : ?>
                        <a class="a-btn a-btn--white a-btn--smallest" href="<?php echo esc_url($structure_pdf['url']); ?>" download>
                            <span>
                                <?php echo esc_html($structure_button_label); ?>
                                <span aria-hidden="true">&darr;</span>
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
