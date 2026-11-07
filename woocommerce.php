<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main" class="container shop-main woocommerce">
<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) { woocommerce_breadcrumb(); } ?>
<?php woocommerce_content(); ?>
</main><?php get_footer(); ?>
