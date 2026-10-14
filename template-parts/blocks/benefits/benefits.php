<?php
use MCA\Helpers;

$visibility = Helpers\mca_get_device_visibility();

if ( ! $visibility['show'] ) {
    return;
}

$classes = [
    'o-benefits',
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

$business    = get_field( 'benefits_variant' ) === 'business';
$image       = absint( get_field( 'benefits_image' ) );
$cards       = get_field( 'benefits_cards' ) ?: [];
$notice      = get_field( 'benefits_notice' );
$notice_link = get_field( 'benefits_notice_link' );
$label       = get_field( 'benefits_label' );
$title       = get_field( 'benefits_title' );
$description = get_field( 'benefits_description' );
?>
<section
    class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
    <?php if ( ! empty( $block['anchor'] ) ) : ?>id="<?php echo esc_attr( $block['anchor'] ); ?>"<?php endif; ?>
>
    <div class="container">
        <div class="m-benefits<?php echo $business ? ' is-business' : ''; ?>">

            <?php if ( $notice ) : ?>
                <div class="m-benefits__notice">
                    <p class="a-text a-text--m"><?php echo esc_html( $notice ); ?></p>

                    <?php if ( ! empty( $notice_link['url'] ) ) : ?>
                        <a
                            class="a-text a-text--m"
                            href="<?php echo esc_url( $notice_link['url'] ); ?>"
                            <?php if ( ( $notice_link['target'] ?? '' ) === '_blank' ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                        >
                            <span><?php echo esc_html( $notice_link['title'] ?? '' ); ?></span>
                            <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="m-benefits__header">
                <div class="m-benefits__heading s-d-t-l s-m-t-s">
                    <?php if ( $label ) : ?>
                        <p class="a-badge h-dark-blue h-semibold"><?php echo esc_html( $label ); ?></p>
                    <?php endif; ?>

                    <?php if ( $title ) : ?>
                        <h2 class="a-heading a-heading--h3 h-dark-blue"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                </div>

                <?php if ( $description ) : ?>
                    <p class="a-text a-text--l"><?php echo nl2br( esc_html( $description ) ); ?></p>
                <?php endif; ?>
            </div>

            <div class="m-benefits__layout s-d-t-s s-m-t-xs">
                <?php if ( $image ) : ?>
                    <div class="m-benefits__image">
                        <?php echo wp_get_attachment_image( $image, 'large', false, [ 'loading' => 'lazy' ] ); ?>
                    </div>
                <?php endif; ?>

                <div class="m-benefits__cards">
                    <?php foreach ( array_slice( $cards, 0, 4 ) as $index => $card ) :
                        $tone = in_array( $card['badge_color'] ?? '', [ 'blue', 'purple', 'teal' ], true ) ? $card['badge_color'] : 'blue';
                        $link = $card['link'] ?? [];
                        ?>
                        <div class="m-benefits__card<?php echo 0 === $index ? ' is-featured' : ''; ?>">
                            <div class="m-benefits__meta">
                                <?php if ( ! empty( $card['badge'] ) ) : ?>
                                    <div class="m-benefits__badge is-<?php echo esc_attr( $tone ); ?>">
                                        <span class="a-text a-text--xs h-uppercase h-semibold"><?php echo esc_html( $card['badge'] ); ?></span>
                                    </div>
                                <?php endif; ?>
                                <span class="a-text a-text--s h-semibold"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
                            </div>

                            <div class="m-benefits__copy">
                                <h3 class="a-heading a-heading--h6 h-semibold"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>

                                <?php if ( ! empty( $card['description'] ) ) : ?>
                                    <p class="a-text a-text--m"><?php echo nl2br( esc_html( $card['description'] ) ); ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if ( ! empty( $link['url'] ) ) : ?>
                                <div class="m-benefits__link">
                                    <a
                                        class="a-text a-text--m h-semibold"
                                        href="<?php echo esc_url( $link['url'] ); ?>"
                                        <?php if ( ( $link['target'] ?? '' ) === '_blank' ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                    >
                                        <span><?php echo esc_html( $link['title'] ?? '' ); ?></span>
                                        <i class="icon-arrow-right-up a-icon" aria-hidden="true"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>