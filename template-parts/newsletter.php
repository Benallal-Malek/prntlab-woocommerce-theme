<?php defined( 'ABSPATH' ) || exit; ?>
<section class="newsletter" aria-labelledby="newsletter-title"><?php echo prntlab_icon( 'mail', 'newsletter-icon' ); ?><div class="newsletter-copy"><h2 id="newsletter-title"><?php echo esc_html( prntlab_home( 'newsletter_title' ) ); ?></h2><p><?php echo esc_html( prntlab_home( 'newsletter_text' ) ); ?></p></div>
<?php if ( shortcode_exists( 'prntlab_newsletter' ) ) { echo do_shortcode( '[prntlab_newsletter]' ); } else { ?><a class="button" href="<?php echo esc_url( prntlab_page_url( 'contacts' ) ); ?>"><?php esc_html_e( 'Связаться с нами', 'prntlab' ); ?><?php echo prntlab_icon( 'arrow' ); ?></a><?php } ?>
</section>
