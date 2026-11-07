<?php
defined( 'ABSPATH' ) || exit;
add_action( 'after_setup_theme', function () {
    load_theme_textdomain( 'prntlab', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    add_theme_support( 'custom-logo', array( 'height' => 70, 'width' => 360, 'flex-height' => true, 'flex-width' => true ) );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor.css' );
    add_theme_support( 'woocommerce', array( 'thumbnail_image_width' => 600, 'single_image_width' => 1000, 'product_grid' => array( 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 4 ) ) );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    register_nav_menus( array( 'primary' => __( 'Главное меню', 'prntlab' ), 'buyers' => __( 'Покупателям', 'prntlab' ), 'brand' => __( 'Бренд', 'prntlab' ), 'collections' => __( 'Коллекции', 'prntlab' ) ) );
} );
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'prntlab', get_theme_file_uri( 'assets/css/main.css' ), array(), PRNTLAB_VERSION );
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style( 'prntlab-shop', get_theme_file_uri( 'assets/css/shop.css' ), array( 'prntlab' ), PRNTLAB_VERSION );
        wp_enqueue_script( 'wc-cart-fragments' );
    }
    wp_enqueue_script( 'prntlab', get_theme_file_uri( 'assets/js/main.js' ), array(), PRNTLAB_VERSION, true );
    wp_localize_script( 'prntlab', 'prntlabConfig', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'shopUrl' => prntlab_shop_url(), 'wishlistUrl' => prntlab_page_url( 'wishlist' ) ) );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
} );
add_filter( 'body_class', function ( $classes ) { $classes[] = 'prntlab-theme'; return $classes; } );
add_filter( 'excerpt_length', function () { return 26; } );
add_action( 'init', function () {
    if ( class_exists( 'WooCommerce' ) && 'ru_RU' === determine_locale() && ! is_textdomain_loaded( 'woocommerce' ) ) {
        $translation = get_template_directory() . '/languages/woocommerce-ru_RU.mo';
        if ( is_file( $translation ) ) { load_textdomain( 'woocommerce', $translation ); }
    }
}, 0 );
add_filter( 'load_script_translation_file', function ( $file, $handle, $domain ) {
    if ( is_string( $file ) && 'woocommerce' === $domain && 'ru_RU' === determine_locale() ) {
        $local = get_template_directory() . '/languages/' . basename( $file );
        if ( is_file( $local ) ) { return $local; }
    }
    return $file;
}, 10, 3 );
