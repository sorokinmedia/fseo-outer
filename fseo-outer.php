<?php
/*
Plugin Name: F-seo Outer
Version: 1.0
Plugin URI: http://f-seo.ru
Author: F-Seo
Author URI: http://f-seo.ru
Description: Общий плагин внешний
*/
if ( ! defined( 'WPINC' ) ) {
    die;
}

const FSEO_OUTER_VER = '1.0';
// Include the autoloader so we can dynamically include the rest of the classes.
require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );
// instantiate the loader
$loader = new \Example\Psr4AutoloaderClass();
$loader->register();
$loader->addNamespace('FseoOuter', dirname( __FILE__ ));

use FseoOuter\common\menu\Menu;
use FseoOuter\common\SupportingFunction;

register_activation_hook(__FILE__, array('Activator', 'install'));
register_uninstall_hook(__FILE__, array('Remove', 'uninstall'));
add_action('admin_menu', array('adminMenu', 'addMenu'));
add_action( 'save_post', [SupportingFunction::class, 'parseArticleText'], 10, 3 );

include_once 'api/Post.php';
include_once 'api/Term.php';
include_once 'api/User.php';
include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

/**
 * Class Activator
 * действия при активации плагина
 */
class Activator
{
    public static function install()
    {

    }
}

/**
 * Class Remove
 * действия при удалении плагина
 */
class Remove
{
    public static function uninstall()
    {

    }
}

/**
 * Class adminMenu
 */
class adminMenu
{
    public static function addMenu()
    {
        Menu::menu();
    }
}

class AddScript
{
    public static function script()
    {
        $main_script_url = plugins_url('/common/js/script.js', __FILE__);
        wp_enqueue_script('custom-script', $main_script_url, array( 'jquery' ), FSEO_OUTER_VER, true);
    }
}