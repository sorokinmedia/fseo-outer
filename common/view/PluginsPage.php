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
                    if (!is_plugin_active(AddPlugin::PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK)) {
                        AddPlugin::installPlugin(AddPlugin::PLUGIN_NAME_POST_EDITOR_BUTTONS_FORK);
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
                    if (is_plugin_inactive(AddPlugin::PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK)) {
                        AddPlugin::activatePlugin(AddPlugin::PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK);
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
                    <?php if (is_plugin_active(AddPlugin::PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK)){
                        echo '<div class="updated notice"><p>Плагин <strong>' . AddPlugin::PLUGIN_NAME_POST_EDITOR_BUTTONS_FORK . '</strong> установлен и активирован</p></div>';
                    }?>

                    <?php if (!is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_API) || !is_plugin_active(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS) || !is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS) || !is_plugin_active(AddPlugin::PLUGIN_DIR_POST_EDITOR_BUTTONS_FORK)){ ?>
                <h3>Установить плагины WP REST API, Application Passwords, WP REST Meta Endpoints</h3>
            <input type="submit" class="button-primary" value="Добавить плагины" name="submit" />
            <?php } ?>
                </p>
            </form>
        </div>
        <div class="wrap">
            <h2>Добавить/обновить кнопки в редакторе</h2>
            <form method="post">
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=fseo-outer-plugins" />
                <?php
                if ($_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=fseo-outer-plugins' && $_POST['submit'] == 'Добавить') {
                    global $wpdb;
                    $peb_caption = json_encode('a:25:{i:0;s:2:"h2";i:1;s:2:"h3";i:2;s:2:"h4";i:3;s:6:"advice";i:4;s:4:"stop";i:5;s:7:"warning";i:6;s:7:"colored";i:7;s:7:"br-nbsp";i:8;s:5:"zakon";i:9;s:3:"big";i:10;s:10:"name_zakon";i:11;s:10:"video-size";i:12;s:15:"block_questions";i:13;s:8:"nofollow";i:14;s:2:"d1";i:15;s:2:"d2";i:16;s:2:"d3";i:17;s:2:"d4";i:18;s:4:"more";i:19;s:8:"kovichki";i:20;s:2:"18";i:21;s:5:"video";i:22;s:8:"round100";i:23;s:8:"round150";i:24;s:8:"round200";}');
                    $peb_before = json_encode('a:25:{i:0;s:4:"<h2>";i:1;s:4:"<h3>";i:2;s:4:"<h4>";i:3;s:20:"<div class="advice">";i:4;s:18:"<div class="stop">";i:5;s:21:"<div class="warning">";i:6;s:21:"<div class="colored">";i:7;s:0:"";i:8;s:33:"<div class="zakon"><!--noindex-->";i:9;s:31:"<span style="font-size:larger">";i:10;s:26:"<p align="center"><strong>";i:11;s:24:"height="420" width="700"";i:12;s:29:"<div class="block_questions">";i:13;s:14:"rel="nofollow"";i:14;s:8:"[direct]";i:15;s:9:"[direct2]";i:16;s:9:"[direct3]";i:17;s:9:"[direct4]";i:18;s:12:"<!--more -->";i:19;s:7:"&laquo;";i:20;s:9:"hide_cock";i:21;s:19:"[embed width="700"]";i:22;s:8:"round100";i:23;s:8:"round150";i:24;s:8:"round200";}');
                    $peb_after = json_encode('a:25:{i:0;s:5:"</h2>";i:1;s:5:"</h3>";i:2;s:5:"</h4>";i:3;s:6:"</div>";i:4;s:6:"</div>";i:5;s:6:"</div>";i:6;s:6:"</div>";i:7;s:12:"<br />&nbsp;";i:8;s:21:"<!--/noindex--></div>";i:9;s:7:"</span>";i:10;s:13:"</strong></p>";i:11;s:0:"";i:12;s:6:"</div>";i:13;s:0:"";i:14;s:0:"";i:15;s:0:"";i:16;s:0:"";i:17;s:0:"";i:18;s:0:"";i:19;s:7:"&raquo;";i:20;s:0:"";i:21;s:8:"[/embed]";i:22;s:0:"";i:23;s:0:"";i:24;s:0:"";}');
                    $_db_options = 'options';
                    $wpdb->show_errors();
                    $table_name = $wpdb->prefix . $_db_options; // имя таблицы
                    $check = $wpdb->query(
                        $wpdb->prepare(
                            "SELECT option_id FROM $table_name WHERE option_name = 'peb_caption'"
                        )
                    );
                    if ($check !== 0) {
                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE $table_name SET option_value = " . $peb_caption . " WHERE option_name LIKE 'peb_caption'"
                            )
                        );
                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE $table_name SET option_value = " . $peb_before . " WHERE option_name LIKE 'peb_before'"
                            )
                        );
                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE $table_name SET option_value = " . $peb_after . " WHERE option_name LIKE 'peb_after'"
                            )
                        );
                    } else {
                        $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO $table_name (option_name, option_value, autoload) VALUES ('peb_caption', $peb_caption, 'yes');"
                            )
                        );
                        $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO $table_name (option_name, option_value, autoload) VALUES ('peb_before', $peb_before, 'yes');"
                            )
                        );
                        $wpdb->query(
                            $wpdb->prepare(
                                "INSERT INTO $table_name (option_name, option_value, autoload) VALUES ('peb_after', $peb_after, 'yes');"
                            )
                        );
                    }
                }
                ?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="Добавить" name="submit" />
                </p>


            </form>
        </div>
        <?php
    }
}