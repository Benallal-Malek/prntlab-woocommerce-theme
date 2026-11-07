<?php defined( 'ABSPATH' ) || exit; $data = prntlab_demo_data(); ?>
<section class="benefits-section" aria-labelledby="benefits-title"><h2 id="benefits-title"><?php echo esc_html( prntlab_home( 'benefits_title' ) ); ?></h2><div class="benefits-grid">
<?php foreach ( $data['benefits'] as $i => $item ) : $n = $i + 1; ?>
  <div class="benefit"><?php echo prntlab_icon( $item['icon'] ); ?><div><h3><?php echo esc_html( prntlab_field( 'benefit_' . $n . '_title', $item['title'] ) ); ?></h3><p><?php echo esc_html( prntlab_field( 'benefit_' . $n . '_text', $item['text'] ) ); ?></p></div></div>
<?php endforeach; ?></div></section>
