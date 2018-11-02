<?php
namespace FseoOuter\common\view;

use FseoOuter\common\setting\AddPlugin;
/**
 * Class SettingPage
 * @package FseoOuter\common\view
 */
class PluginsPage
{
    public static function settingPage()
    { ?>
        <div class="wrap">
            <h2>Настройка сайта для работы по API установка плагинов</h2>
            <form method="post">
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=fseo-outer-plugins" />
                <?php
                if ($_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=fseo-outer-plugins' && $_POST['submit'] == 'Добавить плагины') {
                    if (!is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_API)) {
                        AddPlugin::installPlugin(AddPlugin::PLUGIN_NAME_WP_REST_API);
                    }
                    if (!is_plugin_active(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS)) {
                        AddPlugin::installPlugin(AddPlugin::PLUGIN_NAME_APPLICATION_PASSWORDS);
                    }
                    if (!is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS)) {
                        AddPlugin::installPlugin(AddPlugin::PLUGIN_NAME_WP_REST_META_ENDPOINTS);
                    }
                    if (is_plugin_inactive(AddPlugin::PLUGIN_DIR_WP_REST_API)){
                        AddPlugin::activatePlugin(AddPlugin::PLUGIN_DIR_WP_REST_API);
                    }
                    if (is_plugin_inactive(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS)) {
                        AddPlugin::activatePlugin(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS);
                    }
                    if (is_plugin_inactive(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS)) {
                        AddPlugin::activatePlugin(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS);
                    }
                    echo '<div class="updated notice"><p>Плагины установлены и активированы</p></div>';
                } 
                ?>
                <p class="submit">
                    <?php if (is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_API)){
                        echo '<div class="updated notice"><p>Плагин <strong>' . AddPlugin::PLUGIN_NAME_WP_REST_API . '</strong> установлен и активирован</p></div>';
                    }?>
                    <?php if (is_plugin_active(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS)){
                        echo '<div class="updated notice"><p>Плагин <strong>' . AddPlugin::PLUGIN_NAME_APPLICATION_PASSWORDS . '</strong> установлен и активирован</p></div>';
                    }?>
                    <?php if (is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS)){
                        echo '<div class="updated notice"><p>Плагин <strong>' . AddPlugin::PLUGIN_NAME_WP_REST_META_ENDPOINTS . '</strong> установлен и активирован</p></div>';
                    }?>

                    <?php if (!is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_API) || !is_plugin_active(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS) || !is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS)){ ?>
                <h3>Установить плагины WP REST API, Application Passwords, WP REST Meta Endpoints</h3>
            <input type="submit" class="button-primary" value="Добавить плагины" name="submit" />
            <?php } ?>
                </p>
            </form>
        </div>
        <?php
    }
}