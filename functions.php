<?php
/** PRNTLAB theme bootstrap. */
defined( 'ABSPATH' ) || exit;
define( 'PRNTLAB_VERSION', '1.0.0' );
foreach ( array( 'helpers', 'setup', 'customizer', 'acf', 'woocommerce', 'demo-import' ) as $prntlab_module ) {
    require_once get_template_directory() . '/inc/' . $prntlab_module . '.php';
}
