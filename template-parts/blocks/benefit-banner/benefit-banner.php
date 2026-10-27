<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if ( ! $visibility['show'] ) {
    return;
}

$classes = [
    'o-benefit-banner',
    $visibility['visibility_classes'],
    $block['className'] ?? '',
];

foreach ( [ 'mobile' => 'm', 'desktop' => 'd' ] as $device => $prefix ) {
    foreach ( [ 'top' => 't', 'bottom' => 'b' ] as $side => $suffix ) {
        if ( $space = get_field( "space_{$device}_{$side}" ) ) {
            $classes[] = "s-{$prefix}-{$suffix}-{$space}";
        }
    }
}

$cards       = get_field( 'benefit_banner_cards' ) ?: [];
$scroll      = get_field( 'benefit_banner_scroll_link' );
$breadcrumb  = get_field( 'benefit_banner_breadcrumb' );
$title       = get_field( 'benefit_banner_title' );
$description = get_field( 'benefit_banner_description' );
$heading = Helpers\get_block_typography(['header_tag' => 'h1', 'header_size' => 'h2', 'header_color' => 'h-white']);
$heading_classes = 'a-heading a-heading--' . $heading['header_size'] . ' ' . $heading['header_color'];
if (get_field('header_line_height')) $heading_classes .= ' h--' . $heading['header_line_height'];
?>
<section
    class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
    <?php if ( ! empty( $block['anchor'] ) ) : ?>id="<?php echo esc_attr( $block['anchor'] ); ?>"<?php endif; ?>
>
    <div class="m-benefit-banner">
        <div class="m-benefit-banner__intro">
            <div class="m-benefit-banner__content">
                <nav class="m-benefit-banner__breadcrumbs" aria-label="Breadcrumb">
                    <a class="a-text a-text--xs h-uppercase e-reveal" data-offset="0" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php esc_html_e( 'Home', 'MCA' ); ?>
                    </a>
                    <span class="a-text a-text--xs e-reveal" data-offset="0" data-delay="80" aria-hidden="true">/</span>
                    <?php if ( $breadcrumb ) : ?>
                        <span class="a-text a-text--xs h-uppercase e-reveal" data-offset="0" data-delay="160" aria-current="page">
                            <?php echo esc_html( $breadcrumb ); ?>
                        </span>
                    <?php endif; ?>
                </nav>

                <?php if ( $title ) : ?>
                    <<?php echo tag_escape($heading['header_tag']); ?> class="<?php echo esc_attr($heading_classes); ?> e-reveal e-reveal--text" data-offset="0" data-delay="100"><?php echo esc_html( $title ); ?></<?php echo tag_escape($heading['header_tag']); ?>>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                    <p class="a-text a-text--l h-white e-reveal e-reveal--text" data-offset="0" data-delay="350"><?php echo nl2br( esc_html( $description ) ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $scroll['url'] ) ) : ?>
                <div class="m-benefit-banner__scroll e-reveal" data-offset="0" data-delay="500">
                    <a
                        href="<?php echo esc_url( $scroll['url'] ); ?>"
                        <?php if ( ! empty( $scroll['title'] ) ) : ?>aria-label="<?php echo esc_attr( $scroll['title'] ); ?>"<?php endif; ?>
                        <?php if ( ( $scroll['target'] ?? '' ) === '_blank' ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                    >
                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php foreach ( array_slice( $cards, 0, 2 ) as $index => $card ) :
            $image = absint( $card['image'] ?? 0 );
            $link  = $card['link'] ?? [];
            ?>
            <div class="m-benefit-banner__card<?php echo 1 === $index ? ' is-business' : ''; ?>">
                <?php if ( $image ) : ?>
                    <div class="m-benefit-banner__image e-reveal e-reveal--image" data-offset="0" data-delay="<?php echo esc_attr( 150 + $index * 120 ); ?>">
                        <?php echo wp_get_attachment_image( $image, 'full', false, [ 'loading' => 'eager' ] ); ?>
                    </div>
                <?php endif; ?>

                <div class="m-benefit-banner__content">
                    <?php if ( ! empty( $card['label'] ) ) : ?>
                        <p class="a-text a-text--xs h-uppercase h-semibold e-reveal e-reveal--text" data-offset="0" data-delay="<?php echo esc_attr( 400 + $index * 120 ); ?>"><?php echo esc_html( $card['label'] ); ?></p>
                    <?php endif; ?>

                    <h2 class="a-heading a-heading--h3 h-white e-reveal e-reveal--text" data-offset="0" data-delay="<?php echo esc_attr( 500 + $index * 120 ); ?>"><?php echo esc_html( $card['title'] ?? '' ); ?></h2>

                    <?php if ( ! empty( $card['description'] ) ) : ?>
                        <p class="a-text a-text--l h-white e-reveal e-reveal--text" data-offset="0" data-delay="<?php echo esc_attr( 750 + $index * 120 ); ?>"><?php echo nl2br( esc_html( $card['description'] ) ); ?></p>
                    <?php endif; ?>

                    <?php if ( ! empty( $link['url'] ) ) : ?>
                        <div class="m-benefit-banner__link e-reveal" data-offset="0" data-delay="<?php echo esc_attr( 900 + $index * 120 ); ?>">
                            <a
                                class="a-text a-text--m"
                                href="<?php echo esc_url( $link['url'] ); ?>"
                                <?php if ( ( $link['target'] ?? '' ) === '_blank' ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                            >
                                <span><?php echo esc_html( $link['title'] ?? '' ); ?></span>
                                <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
