<?php
/** Explicit, resumable, local-only demonstration importer. */
defined( 'ABSPATH' ) || exit;
add_action( 'admin_menu', function () { add_theme_page( 'PRNTLAB — быстрый старт', 'PRNTLAB', 'manage_options', 'prntlab-setup', 'prntlab_setup_screen' ); } );
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( 'appearance_page_prntlab-setup' !== $hook ) { return; }
    wp_enqueue_script( 'prntlab-admin', get_theme_file_uri( 'assets/js/admin.js' ), array(), PRNTLAB_VERSION, true );
    wp_localize_script( 'prntlab-admin', 'prntlabImport', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'prntlab_import' ), 'steps' => prntlab_import_steps() ) );
} );
function prntlab_setup_screen() {
    if ( ! current_user_can( 'manage_options' ) ) { return; } ?>
    <div class="wrap" style="max-width:940px"><h1>PRNTLAB — быстрый старт</h1><p>Тема магазина худи по вашему макету. Версия <?php echo esc_html( PRNTLAB_VERSION ); ?>.</p>
    <div style="background:white;border:1px solid #dcdcde;padding:24px;border-radius:12px;margin:20px 0"><h2 style="margin-top:0">1. Плагины</h2><p>WooCommerce: <strong><?php echo class_exists( 'WooCommerce' ) ? 'активен ✓' : 'нужна установка'; ?></strong> · Advanced Custom Fields: <strong><?php echo function_exists( 'get_field' ) ? 'активен ✓' : 'рекомендуется установить'; ?></strong></p><p>Подходит бесплатный ACF. Платные поля не используются.</p><a class="button" href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>">Установить WooCommerce</a> <a class="button" href="https://www.advancedcustomfields.com/" target="_blank" rel="noopener noreferrer">Официальный ACF</a></div>
    <div style="background:white;border:1px solid #dcdcde;padding:24px;border-radius:12px"><h2 style="margin-top:0">2. Демонстрационный магазин</h2><p>Будут созданы 4 вариативных товара с размерами S–XXL, 5 категорий, страницы, меню и 7 изображений в медиатеке. Файлы уже входят в тему; загрузка изображений с внешних сайтов не требуется.</p><p>Повторный запуск пропускает уже созданные объекты и сохраняет внесённые вами изменения. Существующие товары и страницы не удаляются.</p>
    <form id="prntlab-import-form"><p><label><input type="checkbox" name="set_home" value="1" <?php checked( ! get_option( 'page_on_front' ) ); ?>> Сделать демо-страницу главной (изменит настройку «Чтение»)</label></p><p><label><input type="checkbox" name="set_currency" value="1"> Установить рубли, положение знака валюты справа и страну магазина «Россия»</label></p><p><button class="button button-primary button-hero" id="prntlab-import-button" type="submit" <?php disabled( ! class_exists( 'WooCommerce' ) ); ?>>Загрузить демо</button></p></form>
    <progress id="prntlab-import-progress" max="100" value="0" style="display:none;width:100%;height:20px"></progress><div id="prntlab-import-status" role="status" aria-live="polite"></div><ol id="prntlab-import-log" style="max-height:240px;overflow:auto"></ol></div>
    <div style="background:white;border:1px solid #dcdcde;padding:24px;border-radius:12px;margin-top:20px"><h2 style="margin-top:0">3. Редактирование</h2><ul><li><strong>Главная:</strong> Страницы → Главная PRNTLAB → поля PRNTLAB под редактором. Сначала назначьте её главной в «Настройки → Чтение».</li><li><strong>Контакты, соцсети и логотип:</strong> Внешний вид → Настроить.</li><li><strong>Товары, цены, остатки и размеры:</strong> Товары → Все товары → нужный товар.</li><li><strong>Меню:</strong> Внешний вид → Меню. При импорте существующие назначения меню сохраняются.</li><li><strong>Подписка:</strong> установите prntlab-companion.zip из комплекта. Настройте доставку почты WordPress через SMTP.</li></ul><p>Демонстрационные отзывы подписаны как примеры. Перед продажами замените их настоящими, укажите собственные контакты, условия доставки и возврата, опубликуйте свою политику конфиденциальности. Оплата и доставка настраиваются в WooCommerce.</p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener">Открыть сайт</a></div></div>
<?php }
function prntlab_import_steps() {
    $steps = array(); $data = prntlab_demo_data();
    foreach ( $data['media'] as $item ) { $steps[] = array( 'id' => 'media:' . $item['key'], 'label' => 'Медиатека: ' . $item['title'] ); }
    $steps[] = array( 'id' => 'pages', 'label' => 'Страницы магазина' );
    $steps[] = array( 'id' => 'categories', 'label' => 'Категории товаров' );
    foreach ( $data['products'] as $item ) { $steps[] = array( 'id' => 'product:' . $item['slug'], 'label' => 'Товар: ' . $item['name'] ); }
    $steps[] = array( 'id' => 'menus', 'label' => 'Навигация и меню' );
    $steps[] = array( 'id' => 'finish', 'label' => 'Настройки главной и завершение' );
    return $steps;
}
add_action( 'wp_ajax_prntlab_import_step', function () {
    if ( ! current_user_can( 'manage_options' ) ) { wp_send_json_error( array( 'message' => 'Недостаточно прав.' ), 403 ); }
    check_ajax_referer( 'prntlab_import', 'nonce' );
    if ( ! class_exists( 'WooCommerce' ) ) { wp_send_json_error( array( 'message' => 'Активируйте WooCommerce.' ), 400 ); }
    $step = isset( $_POST['step'] ) && is_string( $_POST['step'] ) ? sanitize_text_field( wp_unslash( $_POST['step'] ) ) : '';
    if ( ! in_array( $step, array_column( prntlab_import_steps(), 'id' ), true ) ) { wp_send_json_error( array( 'message' => 'Неизвестный этап.' ), 400 ); }
    $lock = 'prntlab_import_lock'; $since = (int) get_option( $lock );
    if ( $since && time() - $since > 180 ) { delete_option( $lock ); }
    if ( ! add_option( $lock, time(), '', false ) ) { wp_send_json_error( array( 'message' => 'Другой импорт уже выполняется. Подождите и повторите.' ), 409 ); }
    try { $message = prntlab_run_import_step( $step, array( 'set_home' => ! empty( $_POST['set_home'] ), 'set_currency' => ! empty( $_POST['set_currency'] ) ) ); delete_option( $lock ); wp_send_json_success( array( 'message' => $message ) ); }
    catch ( Throwable $e ) { delete_option( $lock ); wp_send_json_error( array( 'message' => $e->getMessage() ), 500 ); }
} );
function prntlab_run_import_step( $step, $settings = array() ) {
    $data = prntlab_demo_data();
    if ( str_starts_with( $step, 'media:' ) ) { $key = substr( $step, 6 ); foreach ( $data['media'] as $item ) { if ( $item['key'] === $key ) { prntlab_import_media( $item ); return $item['title']; } } }
    if ( 'pages' === $step ) { prntlab_import_pages(); return 'Страницы созданы. Существующие страницы сохранены.'; }
    if ( 'categories' === $step ) { foreach ( $data['collections'] as $item ) { $term = get_term_by( 'slug', $item['slug'], 'product_cat' ); if ( ! $term ) { $result = wp_insert_term( $item['title'], 'product_cat', array( 'slug' => $item['slug'], 'description' => $item['description'] ) ); if ( is_wp_error( $result ) ) { throw new RuntimeException( $result->get_error_message() ); } $term = get_term( $result['term_id'], 'product_cat' ); update_term_meta( $term->term_id, 'thumbnail_id', (int) get_option( 'prntlab_media_' . $item['media'] ) ); } } return '5 категорий готовы.'; }
    if ( str_starts_with( $step, 'product:' ) ) { $slug = substr( $step, 8 ); foreach ( $data['products'] as $i => $item ) { if ( $item['slug'] === $slug ) { prntlab_import_product( $item, $i ); return $item['name'] . ' — размеры S, M, L, XL, XXL'; } } }
    if ( 'menus' === $step ) { prntlab_import_menus(); return 'Меню созданы; существующие назначения сохранены.'; }
    if ( 'finish' === $step ) {
        $home_id = (int) get_option( 'prntlab_page_home' );
        if ( ! empty( $settings['set_home'] ) && $home_id ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $home_id ); }
        if ( ! empty( $settings['set_currency'] ) ) { update_option( 'woocommerce_currency', 'RUB' ); update_option( 'woocommerce_currency_pos', 'right_space' ); update_option( 'woocommerce_price_num_decimals', '0' ); update_option( 'woocommerce_price_thousand_sep', ' ' ); update_option( 'woocommerce_price_decimal_sep', ',' ); update_option( 'woocommerce_default_country', 'RU' ); }
        update_option( 'prntlab_demo_version', PRNTLAB_VERSION, false ); flush_rewrite_rules( false ); return 'Готово! Проверьте главную, каталог и настройки WooCommerce.';
    }
    throw new RuntimeException( 'Не удалось выполнить этап.' );
}
function prntlab_import_media( $item ) {
    $old = (int) get_option( 'prntlab_media_' . $item['key'] ); if ( $old && 'attachment' === get_post_type( $old ) && get_attached_file( $old ) && file_exists( get_attached_file( $old ) ) ) { return $old; }
    require_once ABSPATH . 'wp-admin/includes/image.php'; require_once ABSPATH . 'wp-admin/includes/file.php';
    $source = get_template_directory() . '/assets/images/' . basename( $item['file'] );
    if ( ! is_file( $source ) ) { throw new RuntimeException( 'В архиве нет изображения: ' . $item['file'] ); }
    $upload = wp_upload_dir(); if ( $upload['error'] ) { throw new RuntimeException( $upload['error'] ); }
    $filename = wp_unique_filename( $upload['path'], 'prntlab-' . basename( $item['file'] ) ); $path = trailingslashit( $upload['path'] ) . $filename;
    if ( ! copy( $source, $path ) ) { throw new RuntimeException( 'Не удаётся записать изображение в uploads.' ); }
    $mime = wp_check_filetype( $filename ); $id = wp_insert_attachment( array( 'post_title' => $item['title'], 'post_status' => 'inherit', 'post_mime_type' => $mime['type'] ), $path, 0, true );
    if ( is_wp_error( $id ) ) { wp_delete_file( $path ); throw new RuntimeException( $id->get_error_message() ); }
    wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $path ) ); update_post_meta( $id, '_wp_attachment_image_alt', $item['alt'] ); update_post_meta( $id, '_prntlab_demo_asset', $item['key'] ); update_option( 'prntlab_media_' . $item['key'], $id, false ); return $id;
}
function prntlab_set_initial_field( $id, $name, $value ) {
    if ( metadata_exists( 'post', $id, $name ) ) { return; }
    if ( function_exists( 'update_field' ) ) { update_field( 'field_prntlab_' . $name, $value, $id ); }
    else { update_post_meta( $id, $name, $value ); update_post_meta( $id, '_' . $name, 'field_prntlab_' . $name ); }
}
function prntlab_demo_page( $key, $title, $slug, $content, $template = '' ) {
    $id = (int) get_option( 'prntlab_page_' . $key ); if ( $id && 'page' === get_post_type( $id ) && 'trash' !== get_post_status( $id ) ) { return $id; }
    $id = wp_insert_post( array( 'post_title' => $title, 'post_name' => $slug, 'post_content' => $content, 'post_type' => 'page', 'post_status' => 'publish', 'comment_status' => 'closed' ), true );
    if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); }
    update_post_meta( $id, '_prntlab_demo_page', $key ); if ( $template ) { update_post_meta( $id, '_wp_page_template', $template ); } update_option( 'prntlab_page_' . $key, $id, false ); return $id;
}
function prntlab_import_pages() {
    $data = prntlab_demo_data();
    $home_id = prntlab_demo_page( 'home', 'Главная PRNTLAB', 'prntlab-home', '' );
    foreach ( $data['home'] as $key => $value ) { prntlab_set_initial_field( $home_id, $key, $value ); }
    foreach ( array( 'hero_image' => 'hero', 'drop_image' => 'winter' ) as $field => $key ) { prntlab_set_initial_field( $home_id, $field, (int) get_option( 'prntlab_media_' . $key ) ); }
    foreach ( array_slice( $data['collections'], 0, 4 ) as $i => $item ) { $prefix = 'collection_' . ( $i + 1 ) . '_'; foreach ( array( 'title', 'subtitle' ) as $k ) { prntlab_set_initial_field( $home_id, $prefix . $k, $item[ $k ] ); } prntlab_set_initial_field( $home_id, $prefix . 'image', (int) get_option( 'prntlab_media_' . $item['media'] ) ); }
    foreach ( $data['benefits'] as $i => $item ) { foreach ( array( 'title', 'text' ) as $k ) { prntlab_set_initial_field( $home_id, 'benefit_' . ( $i + 1 ) . '_' . $k, $item[ $k ] ); } }
    foreach ( $data['reviews'] as $i => $item ) { foreach ( array( 'name', 'text', 'date' ) as $k ) { prntlab_set_initial_field( $home_id, 'review_' . ( $i + 1 ) . '_' . $k, $item[ $k ] ); } }
    $pages = array(
        'about' => array( 'О PRNTLAB', 'about', '<h2>Носи то, что чувствуешь.</h2><p>PRNTLAB — худи с характером. Мы соединяем свободный крой, комфорт и выразительную графику, чтобы каждый день оставалось место для самовыражения.</p><p>От спокойных японских мотивов до космических историй и смелого стрит-арта — выбирай свою коллекцию.</p>' ),
        'delivery' => array( 'Доставка', 'delivery', '<h2>Твой новый худи уже в пути</h2><p>Доступные способы, стоимость и сроки доставки отображаются при оформлении заказа после указания адреса.</p><p>После отправки заказа вы получите письмо с информацией для отслеживания, если перевозчик предоставляет такую возможность.</p>' ),
        'payment' => array( 'Оплата', 'payment', '<h2>Как оплатить заказ</h2><p>Доступные способы оплаты отображаются при оформлении заказа. Выберите подходящий вариант и следуйте подсказкам платёжного сервиса.</p><p>Подтверждение заказа придёт на указанный email.</p>' ),
        'returns' => array( 'Возврат и обмен', 'returns', '<h2>Не подошёл размер?</h2><p>Свяжитесь с нами и укажите номер заказа. Мы подскажем порядок обмена или возврата, требования к состоянию товара и способ отправки.</p><p>Сохраняйте упаковку и документы о покупке до примерки.</p>' ),
        'contacts' => array( 'Контакты', 'contacts', '<h2>Есть вопрос о заказе?</h2><p>Поможем выбрать размер, разобраться с доставкой и подобрать принт под ваше настроение.</p>' ),
        'privacy' => array( 'Политика конфиденциальности', 'privacy-policy', '<h2>Обработка персональных данных</h2><p>Данные, переданные при оформлении заказа, используются для обработки заказа, оплаты, доставки и связи с покупателем.</p><h2>Подписка на новости</h2><p>Адрес электронной почты для рассылки сохраняется после заполнения формы и подтверждения подписки. Отписаться можно по ссылке в письме подтверждения.</p><h2>Сохранение данных в браузере</h2><p>Избранное сохраняется локально в браузере. WooCommerce использует данные сеанса для работы корзины и оформления заказа.</p>' ),
    );
    foreach ( $pages as $key => $page ) { prntlab_demo_page( $key, $page[0], $page[1], $page[2], 'contacts' === $key ? 'template-contacts.php' : '' ); }
    prntlab_demo_page( 'wishlist', 'Избранное', 'wishlist', '', 'template-wishlist.php' );
    foreach ( array( 'shop' => array( 'Все худи', 'shop', '' ), 'cart' => array( 'Корзина', 'cart', '[woocommerce_cart]' ), 'checkout' => array( 'Оформление заказа', 'checkout', '[woocommerce_checkout]' ), 'myaccount' => array( 'Мой аккаунт', 'my-account', '[woocommerce_my_account]' ) ) as $key => $page ) {
        $option = 'woocommerce_' . $key . '_page_id'; $current = (int) get_option( $option ); if ( ! $current || ! get_post( $current ) || 'trash' === get_post_status( $current ) ) { $id = prntlab_demo_page( $key, $page[0], $page[1], $page[2] ); update_option( $option, $id ); }
    }
    if ( ! get_option( 'wp_page_for_privacy_policy' ) ) { update_option( 'wp_page_for_privacy_policy', get_option( 'prntlab_page_privacy' ) ); }
}
function prntlab_import_product( $item, $index ) {
    $old = (int) get_option( 'prntlab_product_' . $item['slug'] ); if ( $old && 'product' === get_post_type( $old ) && 'trash' !== get_post_status( $old ) ) { if ( ! get_post_meta( $old, '_prntlab_demo_complete', true ) ) { prntlab_complete_product_variations( $old, $item ); prntlab_set_initial_field( $old, 'prntlab_badge', $item['badge'] ); update_post_meta( $old, '_prntlab_demo_complete', 1 ); } return $old; }
    if ( wc_get_product_id_by_sku( $item['sku'] ) ) { throw new RuntimeException( 'SKU ' . $item['sku'] . ' уже занят другим товаром. Измените SKU в demo/content.json или удалите конфликт.' ); }
    $product = new WC_Product_Variable(); $product->set_name( $item['name'] ); $product->set_slug( $item['slug'] ); $product->set_status( 'publish' ); $product->set_sku( $item['sku'] ); $product->set_featured( true ); $product->set_catalog_visibility( 'visible' ); $product->set_menu_order( $index );
    $product->set_description( '<p>' . esc_html( $item['short'] ) . '</p><p>Свободный крой oversize, мягкая изнанка и плотный футер. Удобный капюшон, карман-кенгуру и манжеты в рубчик.</p><p>Состав: 80% хлопок, 20% полиэстер. Плотность: 360 г/м².</p>' ); $product->set_short_description( $item['short'] ); $product->set_image_id( (int) get_option( 'prntlab_media_' . $item['media'] ) ); $product->set_weight( '0.8' );
    $cat_ids = array(); foreach ( $item['categories'] as $slug ) { $term = get_term_by( 'slug', $slug, 'product_cat' ); if ( $term ) { $cat_ids[] = $term->term_id; } } $product->set_category_ids( $cat_ids );
    $size = new WC_Product_Attribute(); $size->set_name( 'Размер' ); $size->set_options( array( 'S', 'M', 'L', 'XL', 'XXL' ) ); $size->set_visible( true ); $size->set_variation( true );
    $color = new WC_Product_Attribute(); $color->set_name( 'Цвет' ); $color->set_options( array( $item['color'] ) ); $color->set_visible( true ); $color->set_variation( false ); $color->set_position( 1 ); $product->set_attributes( array( $size, $color ) );
    $id = $product->save();
    // Save identity before variations so a repeated import cannot duplicate a product.
    update_option( 'prntlab_product_' . $item['slug'], $id, false ); update_post_meta( $id, '_prntlab_demo_product', 1 );
    prntlab_complete_product_variations( $id, $item );
    prntlab_set_initial_field( $id, 'prntlab_badge', $item['badge'] ); prntlab_set_initial_field( $id, 'prntlab_material', 'Хлопок 80%, полиэстер 20% · 360 г/м²' );
    prntlab_set_initial_field( $id, 'prntlab_size_guide', '<p>Свободная посадка oversize. Размеры указаны для изделия, допустимое отклонение 1–2 см.</p><table><thead><tr><th>Размер</th><th>Ширина, см</th><th>Длина, см</th><th>Рукав, см</th></tr></thead><tbody><tr><td>S</td><td>58</td><td>68</td><td>58</td></tr><tr><td>M</td><td>61</td><td>70</td><td>59</td></tr><tr><td>L</td><td>64</td><td>72</td><td>60</td></tr><tr><td>XL</td><td>67</td><td>74</td><td>61</td></tr><tr><td>XXL</td><td>70</td><td>76</td><td>62</td></tr></tbody></table>' );
    prntlab_set_initial_field( $id, 'prntlab_care', "Стирать наизнанку при 30 °C в деликатном режиме.\nНе отбеливать и не сушить в машине.\nГладить с изнанки, не касаясь принта.\nСушить в расправленном виде." ); update_post_meta( $id, '_prntlab_demo_complete', 1 ); return $id;
}
function prntlab_complete_product_variations( $id, $item ) {
    $key = sanitize_title( 'Размер' ); $existing = array();
    $parent = wc_get_product( $id ); foreach ( $parent->get_children() as $child ) { $variation = wc_get_product( $child ); if ( $variation ) { $existing[] = $variation->get_attribute( $key ); } }
    foreach ( array( 'S', 'M', 'L', 'XL', 'XXL' ) as $size ) { if ( in_array( $size, $existing, true ) ) { continue; } $variation = new WC_Product_Variation(); $variation->set_parent_id( $id ); $variation->set_status( 'publish' ); $variation->set_attributes( array( $key => $size ) ); $variation->set_regular_price( $item['price'] ); $variation->set_sku( $item['sku'] . '-' . $size ); $variation->set_manage_stock( true ); $variation->set_stock_quantity( 10 ); $variation->set_stock_status( 'instock' ); $variation->save(); }
    WC_Product_Variable::sync( $id ); wc_delete_product_transients( $id );
}
function prntlab_import_menus() {
    $locations = get_theme_mod( 'nav_menu_locations', array() );
    $menus = array(
        'primary' => array( 'Коллекции' => prntlab_shop_url(), 'Худи' => prntlab_shop_url(), 'Новинки' => add_query_arg( 'orderby', 'date', prntlab_shop_url() ), 'Лимитированные дропы' => prntlab_collection_url( 'limited' ), 'О бренде' => prntlab_page_url( 'about' ) ),
        'buyers' => array( 'Доставка' => prntlab_page_url( 'delivery' ), 'Оплата' => prntlab_page_url( 'payment' ), 'Возврат и обмен' => prntlab_page_url( 'returns' ), 'Избранное' => prntlab_page_url( 'wishlist' ) ),
        'brand' => array( 'О PRNTLAB' => prntlab_page_url( 'about' ), 'Контакты' => prntlab_page_url( 'contacts' ), 'Конфиденциальность' => prntlab_page_url( 'privacy' ) ),
        'collections' => array( 'Все худи' => prntlab_shop_url(), 'Новинки' => add_query_arg( 'orderby', 'date', prntlab_shop_url() ), 'Лимитированные дропы' => prntlab_collection_url( 'limited' ), 'Зимняя коллекция' => prntlab_collection_url( 'winter-drop' ) ),
    );
    foreach ( $menus as $location => $items ) {
        $id = (int) get_option( 'prntlab_menu_' . $location );
        if ( ! $id || ! wp_get_nav_menu_object( $id ) ) { $id = wp_create_nav_menu( 'PRNTLAB — ' . $location . ' ' . wp_date( 'His' ) ); if ( is_wp_error( $id ) ) { throw new RuntimeException( $id->get_error_message() ); } update_option( 'prntlab_menu_' . $location, $id, false ); foreach ( $items as $title => $url ) { wp_update_nav_menu_item( $id, 0, array( 'menu-item-title' => $title, 'menu-item-url' => $url, 'menu-item-status' => 'publish', 'menu-item-type' => 'custom' ) ); } }
        if ( empty( $locations[ $location ] ) ) { $locations[ $location ] = $id; }
    }
    set_theme_mod( 'nav_menu_locations', $locations );
}
