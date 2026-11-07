<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Перейти к содержимому', 'prntlab' ); ?></a>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?> — <?php esc_attr_e( 'Главная', 'prntlab' ); ?>">
    <?php if ( has_custom_logo() ) { $logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' ); echo '<img src="' . esc_url( $logo[0] ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="180" height="36">'; } else { echo 'PRNTLAB<sup>®</sup>'; } ?></a>
    <nav class="primary-navigation" id="primary-navigation" aria-label="<?php esc_attr_e( 'Главное меню', 'prntlab' ); ?>"><?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'fallback_cb' => 'prntlab_primary_fallback', 'depth' => 2 ) ); ?></nav>
    <form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <label for="header-search" class="screen-reader-text"><?php esc_html_e( 'Поиск товаров', 'prntlab' ); ?></label><?php echo prntlab_icon( 'search' ); ?>
      <input id="header-search" type="search" name="s" placeholder="<?php esc_attr_e( 'Поиск по сайту…', 'prntlab' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
      <?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
      <button class="screen-reader-text" type="submit"><?php esc_html_e( 'Найти', 'prntlab' ); ?></button>
    </form>
    <div class="header-actions">
      <a class="icon-button wishlist-header" href="<?php echo esc_url( prntlab_page_url( 'wishlist' ) ); ?>" aria-label="<?php esc_attr_e( 'Избранное', 'prntlab' ); ?>"><?php echo prntlab_icon( 'heart' ); ?><span class="wishlist-count" hidden>0</span></a>
      <a class="icon-button" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Личный кабинет', 'prntlab' ); ?>"><?php echo prntlab_icon( 'user' ); ?></a>
      <?php prntlab_cart_link(); ?>
      <button class="icon-button menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?php esc_attr_e( 'Открыть меню', 'prntlab' ); ?>"><?php echo prntlab_icon( 'menu' ); ?></button>
    </div>
  </div>
</header>
