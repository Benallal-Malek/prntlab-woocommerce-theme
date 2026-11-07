<?php
/** Template Name: Контакты PRNTLAB */
defined( 'ABSPATH' ) || exit; get_header(); ?>
<main id="main" class="container page-main narrow"><?php while ( have_posts() ) : the_post(); ?><h1 class="page-title"><?php the_title(); ?></h1><div class="entry-content"><?php the_content(); ?>
<?php $email = get_theme_mod( 'prntlab_email' ); $phone = get_theme_mod( 'prntlab_phone' ); $address = get_theme_mod( 'prntlab_address' ); ?>
<?php if ( $email ) : ?><p><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></p><?php endif; ?>
<?php if ( $phone ) : ?><p><strong><?php esc_html_e( 'Телефон:', 'prntlab' ); ?></strong> <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p><?php endif; ?>
<?php if ( $address ) : ?><p><strong><?php esc_html_e( 'Адрес:', 'prntlab' ); ?></strong> <?php echo esc_html( $address ); ?></p><?php endif; ?></div><?php endwhile; ?></main><?php get_footer(); ?>
