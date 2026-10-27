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
$show_ways   = get_field( 'benefits_show_ways' );
$ways        = $show_ways ? ( get_field( 'benefits_ways' ) ?: [] ) : [];
$ways_title  = get_field( 'benefits_ways_title' );

$typography = Helpers\get_block_typography( [
    'header_tag'   => 'h2',
    'header_size'  => 'h3',
    'header_color' => 'h-dark-blue',
    'text_type'    => 'l',
    'text_color'   => 'h-dark-blue',
] );
?>
<section
    class="<?php echo esc_attr( implode( ' ', array_filter( $classes ) ) ); ?>"
    <?php if ( ! empty( $block['anchor'] ) ) : ?>id="<?php echo esc_attr( $block['anchor'] ); ?>"<?php endif; ?>
>
    <div class="container">
        <div class="m-benefits<?php echo $business ? ' is-business' : ''; ?>">

            <?php if ( $notice ) : ?>
                <div class="m-benefits__notice">
                    <p class="a-text a-text--m e-reveal"><?php echo esc_html( $notice ); ?></p>

                    <?php if ( ! empty( $notice_link['url'] ) ) : ?>
                        <a
                            class="a-text a-text--m e-reveal"
                            data-delay="100"
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
                        <p class="a-badge h-dark-blue h-semibold e-reveal e-reveal--text"><?php echo esc_html( $label ); ?></p>
                    <?php endif; ?>

                    <?php if ( $title ) : ?>
                        <<?php echo tag_escape( $typography['header_tag'] ); ?>
                            class="a-heading a-heading--<?php echo esc_attr( $typography['header_size'] ); ?> <?php echo esc_attr( $typography['header_color'] ); ?> h--<?php echo esc_attr( $typography['header_line_height'] ); ?> e-reveal e-reveal--text"
                            data-delay="100"
                        >
                            <?php echo esc_html( $title ); ?>
                        </<?php echo tag_escape( $typography['header_tag'] ); ?>>
                    <?php endif; ?>
                </div>

                <?php if ( $description ) : ?>
                    <p class="a-text a-text--<?php echo esc_attr( $typography['text_type'] ); ?> <?php echo esc_attr( $typography['text_color'] ); ?> h--<?php echo esc_attr( $typography['text_line_height'] ); ?> e-reveal e-reveal--text" data-delay="350">
                        <?php echo nl2br( esc_html( $description ) ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="m-benefits__layout s-d-t-s s-m-t-xs">
                <?php if ( $image ) : ?>
                    <div class="m-benefits__image e-reveal e-reveal--image">
                        <?php echo wp_get_attachment_image( $image, 'large', false, [ 'loading' => 'lazy' ] ); ?>
                    </div>
                <?php endif; ?>

                <div class="m-benefits__cards">
                    <?php foreach ( array_slice( $cards, 0, 4 ) as $index => $card ) :
                        $tone = in_array( $card['badge_color'] ?? '', [ 'blue', 'purple', 'teal' ], true ) ? $card['badge_color'] : 'blue';
                        $link = $card['link'] ?? [];
                        ?>
                        <div class="m-benefits__card<?php echo 0 === $index ? ' is-featured' : ''; ?> e-reveal" data-delay="<?php echo esc_attr( 150 + ( $index % 2 ) * 120 ); ?>">
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

            <?php if ( $ways ) : ?>
                <div class="m-benefits__ways s-d-t-m s-m-t-s">
                    <?php if ( $ways_title ) : ?>
                        <h2 class="a-heading a-heading--h5 h-dark-blue e-reveal e-reveal--text"><?php echo esc_html( $ways_title ); ?></h2>
                    <?php endif; ?>

                    <div class="m-benefits__ways-grid">
                        <?php
                        $way_index = 0;

                        foreach ( array_slice( $ways, 0, 4 ) as $way ) :
                            $way_link = $way['link'] ?? [];

                            if ( empty( $way_link['url'] ) ) {
                                continue;
                            }

                            $way_color = in_array( $way['color'] ?? '', [ 'navy', 'blue', 'purple', 'pink' ], true ) ? $way['color'] : 'navy';
                            $way_delay = 150 + ( $way_index++ % 4 ) * 120;
                            ?>
                            <div class="m-benefits__way is-<?php echo esc_attr( $way_color ); ?> e-reveal" data-delay="<?php echo esc_attr( $way_delay ); ?>">
                                <a
                                    href="<?php echo esc_url( $way_link['url'] ); ?>"
                                    <?php if ( ( $way_link['target'] ?? '' ) === '_blank' ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                                >
                                    <div>
                                        <h3 class="a-text a-text--xxl h-semibold"><?php echo esc_html( $way['title'] ?? ( $way_link['title'] ?? '' ) ); ?></h3>

                                        <?php if ( ! empty( $way['description'] ) ) : ?>
                                            <p class="a-text a-text--m"><?php echo esc_html( $way['description'] ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span aria-hidden="true"><i class="icon-arrow-right-up a-icon"></i></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>