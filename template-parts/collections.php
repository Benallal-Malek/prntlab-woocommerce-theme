<?php defined( 'ABSPATH' ) || exit;
$data = prntlab_demo_data(); ?>
<section class="collections-grid" aria-label="<?php esc_attr_e( 'Коллекции', 'prntlab' ); ?>">
<?php foreach ( array_slice( $data['collections'], 0, 4 ) as $i => $collection ) : $n = $i + 1;
$url = prntlab_field( 'collection_' . $n . '_url', '' ) ?: prntlab_collection_url( $collection['slug'] );
$img = prntlab_image_url( prntlab_field( 'collection_' . $n . '_image' ), $collection['image'] ); ?>
  <a class="collection-card collection-card--<?php echo esc_attr( $collection['slug'] ); ?>" href="<?php echo esc_url( $url ); ?>">
    <img src="<?php echo esc_url( $img ); ?>" width="600" height="340" alt="" loading="lazy">
    <?php if ( 'limited' === $collection['slug'] ) : ?><span class="limited-stamp" aria-hidden="true">LIMITED<br>EDITION</span><?php endif; ?>
    <div class="collection-copy"><h2><?php echo esc_html( prntlab_field( 'collection_' . $n . '_title', $collection['title'] ) ); ?></h2><p><?php echo esc_html( prntlab_field( 'collection_' . $n . '_subtitle', $collection['subtitle'] ) ); ?></p></div>
    <span class="circle-arrow"><?php echo prntlab_icon( 'arrow' ); ?></span>
  </a>
<?php endforeach; ?>
</section>
