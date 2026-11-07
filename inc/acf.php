<?php
defined( 'ABSPATH' ) || exit;
function prntlab_acf_field( $name, $label, $type = 'text', $extra = array() ) {
    return array_merge( array( 'key' => 'field_prntlab_' . $name, 'name' => $name, 'label' => $label, 'type' => $type ), $extra );
}
function prntlab_acf_groups() {
    $data = prntlab_demo_data(); $fields = array();
    $fields[] = prntlab_acf_field( 'tab_hero', 'Первый экран', 'tab' );
    $labels = array( 'hero_eyebrow' => 'Надпись над заголовком', 'hero_title' => 'Заголовок', 'hero_description' => 'Описание', 'hero_button' => 'Текст кнопки', 'hero_url' => 'Ссылка кнопки (пусто = магазин)', 'hero_alt' => 'Описание изображения для доступности' );
    foreach ( $labels as $name => $label ) { $fields[] = prntlab_acf_field( $name, $label, str_contains( $name, 'description' ) ? 'textarea' : ( str_ends_with( $name, '_url' ) ? 'url' : 'text' ), array( 'default_value' => $data['home'][ $name ] ?? '', 'rows' => 3 ) ); }
    $fields[] = prntlab_acf_field( 'hero_image', 'Фоновое изображение', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Рекомендуется широкое фото 1920 × 640 px, модель справа, свободное место слева.' ) );
    $fields[] = prntlab_acf_field( 'tab_collections', 'Коллекции', 'tab' );
    foreach ( array_slice( $data['collections'], 0, 4 ) as $i => $item ) { $prefix = 'collection_' . ( $i + 1 ) . '_'; $fields[] = prntlab_acf_field( $prefix . 'title', 'Коллекция ' . ( $i + 1 ) . ': заголовок', 'text', array( 'default_value' => $item['title'] ) ); $fields[] = prntlab_acf_field( $prefix . 'subtitle', 'Подпись', 'text', array( 'default_value' => $item['subtitle'] ) ); $fields[] = prntlab_acf_field( $prefix . 'image', 'Изображение', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) ); $fields[] = prntlab_acf_field( $prefix . 'url', 'Ссылка (пусто = категория демо)', 'url' ); }
    $fields[] = prntlab_acf_field( 'tab_products', 'Товары', 'tab' );
    $fields[] = prntlab_acf_field( 'products_title', 'Заголовок секции', 'text', array( 'default_value' => $data['home']['products_title'] ) );
    $fields[] = prntlab_acf_field( 'featured_products', 'Товары на главной', 'post_object', array( 'post_type' => array( 'product' ), 'return_format' => 'id', 'multiple' => 1, 'ui' => 1, 'instructions' => 'До 4 товаров. Без выбора показываются рекомендуемые товары WooCommerce, затем первые товары каталога.' ) );
    $fields[] = prntlab_acf_field( 'tab_drop', 'Баннер дропа', 'tab' );
    foreach ( array( 'drop_eyebrow' => 'Надпись над заголовком', 'drop_title' => 'Заголовок дропа', 'drop_description' => 'Описание', 'drop_button' => 'Текст кнопки', 'drop_url' => 'Ссылка (пусто = зимняя коллекция)', 'drop_alt' => 'Описание изображения для доступности' ) as $name => $label ) { $fields[] = prntlab_acf_field( $name, $label, str_contains( $name, 'description' ) ? 'textarea' : ( str_ends_with( $name, '_url' ) ? 'url' : 'text' ), array( 'default_value' => $data['home'][ $name ] ?? '', 'rows' => 3 ) ); }
    $fields[] = prntlab_acf_field( 'drop_image', 'Фон баннера', 'image', array( 'return_format' => 'id', 'preview_size' => 'medium' ) );
    $fields[] = prntlab_acf_field( 'tab_benefits', 'Преимущества', 'tab' );
    $fields[] = prntlab_acf_field( 'benefits_title', 'Заголовок секции', 'text', array( 'default_value' => $data['home']['benefits_title'] ) );
    foreach ( $data['benefits'] as $i => $item ) { $prefix = 'benefit_' . ( $i + 1 ) . '_'; $fields[] = prntlab_acf_field( $prefix . 'title', 'Преимущество ' . ( $i + 1 ), 'text', array( 'default_value' => $item['title'] ) ); $fields[] = prntlab_acf_field( $prefix . 'text', 'Описание', 'textarea', array( 'default_value' => $item['text'], 'rows' => 2 ) ); }
    $fields[] = prntlab_acf_field( 'tab_reviews', 'Отзывы', 'tab' );
    $fields[] = prntlab_acf_field( 'reviews_title', 'Заголовок секции', 'text', array( 'default_value' => $data['home']['reviews_title'] ) );
    foreach ( $data['reviews'] as $i => $item ) { $prefix = 'review_' . ( $i + 1 ) . '_'; foreach ( array( 'name' => 'Имя', 'text' => 'Текст', 'date' => 'Дата / подпись' ) as $key => $label ) { $fields[] = prntlab_acf_field( $prefix . $key, 'Отзыв ' . ( $i + 1 ) . ': ' . $label, 'text' === $key ? 'textarea' : 'text', array( 'default_value' => $item[ $key ], 'rows' => 3 ) ); } $fields[] = prntlab_acf_field( $prefix . 'avatar', 'Аватар', 'image', array( 'return_format' => 'id', 'preview_size' => 'thumbnail' ) ); }
    $fields[] = prntlab_acf_field( 'reviews_note', 'Примечание под отзывами', 'text', array( 'default_value' => $data['home']['reviews_note'], 'instructions' => 'Удалите подпись только после замены примеров реальными отзывами.' ) );
    $fields[] = prntlab_acf_field( 'tab_newsletter', 'Подписка', 'tab' );
    foreach ( array( 'newsletter_title' => 'Заголовок', 'newsletter_text' => 'Описание' ) as $name => $label ) { $fields[] = prntlab_acf_field( $name, $label, 'text', array( 'default_value' => $data['home'][ $name ] ) ); }
    $home = array( 'key' => 'group_prntlab_home', 'title' => 'PRNTLAB — содержимое главной', 'fields' => $fields, 'location' => array( array( array( 'param' => 'page_type', 'operator' => '==', 'value' => 'front_page' ) ) ), 'style' => 'default', 'position' => 'normal' );
    $product = array( 'key' => 'group_prntlab_product', 'title' => 'PRNTLAB — оформление товара', 'fields' => array( prntlab_acf_field( 'prntlab_badge', 'Бейдж карточки', 'select', array( 'choices' => array( 'Хит' => 'Хит', 'Новинка' => 'Новинка', 'Лимитка' => 'Лимитка' ), 'allow_null' => 1 ) ), prntlab_acf_field( 'prntlab_material', 'Состав и плотность ткани' ), prntlab_acf_field( 'prntlab_size_guide', 'Размерная сетка', 'wysiwyg', array( 'toolbar' => 'basic', 'media_upload' => 0 ) ), prntlab_acf_field( 'prntlab_care', 'Уход', 'textarea', array( 'rows' => 4 ) ) ), 'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ) ) ) );
    return array( $home, $product );
}
add_action( 'acf/init', function () { foreach ( prntlab_acf_groups() as $group ) { acf_add_local_field_group( $group ); } } );
