<?php defined( 'ABSPATH' ) || exit; ?>
<section class="popular-section woocommerce" aria-labelledby="popular-title">
  <div class="section-heading"><h2 id="popular-title"><?php echo esc_html( prntlab_home( 'products_title' ) ); ?></h2><a class="text-link" href="<?php echo esc_url( prntlab_shop_url() ); ?>"><?php esc_html_e( 'Смотреть все худи', 'prntlab' ); ?><?php echo prntlab_icon( 'arrow' ); ?></a></div>
  <?php if ( class_exists( 'WooCommerce' ) ) :
    $chosen = prntlab_field( 'featured_products', array() );
    $args = array( 'status' => 'publish', 'limit' => 4, 'visibility' => 'catalog', 'orderby' => 'menu_order', 'order' => 'ASC' );
    if ( is_array( $chosen ) && $chosen ) { $args['include'] = array_map( 'absint', $chosen ); $args['orderby'] = 'post__in'; }
    else { $args['featured'] = true; }
    $products = wc_get_products( $args );
    if ( ! $products && empty( $chosen ) ) { unset( $args['featured'] ); $products = wc_get_products( $args ); }
    if ( $products ) : wc_set_loop_prop( 'columns', 4 ); woocommerce_product_loop_start();
      foreach ( $products as $item ) { $post_object = get_post( $item->get_id() ); setup_postdata( $GLOBALS['post'] = $post_object ); wc_setup_product_data( $post_object ); wc_get_template_part( 'content', 'product' ); }
      woocommerce_product_loop_end(); wp_reset_postdata(); wc_reset_loop();
    else : ?><div class="empty-state"><p><?php esc_html_e( 'Добавьте товары в WooCommerce или загрузите демо через «Внешний вид → PRNTLAB».', 'prntlab' ); ?></p></div><?php endif;
  else : ?><div class="empty-state"><p><?php esc_html_e( 'Активируйте WooCommerce, чтобы открыть каталог и корзину.', 'prntlab' ); ?></p></div><?php endif; ?>
</section>
