<?php
defined( 'ABSPATH' ) || exit;
function prntlab_cart_link() {
    $count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
    echo '<a class="icon-button cart-link" href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'Корзина', 'prntlab' ) . '">' . prntlab_icon( 'cart' ) . '<span class="cart-count">' . esc_html( $count ) . '</span></a>';
}
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) { ob_start(); prntlab_cart_link(); $fragments['a.cart-link'] = ob_get_clean(); return $fragments; } );
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 12; } );
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) { return $product->is_type( 'variable' ) ? __( 'Выбрать размер', 'prntlab' ) : $text; }, 10, 2 );
add_action( 'after_setup_theme', function () {
    if ( ! class_exists( 'WooCommerce' ) ) { return; }
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
    add_action( 'woocommerce_before_shop_loop_item', 'prntlab_product_card_start', 5 );
    add_action( 'woocommerce_before_shop_loop_item_title', 'prntlab_product_badge', 8 );
    add_action( 'woocommerce_after_shop_loop_item', function () { echo '<div class="product-card-actions">'; }, 8 );
    add_action( 'woocommerce_after_shop_loop_item', function () { global $product; echo '<a class="product-details-link" href="' . esc_url( $product->get_permalink() ) . '">' . esc_html__( 'Подробнее', 'prntlab' ) . '</a>'; }, 15 );
    add_action( 'woocommerce_after_shop_loop_item', function () { echo '</div></div>'; }, 20 );
} );
function prntlab_product_card_start() {
    global $product;
    echo '<div class="product-card-inner"><button class="wishlist-toggle" type="button" data-wishlist-id="' . esc_attr( $product->get_id() ) . '" aria-label="' . esc_attr__( 'Добавить в избранное', 'prntlab' ) . '" aria-pressed="false">' . prntlab_icon( 'heart' ) . '</button>';
}
function prntlab_product_badge() {
    global $product;
    $badge = prntlab_field( 'prntlab_badge', '', $product->get_id() );
    if ( ! $badge && $product->is_on_sale() ) { $badge = __( 'Скидка', 'prntlab' ); }
    if ( $badge ) { echo '<span class="product-badge ' . ( 'Хит' === $badge ? 'product-badge--gold' : '' ) . '">' . esc_html( $badge ) . '</span>'; }
}
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
    global $product;
    if ( ! $product ) { return $tabs; }
    if ( prntlab_field( 'prntlab_size_guide', '', $product->get_id() ) ) { $tabs['prntlab_sizes'] = array( 'title' => __( 'Размерная сетка', 'prntlab' ), 'priority' => 25, 'callback' => 'prntlab_size_tab' ); }
    if ( prntlab_field( 'prntlab_care', '', $product->get_id() ) ) { $tabs['prntlab_care'] = array( 'title' => __( 'Уход', 'prntlab' ), 'priority' => 26, 'callback' => 'prntlab_care_tab' ); }
    return $tabs;
} );
function prntlab_size_tab() { global $product; echo '<div class="entry-content">' . wp_kses_post( wpautop( prntlab_field( 'prntlab_size_guide', '', $product->get_id() ) ) ) . '</div>'; }
function prntlab_care_tab() { global $product; echo '<div class="entry-content">' . wp_kses_post( wpautop( prntlab_field( 'prntlab_care', '', $product->get_id() ) ) ) . '</div>'; }
add_action( 'woocommerce_single_product_summary', function () { global $product; $value = prntlab_field( 'prntlab_material', '', $product->get_id() ); if ( $value ) { echo '<p class="product-material">' . prntlab_icon( 'layers' ) . esc_html( $value ) . '</p>'; } }, 21 );
add_action( 'wp_ajax_prntlab_wishlist', 'prntlab_wishlist_response' );
add_action( 'wp_ajax_nopriv_prntlab_wishlist', 'prntlab_wishlist_response' );
function prntlab_wishlist_response() {
    if ( ! function_exists( 'wc_get_products' ) ) { wp_send_json_error( array( 'message' => 'WooCommerce недоступен' ), 503 ); }
    $raw = isset( $_GET['ids'] ) && is_string( $_GET['ids'] ) ? sanitize_text_field( wp_unslash( $_GET['ids'] ) ) : '';
    $ids = array_slice( array_unique( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) ), 0, 48 );
    $items = $ids ? wc_get_products( array( 'include' => $ids, 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 48, 'orderby' => 'post__in' ) ) : array();
    ob_start();
    if ( $items ) { wc_set_loop_prop( 'columns', 4 ); woocommerce_product_loop_start(); foreach ( $items as $item ) { $obj = get_post( $item->get_id() ); setup_postdata( $GLOBALS['post'] = $obj ); wc_setup_product_data( $obj ); wc_get_template_part( 'content', 'product' ); } woocommerce_product_loop_end(); wp_reset_postdata(); wc_reset_loop(); }
    else { echo '<div class="empty-state"><p>' . esc_html__( 'Сохранённые товары больше не доступны.', 'prntlab' ) . '</p></div>'; }
    wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
