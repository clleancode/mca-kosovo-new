<?php

use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-transparency',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

$devices = [
    'mobile'  => 'm',
    'desktop' => 'd',
];

$sides = [
    'top'    => 't',
    'bottom' => 'b',
];

foreach ($devices as $device => $prefix) {
    foreach ($sides as $side => $suffix) {
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

$section_id  = sanitize_title(get_field('transparency_section_id') ?: ($block['anchor'] ?? ''));
$collections = array_slice(get_field('transparency_collections') ?: [], 0, 3);
$label       = get_field('transparency_label');
?>
<section <?php if ($section_id): ?>id="<?php echo esc_attr($section_id); ?>" <?php endif; ?>class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <div class="m-transparency">

            <div class="m-transparency__heading">
                <?php if ($label): ?>
                    <p class="a-badge h-dark-blue s-d-b-xs s-m-b-xs h-semibold">
                        <?php echo esc_html($label); ?>
                    </p>
                <?php endif; ?>

                <<?php echo tag_escape($tag); ?> class="a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?>">
                    <?php echo esc_html(get_field('transparency_title')); ?>
                </<?php echo tag_escape($tag); ?>>
            </div>

            <div class="m-transparency__grid">
                <?php foreach ($collections as $collection):
                    $documents = is_array($collection['documents'] ?? null)
                        ? array_values(array_filter($collection['documents'], static function ($document) {
                            return is_array($document) && !empty($document['file']['url']);
                        }))
                        : [];

                    $limit    = max(1, min(20, (int) $collection['visible_count']));
                    $view_all = $collection['view_all'] ?? [];
                    ?>
                    <article class="m-transparency__card">

                        <div class="m-transparency__card-heading">
                            <h3 class="a-heading a-heading--h6 h-dark-blue h-semibold">
                                <?php echo esc_html($collection['title']); ?>
                            </h3>
                            <div class="m-transparency__count">
                                <span class="a-text a-text--xs h-white">
                                    <?php echo esc_html(count($documents)); ?>
                                </span>
                            </div>
                        </div>

                        <ul class="m-transparency__documents">
                            <?php foreach (array_slice($documents, 0, $limit) as $document):
                                $file = $document['file'];
                                ?>
                                <li>
                                    <div class="m-transparency__document-copy">
                                        <h4 class="a-text a-text--m h-semibold h-dark-blue">
                                            <?php echo esc_html($document['title']); ?>
                                        </h4>
                                        <span class="a-text a-text--xs h-semibold">
                                            <?php echo esc_html(!empty($document['caption']) ? $document['caption'] . ' · PDF' : 'PDF'); ?>
                                        </span>
                                    </div>

                                    <div class="m-transparency__download">
                                        <a class="a-transparency-download"
                                           href="<?php echo esc_url($file['url']); ?>"
                                           download
                                           aria-label="<?php echo esc_attr('Download ' . $document['title']); ?>">
                                            <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.89844 0.898438V10.8984M1.89844 6.89844L6.89844 11.8984L11.8984 6.89844M0.898438 14.8984H12.8984"
                                                      stroke="#001F54"
                                                      stroke-width="1.8"
                                                      stroke-linecap="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <?php if (!empty($view_all['url'])): ?>
                            <div class="m-transparency__view-all h-semibold">
                                <a class="a-text a-text--l"
                                   href="<?php echo esc_url($view_all['url']); ?>"
                                   <?php if (!empty($view_all['target'])): ?>target="<?php echo esc_attr($view_all['target']); ?>"<?php endif; ?>
                                   <?php if (($view_all['target'] ?? '') === '_blank'): ?>rel="noopener noreferrer"<?php endif; ?>>
                                    <?php echo esc_html($view_all['title']); ?>
                                    <span aria-hidden="true">→</span>
                                </a>
                            </div>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</section>