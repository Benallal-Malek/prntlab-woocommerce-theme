<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main">
  <?php get_template_part( 'template-parts/hero' ); ?>
  <div class="container home-content">
    <?php get_template_part( 'template-parts/collections' ); ?>
    <?php get_template_part( 'template-parts/products' ); ?>
    <?php get_template_part( 'template-parts/drop' ); ?>
    <?php get_template_part( 'template-parts/benefits' ); ?>
    <?php get_template_part( 'template-parts/reviews' ); ?>
    <?php get_template_part( 'template-parts/newsletter' ); ?>
  </div>
</main>
<?php get_footer(); ?>
