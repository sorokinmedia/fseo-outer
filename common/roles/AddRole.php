<?php
namespace FseoOuter\common\roles;

class AddRole
{
    /**
     * Добавление ролей, работает при активации плагина
     */
    public static function addRoleLink()
    {
        new FseoRole('publishv21','publishv21',array(
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => false, // редактирование категорий
            'upload_files'=> true, // загрузка файлов
        ));

        new FseoRole('publishv22','publishv22',array(
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => true, // редактирование категорий
            'upload_files'=> true, // загрузка файлов
        ));

        new FseoRole('publishv23','publishv23',array(
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'create_posts' => true, // создание новых записей
            'manage_categories' => true, // редактирование категорий
            'upload_files'=> true, // загрузка файлов
        ));

        new FseoRole('wambleChecker','wambleChecker',array(
            'read' => true, // просмотр записей
            'edit_posts' => true, // редактирование своих записей
            'edit_others_posts' => true, // редактирование других записей
            'edit_published_posts' => true, // редактирование опубликованных записей,
            'create_posts' => false, // создание новых записей
            'manage_categories' => false, // редактирование категорий
            'upload_files'=> true, // загрузка файлов
        ));
    }

    /**
     * Проверка юзера на Роль
     * @param $role
     * @param null $user_id
     * @return bool
     */
    public static function checkUserRole( $role, $user_id = null ) {
        if ( is_numeric( $user_id ) ) {
            $user = get_userdata($user_id);
        } else {
            $user = wp_get_current_user();
            if ($user->ID == 0) {
                return false;
            }
        }
        return in_array( $role, (array) $user->roles );
    }

    /**
     * Удаление лишнего функционала
     */
    public static function metaBoxInit()
    {
        if (self::checkUserRole('wambleChecker')){
            add_action('admin_menu', [__CLASS__, 'wambleCheckerMetaboxes']);
        }
        if( self::checkUserRole('publishv21') ){ // роль publishv21
            add_action('admin_menu',[__CLASS__, 'publishv21Metaboxes']);
        }
        if( self::checkUserRole('publishv22') ){ // роль publishv22
            add_action('admin_menu',[__CLASS__, 'publishv22Metaboxes']);
            add_filter( 'get_sample_permalink_html', [__CLASS__, 'removeEditSlag'] ); // редактирование слага
        }
        if( self::checkUserRole('publishv23') ){ // роль publishv23
            add_action('admin_menu',[__CLASS__, 'publishv12Metaboxes']);
            add_filter( 'get_sample_permalink_html', [__CLASS__, 'removeEditSlag'] ); // редактирование слага
        }
        if( self::checkUserRole('publishv21') ||
            self::checkUserRole('publishv22') ||
            self::checkUserRole('publishv23') ||
            self::checkUserRole('wambleChecker')) { // отключаем визуальный редактор данному пользователю
                update_user_meta( wp_get_current_user()->ID, 'rich_editing', 'false' );
        }
        $user = wp_get_current_user();
        if ($user->ID != 0) {
            add_action('init', [__CLASS__,'metaBoxInit']);
        }
    }

    /**
     * Удаление метабоксов
     */
    public static function publishLinkMetaboxes()
    {
        self::removeRepeatMetabox();
        self::removeImgMedia();
        remove_meta_box('categorydiv', 'post', 'normal'); // Список категорий
        remove_meta_box('ortext-metabox','post','normal' ); // formats - сайдбар
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // через экшн с добавление приоритета
    }

    public static function wambleCheckerMetaboxes()
    {
        //self::removeImgMedia();
        remove_meta_box( 'formatdiv','post','normal' ); // formats - сайдбар
        remove_meta_box( 'tagsdiv-post_tag','post','normal' ); // tags - сайдбар
        remove_meta_box('wpsociallikes', 'post', 'normal'); // Social Likes - под постом
        remove_menu_page( 'tools.php' ); // страница настроек инструменты - меню
        remove_menu_page( 'wpcf7' ); // страница с contact form 7, если есть
        remove_menu_page( 'edit-comments.php' ); // страница комментов - меню
        remove_menu_page( 'edit.php?post_type=question' ); // страница вопросов - меню
        remove_submenu_page( 'edit.php', 'post-new.php' ); // создание нового поста - меню
        remove_meta_box('categorydiv', 'post', 'normal'); // Список категорий
        remove_meta_box('ortext-metabox','post','normal' ); // formats - сайдбар
        remove_meta_box('commentsdiv', 'post', 'normal'); // убирает работу с коментами на странице редактирования поста у тошнотников
        remove_meta_box('titlediv', 'post', 'normal'); // убирает титл статьи
    }

    public static function publishv11Metaboxes(){
        self::removeRepeatMetabox();
        self::removeImgMedia();
    }

    public static function publishv12Metaboxes(){
        self::removeRepeatMetabox();
        remove_meta_box( 'categorydiv','post','normal' ); // categories - сайдбар
        remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // AIO SEO - под постом
    }

    public static function publishv21Metaboxes(){
        self::removeRepeatMetabox();
        self::removeImgMedia();
        add_action('add_meta_boxes', [__CLASS__, 'removeAioseo'], 100000); // AIO SEO - под постом
    }

    public static function publishv22Metaboxes(){
        self::removeRepeatMetabox();
        self::removeImgMedia();
    }

    /**
     * Вынес отдельно повторяющиеся боксы
     */
    public static function removeRepeatMetabox()
    {
        remove_meta_box( 'formatdiv','post','normal' ); // formats - сайдбар
        remove_meta_box( 'tagsdiv-post_tag','post','normal' ); // tags - сайдбар
        remove_meta_box('wpsociallikes', 'post', 'normal'); // Social Likes - под постом
        remove_menu_page( 'tools.php' ); // страница настроек инструменты - меню
        remove_menu_page( 'wpcf7' ); // страница с contact form 7, если есть
        remove_menu_page( 'edit-comments.php' ); // страница комментов - меню
        remove_menu_page( 'edit.php?post_type=question' ); // страница вопросов - меню
        remove_submenu_page( 'edit.php', 'post-new.php' ); // создание нового поста - меню
        global $pagenow; // скрыть кнопку опубликовать
        if ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) {
            add_action('admin_head', [__CLASS__, 'customPublishBox']);
        }
    }

    /**
     * Повторяющиеся боксы имг и медиа
     */
    public static function removeImgMedia()
    {
        remove_meta_box( 'postimagediv','post','normal' ); // миниатюра - сайдбар
        remove_action( 'media_buttons', 'media_buttons' );
    }

    /**
     * Удаление сео полей
     */
    public static function removeAioseo()
    {
        remove_meta_box( 'aiosp','post','normal' ); // AIO SEO
    }

    /**
     * Будет кнопка Обновить или Другая
     */
    public static function customPublishBox() {
        if( !is_admin() ||
            self::checkUserRole('publishLinkv1') ||
            self::checkUserRole('publishLinkv2') )
            return;
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
     * Обговорить с Валерой названия боксов
     * и потом добавить у кого какие будут отображаться
     */
    public static function metaboxLink()
    {
        //Функция для скрытия/показа будущих боксов, когда сделает Валера
        // под постом или сайдбар
    }
}