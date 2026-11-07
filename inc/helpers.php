<?php
defined( 'ABSPATH' ) || exit;
function prntlab_demo_data() {
    static $data;
    if ( null === $data ) { $data = json_decode( file_get_contents( get_template_directory() . '/demo/content.json' ), true ); }
    return is_array( $data ) ? $data : array();
}
function prntlab_field( $name, $fallback = '', $post_id = 0 ) {
    $post_id = $post_id ?: (int) get_option( 'page_on_front' );
    if ( function_exists( 'get_field' ) && $post_id ) {
        $value = get_field( $name, $post_id );
        if ( null !== $value && false !== $value && '' !== $value ) { return $value; }
        // An intentionally empty saved value should remain empty.
        if ( metadata_exists( 'post', $post_id, $name ) ) { return $value; }
    }
    if ( $post_id && metadata_exists( 'post', $post_id, $name ) ) { return get_post_meta( $post_id, $name, true ); }
    return $fallback;
}
function prntlab_home( $name ) {
    $data = prntlab_demo_data();
    return prntlab_field( $name, $data['home'][ $name ] ?? '' );
}
function prntlab_asset( $file ) { return get_theme_file_uri( 'assets/images/' . $file ); }
function prntlab_image_url( $value, $fallback = '', $size = 'full' ) {
    if ( is_numeric( $value ) && (int) $value > 0 ) { $url = wp_get_attachment_image_url( (int) $value, $size ); if ( $url ) { return $url; } }
    if ( is_array( $value ) && ! empty( $value['url'] ) ) { return $value['url']; }
    if ( is_string( $value ) && preg_match( '~^https?://~i', $value ) ) { return $value; }
    return $fallback ? prntlab_asset( $fallback ) : '';
}
function prntlab_shop_url() { return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ); }
function prntlab_page_url( $key ) {
    $id = (int) get_option( 'prntlab_page_' . $key );
    if ( $id && 'publish' === get_post_status( $id ) ) { return get_permalink( $id ); }
    $slugs = array( 'about' => 'about', 'delivery' => 'delivery', 'payment' => 'payment', 'returns' => 'returns', 'contacts' => 'contacts', 'privacy' => 'privacy-policy', 'wishlist' => 'wishlist' );
    $page = get_page_by_path( $slugs[ $key ] ?? sanitize_title( $key ) );
    return $page ? get_permalink( $page ) : home_url( '/' . ( $slugs[ $key ] ?? sanitize_title( $key ) ) . '/' );
}
function prntlab_collection_url( $slug ) {
    if ( taxonomy_exists( 'product_cat' ) ) { $term = get_term_by( 'slug', $slug, 'product_cat' ); if ( $term ) { $url = get_term_link( $term ); if ( ! is_wp_error( $url ) ) { return $url; } } }
    return prntlab_shop_url();
}
function prntlab_icon( $name, $class = '' ) {
    $paths = array(
        'arrow' => '<path d="M4 12h16m-6-6 6 6-6 6"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',
        'user' => '<circle cx="12" cy="7" r="4"/><path d="M4 22v-3a8 8 0 0 1 16 0v3Z"/>',
        'cart' => '<path d="M2 3h3l3 13h11l3-10H6"/><circle cx="9" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/>',
        'layers' => '<path d="m12 2 10 5-10 5L2 7Zm-10 11 10 5 10-5M2 19l10 5 10-5"/>',
        'image' => '<rect x="2" y="3" width="20" height="18" rx="2"/><circle cx="8" cy="8" r="1.5"/><path d="m3 18 6-6 4 4 4-5 5 6"/>',
        'truck' => '<path d="M1 4h13v13H1Zm13 4h5l4 5v4h-9"/><circle cx="6" cy="19" r="3"/><circle cx="19" cy="19" r="3"/>',
        'return' => '<path d="M3 10a9 9 0 0 1 15-6l3 3M21 2v5h-5M21 14A9 9 0 0 1 6 20l-3-3M3 22v-5h5"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m3 5 9 7 9-7"/>',
        'phone' => '<path d="m7 2 4 6-3 3a17 17 0 0 0 5 5l3-3 6 4-2 5C9 22 2 15 2 4Z"/>',
        'pin' => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'close' => '<path d="m5 5 14 14M19 5 5 19"/>',
        'check' => '<path d="m4 12 5 5L20 6"/>',
        'chevron' => '<path d="m6 9 6 6 6-6"/>',
    );
    return '<svg class="icon ' . esc_attr( $class ) . '" width="24" height="24" viewBox="0 0 24 26" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . ( $paths[ $name ] ?? $paths['arrow'] ) . '</svg>';
}
function prntlab_primary_fallback() {
    echo '<ul class="primary-menu"><li><a href="' . esc_url( prntlab_shop_url() ) . '">' . esc_html__( 'Коллекции', 'prntlab' ) . '</a></li><li><a href="' . esc_url( prntlab_shop_url() ) . '">' . esc_html__( 'Худи', 'prntlab' ) . '</a></li><li><a href="' . esc_url( add_query_arg( 'orderby', 'date', prntlab_shop_url() ) ) . '">' . esc_html__( 'Новинки', 'prntlab' ) . '</a></li><li><a href="' . esc_url( prntlab_collection_url( 'limited' ) ) . '">' . esc_html__( 'Лимитированные дропы', 'prntlab' ) . '</a></li><li><a href="' . esc_url( prntlab_page_url( 'about' ) ) . '">' . esc_html__( 'О бренде', 'prntlab' ) . '</a></li></ul>';
}
