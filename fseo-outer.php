<?php
/*
Plugin Name: F-seo Outer
Version: 1.6
Plugin URI: http://f-seo.ru
Author: F-Seo
Author URI: http://f-seo.ru
Description: Общий плагин внешний
*/
if (!defined('WPINC')) {
    die;
}

const FSEO_OUTER_VER = '1.6';

// Include the autoloader so we can dynamically include the rest of the classes.
require_once(trailingslashit(dirname(__FILE__)) . 'inc/autoloader.php');
// instantiate the loader
$loader = new \Example\Psr4AutoloaderClassOuter();
$loader->register();
$loader->addNamespace('FseoOuter', dirname(__FILE__));

use FseoOuter\common\menu\Menu;
use FseoOuter\common\SupportingFunction;
use FseoOuter\common\AutoLogin;
use FseoOuter\common\contents\ContentsPost;
use FseoOuter\common\roles\AddRole;

register_activation_hook(__FILE__, ['ActivatorFseo', 'install']);
register_uninstall_hook(__FILE__, ['RemoveFseo', 'uninstall']);
add_action('admin_menu', ['adminMenuOuter', 'addMenu']);
add_action('save_post', [SupportingFunction::class, 'parseArticleText'], 10, 3);
add_action('admin_enqueue_scripts', ['AddScriptOuter', 'script']);
add_action('admin_init', ['AddStyleAdmin', 'style']);
add_action('wp_enqueue_scripts', ['SupportingScriptOuter', 'script']);
add_action('wp_enqueue_scripts', ['AddStyleOuter', 'style']);
add_action('after_setup_theme', [AutoLogin::class, 'autoLogin']);
add_action('wp_enqueue_editor', [SupportingFunction::class, 'tnlAddNofollow']);

include_once 'api/Post.php';
include_once 'api/Term.php';
include_once 'api/User.php';
include_once 'api/Category.php';
include_once 'api/Link.php';
include_once 'api/TermLink.php';
include_once 'api/Wamble.php';
include_once 'api/PluginUpdate.php';
include_once 'common/supporting/SupportingFunctions.php';
include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');

/**
 * Class ActivatorFseo
 * действия при активации плагина
 */
class ActivatorFseo
{
    public static function install()
    {
        AddRole::addRoleLink();
    }
}

/**
 * Class RemoveFseo
 * действия при удалении плагина
 */
class RemoveFseo
{
    public static function uninstall()
    {

    }
}

/**
 * Class adminMenu
 */
class adminMenuOuter
{
    public static function addMenu()
    {
        Menu::menu();
    }
}

/**
 * Class AddScriptOuter
 */
class AddScriptOuter
{
    public static function script()
    {
        $main_script_url = plugins_url('/common/js/script.js', __FILE__);
        wp_enqueue_script('custom-script-outer', $main_script_url, array('jquery'), FSEO_OUTER_VER, true);
    }
}

/**
 * Подключаем вспомогательные скрипты
 * Class SupportingScript
 */
class SupportingScriptOuter
{
    public static function script()
    {
        wp_register_script('ya_static_share', '//yastatic.net/share2/share.js', [], FSEO_OUTER_VER);
        wp_enqueue_script('ya_static_share');
        wp_register_script('common_scripts', plugins_url('/common/js/script-front.js', __FILE__), [], FSEO_OUTER_VER, true);
        wp_enqueue_script('common_scripts');
    }
}

/**
 * Необходимые стили для админки
 * Class AddStyleAdmin
 */
class AddStyleAdmin
{
    public static function style()
    {
        $style_admin = plugins_url('/common/css/admin-style.css', __FILE__);
        wp_enqueue_style('style-admin-outer', $style_admin, [], FSEO_OUTER_VER);
    }
}

/**
 * Необходимые стили
 * Class AddStyleOuter
 */
class AddStyleOuter
{
    public static function style()
    {
        $style_url = plugins_url('/common/css/style.css', __FILE__);
        wp_enqueue_style('custom-style-outer', $style_url, [], FSEO_OUTER_VER);
    }
}

/**
 * Инициализация обработчиков
 */
function initFilterOuter()
{
    add_filter('is_protected_meta', function ($protected, $meta_key) {
        if ('_aioseop_keywords' == $meta_key || '_aioseop_keywords' == $meta_key && defined('REST_REQUEST') && REST_REQUEST) {
            $protected = false;
        }
        return $protected;
    }, 10, 2);
    add_filter('the_content', [SupportingFunction::class, 'socButtonMoreCat'], 10);
    add_filter('the_content', [ContentsPost::class, 'fseoContentsShortcode']);
    add_filter('jpeg_quality', [SupportingFunction::class, 'imgQuality']);
}

initFilterOuter();

// Проверка на роли работает через хук add_action
add_action('plugins_loaded', 'initRoleMetabox');
function initRoleMetabox()
{
    AddRole::metaBoxInit();
}
