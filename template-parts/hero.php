<?php defined( 'ABSPATH' ) || exit;
$hero = prntlab_image_url( prntlab_home( 'hero_image' ), 'hero.webp' );
?>
<section class="hero" aria-labelledby="hero-title">
  <img class="hero-image" src="<?php echo esc_url( $hero ); ?>" width="1920" height="640" fetchpriority="high" alt="<?php echo esc_attr( prntlab_home( 'hero_alt' ) ); ?>">
  <div class="container hero-inner"><div class="hero-copy">
    <p class="eyebrow"><?php echo esc_html( prntlab_home( 'hero_eyebrow' ) ); ?></p>
    <h1 id="hero-title"><?php echo esc_html( prntlab_home( 'hero_title' ) ); ?></h1>
    <svg class="hero-underline" viewBox="0 0 220 25" aria-hidden="true"><path d="M3 20C65 3 151 5 98 16s83-8 118-5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
    <p class="hero-description"><?php echo nl2br( esc_html( prntlab_home( 'hero_description' ) ) ); ?></p>
    <a class="button" href="<?php echo esc_url( prntlab_home( 'hero_url' ) ?: prntlab_shop_url() ); ?>"><?php echo esc_html( prntlab_home( 'hero_button' ) ); ?><?php echo prntlab_icon( 'arrow' ); ?></a>
  </div></div>
</section>
