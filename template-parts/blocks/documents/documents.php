<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if (!$visibility['show']) {
    return;
}

$classes = [
    'o-documents',
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
    'text_type'          => $text_size,
    'text_color'         => $text_color,
    'text_line_height'   => $text_height,
] = Helpers\get_block_typography([
    'header_color' => 'h-dark-blue',
    'text_type'    => 's',
    'text_color'   => 'h-dark-blue',
]);

$documents = get_field('project_documents') ?: [];
$description = get_field('project_documents_description');
?>
<section id="documents" class="<?php echo esc_attr(implode(' ', array_filter($classes))); ?>">
    <div class="container">
        <?php if ($documents) : ?>
            <div class="m-documents__documents">
                <div class="m-documents__documents-heading">
                    <p class="a-badge h-dark-blue h-semibold e-reveal e-reveal--text"><?php echo esc_html(get_field('project_documents_label')); ?></p>

                    <h2 class="s-d-b-xs s-m-b-xs s-d-t-xs a-heading a-heading--<?php echo esc_attr($size); ?> <?php echo esc_attr($color); ?> h--<?php echo esc_attr($height); ?> e-reveal e-reveal--text" data-delay="100">
                        <?php echo esc_html(get_field('project_documents_title')); ?>
                    </h2>

                    <?php if ($description) : ?>
                        <div class="m-documents__documents-description">
                            <p class="a-text a-text--<?php echo esc_attr($text_size); ?> <?php echo esc_attr($text_color); ?> h--<?php echo esc_attr($text_height); ?> e-reveal e-reveal--text" data-delay="350">
                                <?php echo nl2br(esc_html($description)); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <ul class="m-documents__document-list">
                    <?php $document_index = 0; ?>
                    <?php while (have_rows('project_documents')) : the_row(); ?>
                        <?php
                        $file = get_sub_field('file') ?: [];

                        if (empty($file['url'])) {
                            continue;
                        }

                        $row_prefix = 'project_documents_' . (get_row_index() - 1) . '_';
                        $file_path = wp_parse_url($file['url'], PHP_URL_PATH) ?: '';
                        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                        $mime_type = strtolower($file['mime_type'] ?? '');
                        if (!$mime_type && !empty($file['ID'])) {
                            $mime_type = strtolower((string) get_post_mime_type($file['ID']));
                        }

                        if (in_array($extension, ['xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'csv'], true)) {
                            $document_icon = 'xls';
                        } elseif ($extension === 'pdf') {
                            $document_icon = 'pdf';
                        } elseif (in_array($mime_type, [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel.sheet.macroenabled.12',
                            'application/vnd.ms-excel.sheet.binary.macroenabled.12',
                            'text/csv',
                        ], true)) {
                            $document_icon = 'xls';
                        } elseif ($mime_type === 'application/pdf') {
                            $document_icon = 'pdf';
                        } else {
                            $document_icon = get_sub_field('document_type') === 'xls' ? 'xls' : 'pdf';
                        }

                        $document_title = $block['data'][$row_prefix . 'project_documents_title']
                            ?? $block['data'][$row_prefix . 'title']
                            ?? get_sub_field('project_documents_title');

                        $document_description = $block['data'][$row_prefix . 'project_documents_description']
                            ?? $block['data'][$row_prefix . 'description']
                            ?? get_sub_field('project_documents_description');
                        ?>
                        <li class="e-reveal" data-delay="<?php echo esc_attr(250 + $document_index * 100); ?>">
                            <img class="a-documents-pdf" src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/' . $document_icon . '.svg')); ?>" alt="" aria-hidden="true">

                            <div class="m-documents__document-copy">
                                <h3 class="a-text a-text--xxl h-semibold h-dark-blue"><?php echo esc_html($document_title); ?></h3>
                                <?php if ($document_description) : ?>
                                    <p class="a-text a-text--m h-semibold"><?php echo esc_html($document_description); ?></p>
                                <?php endif; ?>
                            </div>

                            <a
                                class="a-documents-download a-text a-text--s"
                                href="<?php echo esc_url($file['url']); ?>"
                                download
                                aria-label="<?php echo esc_attr('Download ' . $document_title); ?>"
                            >
                                <?php echo esc_html(get_field('project_documents_download_label')); ?>
                                <img src="<?php echo esc_url(get_theme_file_uri('/assets/img/pics/icons/download.svg')); ?>" alt="" aria-hidden="true">
                            </a>
                        </li>
                        <?php $document_index++; ?>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
