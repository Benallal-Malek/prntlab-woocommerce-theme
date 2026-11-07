<?php defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main" class="container page-main <?php echo ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ? 'commerce-page' : ''; ?>">
<?php while ( have_posts() ) : the_post(); ?><article <?php post_class(); ?>><h1 class="page-title"><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); wp_link_pages(); ?></div></article><?php if ( comments_open() || get_comments_number() ) { comments_template(); } endwhile; ?>
</main><?php get_footer(); ?>
