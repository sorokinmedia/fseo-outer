<?php

namespace FseoOuter\common\setting;

/**
 * Class AddPlugin
 * @package FseoOuter\common\setting
 */
class AddPlugin
{
    // названия плагинов
    const PLUGIN_NAME_WP_REST_API = 'rest-api';
    const PLUGIN_NAME_APPLICATION_PASSWORDS = 'application-passwords';
    const PLUGIN_NAME_WP_REST_META_ENDPOINTS = 'rest-api-meta-endpoints';
    const PLUGIN_NAME_POST_EDITOR_BUTTONS_FORK = 'post-editor-buttons-fork';

    // пути до основных файлов плагинов
    const PLUGIN_DIR_WP_REST_API = 'rest-api/plugin.php';
    const PLUGIN_DIR_APPLICATION_PASSWORDS = 'application-passwords/application-passwords.php';
    const PLUGIN_DIR_WP_REST_META_ENDPOINTS = 'rest-api-meta-endpoints/plugin.php';
    const PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK = 'post-editor-buttons-fork/post-editor-buttons.php';

    /**
     * инсталл плагина
     */
    public static function installPlugin($plugin)
    {
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php'; //for plugins_api..
        $api = plugins_api('plugin_information', [
            'slug' => $plugin,
            'fields' => [
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ],
        ]);
        //includes necessary for Plugin_Upgrader and Plugin_Installer_Skin
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/misc.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        $upgrader = new \Plugin_Upgrader(new QuietSkin());
        $upgrader->install($api->download_link);
    }

    /**
     * активация плагина
     * @param $plugin
     */
    public static function activatePlugin($plugin)
    {
        if (!function_exists('activate_plugin')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin = ABSPATH . 'wp-content/plugins/' . $plugin;
        if (!is_plugin_active($plugin)) {
            activate_plugin($plugin);
        }
    }
}
