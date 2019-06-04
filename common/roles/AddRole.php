<?php

namespace FseoOuter\common\roles;

/**
 * Class AddRole
 * @package FseoOuter\common\roles
 */
class AddRole
{
    /**
     * Добавление ролей, работает при активации плагина
     */
    public static function addRoleLink()
    {
        new FseoRole('publishv21', 'publishv21', [
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => false, // редактирование категорий
            'upload_files' => true, // загрузка файлов
        ]);

        new FseoRole('publishv22', 'publishv22', [
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => true, // редактирование категорий
            'upload_files' => true, // загрузка файлов
        ]);

        new FseoRole('publishv23', 'publishv23', [
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => true, // редактирование категорий
            'upload_files' => true, // загрузка файлов
        ]);

        new FseoRole('wambleChecker', 'wambleChecker', [
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'edit_published_posts' => true, // редактирование опубликованных записей,
            'create_posts' => false, // создание новых записей
            'manage_categories' => false, // редактирование категорий
            'upload_files' => true, // загрузка файлов
        ]);
    }

    /**
     * Удаление лишнего функционала
     */
    public static function metaBoxInit()
    {
        if (self::checkUserRole('wambleChecker')) {
            add_action('admin_menu', [__CLASS__, 'wambleCheckerMetaboxes']);
        }
        if (self::checkUserRole('publishv21')) { // роль publishv21
            add_action('admin_menu', [__CLASS__, 'publishv21Metaboxes']);
        }
        if (self::checkUserRole('publishv22')) { // роль publishv22
            add_action('admin_menu', [__CLASS__, 'publishv22Metaboxes']);
            add_filter('get_sample_permalink_html', [__CLASS__, 'removeEditSlag']); // редактирование слага
        }
        if (self::checkUserRole('publishv23')) { // роль publishv23
            add_action('admin_menu', [__CLASS__, 'publishv12Metaboxes']);
            add_filter('get_sample_permalink_html', [__CLASS__, 'removeEditSlag']); // редактирование слага
            add_action('add_meta_boxes', [__CLASS__, 'add_recent_thumb_meta_box']); // последние миниатюры
            add_action('edit_form_after_title', [__CLASS__, 'move_advanced_after_title']); // передвинем под титл
        }
        if (self::checkUserRole('publishv21') ||
            self::checkUserRole('publishv22') ||
            self::checkUserRole('publishv23') ||
            self::checkUserRole('wambleChecker')) { // отключаем визуальный редактор данному пользователю
            update_user_meta(wp_get_current_user()->ID, 'rich_editing', 'false');
        }
        $user = wp_get_current_user();
        if ($user->ID !== 0) {
            add_action('init', [__CLASS__, 'metaBoxInit']);
        }
    }

    /**
     * Проверка юзера на Роль
     * @param $role
     * @param null $user_id
     * @return bool
     */
    public static function checkUserRole($role, $user_id = null)
    {
        if (is_numeric($user_id)) {
            $user = get_userdata($user_id);
        } else {
            $user = wp_get_current_user();
            if ($user->ID === 0) {
                return false;
            }
        }
        return in_array($role, (array)$user->roles, true);
    }

    /**
     * Удаление метабоксов
     */
    public static function publishLinkMetaboxes()
    {
        self::removeRepeatMetabox();
        self::removeImgMedia();
        remove_meta_box('categorydiv', 'post', 'normal'); // Список категорий
        remove_meta_box('ortext-metabox', 'post', 'normal'); // formats - сайдбар
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // через экшн с добавление приоритета
    }

    /**
     * Вынес отдельно повторяющиеся боксы
     */
    public static function removeRepeatMetabox()
    {
        remove_meta_box('formatdiv', 'post', 'normal'); // formats - сайдбар
        remove_meta_box('tagsdiv-post_tag', 'post', 'normal'); // tags - сайдбар
        remove_meta_box('wpsociallikes', 'post', 'normal'); // Social Likes - под постом
        remove_menu_page('tools.php'); // страница настроек инструменты - меню
        remove_menu_page('wpcf7'); // страница с contact form 7, если есть
        remove_menu_page('edit-comments.php'); // страница комментов - меню
        remove_menu_page('edit.php?post_type=question'); // страница вопросов - меню
        remove_submenu_page('edit.php', 'post-new.php'); // создание нового поста - меню
        global $pagenow; // скрыть кнопку опубликовать
        if ('post.php' === $pagenow || 'post-new.php' === $pagenow) {
            add_action('admin_head', [__CLASS__, 'customPublishBox']);
        }
    }

    /**
     * Повторяющиеся боксы имг и медиа
     */
    public static function removeImgMedia()
    {
        remove_meta_box('postimagediv', 'post', 'normal'); // миниатюра - сайдбар
        remove_action('media_buttons', 'media_buttons');
    }

