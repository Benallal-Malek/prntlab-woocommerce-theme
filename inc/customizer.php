<?php
defined( 'ABSPATH' ) || exit;
add_action( 'customize_register', function ( $customizer ) {
    $customizer->add_section( 'prntlab_contacts', array( 'title' => __( 'PRNTLAB: контакты и подвал', 'prntlab' ), 'priority' => 35 ) );
    $fields = array(
        'email' => array( 'Email магазина', 'email', 'sanitize_email' ),
        'phone' => array( 'Телефон', 'text', 'sanitize_text_field' ),
        'address' => array( 'Адрес', 'text', 'sanitize_text_field' ),
        'vk' => array( 'Ссылка VK', 'url', 'esc_url_raw' ),
        'telegram' => array( 'Ссылка Telegram', 'url', 'esc_url_raw' ),
        'youtube' => array( 'Ссылка YouTube', 'url', 'esc_url_raw' ),
        'footer_text' => array( 'Текст под логотипом', 'textarea', 'sanitize_textarea_field' ),
    );
    foreach ( $fields as $name => $field ) {
        $customizer->add_setting( 'prntlab_' . $name, array( 'default' => 'footer_text' === $name ? "Худи с авторскими принтами.\nСтиль. Комфорт. Самовыражение." : '', 'sanitize_callback' => $field[2] ) );
        $customizer->add_control( 'prntlab_' . $name, array( 'section' => 'prntlab_contacts', 'label' => $field[0], 'type' => $field[1] ) );
    }
} );
