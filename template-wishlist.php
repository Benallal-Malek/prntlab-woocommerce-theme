<?php
/** Template Name: Избранное PRNTLAB */
defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main" class="container page-main woocommerce"><div class="section-heading"><h1 class="page-title"><?php the_title(); ?></h1><a class="text-link" href="<?php echo esc_url( prntlab_shop_url() ); ?>"><?php esc_html_e( 'В каталог', 'prntlab' ); ?><?php echo prntlab_icon( 'arrow' ); ?></a></div><div id="wishlist-products" aria-live="polite"><p><?php esc_html_e( 'Загружаем избранное…', 'prntlab' ); ?></p></div><noscript><p><?php esc_html_e( 'Для избранного включите JavaScript.', 'prntlab' ); ?></p></noscript><p class="muted"><?php esc_html_e( 'Список хранится в этом браузере на вашем устройстве.', 'prntlab' ); ?></p></main><?php get_footer(); ?>