    /**
     * для чекера тошнотки
     */
    public static function wambleCheckerMetaboxes()
    {
        //self::removeImgMedia();
        remove_meta_box('formatdiv', 'post', 'normal'); // formats - сайдбар
        remove_meta_box('tagsdiv-post_tag', 'post', 'normal'); // tags - сайдбар
        remove_meta_box('wpsociallikes', 'post', 'normal'); // Social Likes - под постом
        remove_menu_page('tools.php'); // страница настроек инструменты - меню
        remove_menu_page('wpcf7'); // страница с contact form 7, если есть
        remove_menu_page('edit-comments.php'); // страница комментов - меню
        remove_menu_page('edit.php?post_type=question'); // страница вопросов - меню
        remove_submenu_page('edit.php', 'post-new.php'); // создание нового поста - меню
        remove_meta_box('categorydiv', 'post', 'normal'); // Список категорий
        remove_meta_box('ortext-metabox', 'post', 'normal'); // formats - сайдбар
        remove_meta_box('commentsdiv', 'post', 'normal'); // убирает работу с коментами на странице редактирования поста у тошнотников
        remove_meta_box('titlediv', 'post', 'normal'); // убирает титл статьи
    }

    /**
     * верстка
     */
    public static function publishv11Metaboxes()
    {
        self::removeRepeatMetabox();
        self::removeImgMedia();
    }

    /**
     * семантика
     */
    public static function publishv12Metaboxes()
    {
        self::removeRepeatMetabox();
        remove_meta_box('categorydiv', 'post', 'normal'); // categories - сайдбар
        remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // AIO SEO - под постом
    }

    /**
     * верстка
     */
    public static function publishv21Metaboxes()
    {
        self::removeRepeatMetabox();
        self::removeImgMedia();
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // AIO SEO - под постом
    }

    /**
     * семантика
     */
    public static function publishv22Metaboxes()
    {
        self::removeRepeatMetabox();
        self::removeImgMedia();
    }

    /**
     * Удаление сео полей
     */
    public static function removeAioseo()
    {
        remove_meta_box('aiosp', 'post', 'normal'); // AIO SEO
    }

    /**
     * Будет кнопка Обновить или Другая
     */
    public static function customPublishBox()
    {
        if (!is_admin()
            || self::checkUserRole('publishLinkv1')
            || self::checkUserRole('publishLinkv2')
        ) {
            return;
        }
        $style = '';
        $style .= '<style type="text/css">';
        $style .= '#publish, #original_publish';
        $style .= '{display: none; }';
        $style .= '</style>';
        echo $style;
    }

    /**
     * Удаление возможности редактирования слага
     * @return string
     */
    public static function removeEditSlag()
    {
        return '';
    }

    /**
     * метод для вывода метабокса под title (edit-post)
     */
    public static function move_advanced_after_title()
    {
        // Get the globals:
        global $post, $wp_meta_boxes;
        // Output the "advanced" meta boxes:
        do_meta_boxes(get_current_screen(), 'advanced', $post);
        // Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['advanced']);
    }

    /**
     * подключаем метабокс с выводом последних миниатюр
     */
    public static function add_recent_thumb_meta_box()
    {
        add_meta_box(
            'fseo_recent_thumbs',
            __( 'Последние миниатюры', 'textdomain' ),
            [__CLASS__, 'recent_thumbs_meta_box_callback'],
            'post',
            'advanced',
            'high'
        );
    }

    /**
     * метод для вывода миниатюр из последних 10 постов
     * @param $post
     */
    public static function recent_thumbs_meta_box_callback( $post )
    {
        $categories = [];
        $cats = get_the_terms($post->ID, 'category');
        if (!empty($cats)){
            $categories[] = $cats[0]->term_id;
            $categories[] = $cats[0]->parent;
        }
        if (!empty($categories)) {
            $posts = get_posts([
                'numberposts' => 10,
                'category__in'    => $categories,
                'orderby'     => 'modified',
                'order'       => 'DESC',
                'post_type'   => 'post',
                'post_status'      => ['draft', 'auto-draft','future','pending','publish'],
                'suppress_filters' => false,
            ]);
            if (!empty($posts)){
                echo '<div class="fseo_recent_thumbs">';
                foreach ($posts as $recent_post){
                    if (has_post_thumbnail($recent_post->ID)){
                        echo get_the_post_thumbnail($recent_post->ID, 'thumbnail', ['class' => 'alignleft']);
                    }
                }
                echo '</div>';
                echo '<style>.fseo_recent_thumbs{overflow: hidden;}.fseo_recent_thumbs img{width:80px; height: auto;margin-right: 5px;}</style>';
            }
        }
    }

    /**
     * Обговорить с Валерой названия боксов
     * и потом добавить у кого какие будут отображаться
     */
    public static function metaboxLink()
    {
        //Функция для скрытия/показа будущих боксов, когда сделает Валера
        // под постом или сайдбар
    }
}
