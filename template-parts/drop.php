<?php defined( 'ABSPATH' ) || exit; ?>
<section class="drop-banner" aria-labelledby="drop-title">
  <img src="<?php echo esc_url( prntlab_image_url( prntlab_home( 'drop_image' ), 'winter.webp' ) ); ?>" width="1920" height="640" loading="lazy" alt="<?php echo esc_attr( prntlab_home( 'drop_alt' ) ); ?>">
  <div class="drop-copy"><p class="eyebrow"><?php echo esc_html( prntlab_home( 'drop_eyebrow' ) ); ?></p><h2 id="drop-title"><?php echo esc_html( prntlab_home( 'drop_title' ) ); ?></h2><p><?php echo nl2br( esc_html( prntlab_home( 'drop_description' ) ) ); ?></p><a class="button button--cream" href="<?php echo esc_url( prntlab_home( 'drop_url' ) ?: prntlab_collection_url( 'winter-drop' ) ); ?>"><?php echo esc_html( prntlab_home( 'drop_button' ) ); ?><?php echo prntlab_icon( 'arrow' ); ?></a></div>
</section>
