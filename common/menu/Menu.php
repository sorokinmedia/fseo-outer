<?php
namespace FseoOuter\common\menu;

use FseoOuter\common\view\PluginsPage;
use FseoOuter\common\view\SettingPage;
use FseoOuter\common\view\UsersPage;

/**
 * Class Menu
 * @package FseoPlugins\plugins\common\menu
 */
class Menu
{

    public static function menu() {
        // верхний уровень
        add_menu_page('Fseo-outer', 'Fseo-outer', 'manage_option', 'fseo-outer', 'sb_admin_fseo-outer');
        // подуровни
        // Основные настройки
        add_submenu_page( 'fseo-outer',
            'Settings',
            'Settings',
            'manage_options',
            'fseo-outer-settings',
            function() {
                self::settingMenu();
            }
        );
        // Работа с плагинами
        add_submenu_page( 'fseo-outer',
            'Settings plugins',
            'Settings plugins',
            'manage_options',
            'fseo-outer-plugins',
            function() {
                self::pluginsMenu();
            }
        );
        // Работа с пользователями
        add_submenu_page( 'fseo-outer',
            'Settings users',
            'Settings users',
            'manage_options',
            'fseo-outer-users',
            function() {
                self::usersMenu();
            }
        );
        register_setting('fseo-outer-settings-group', 'fseo_outer_social');
        register_setting('fseo-outer-settings-group', 'fseo_outer_contents');
    }

    /**
     * Вывод настроек
     */
    public static function settingMenu()
    {
        SettingPage::settingPage();
    }

    /**
     * Вывод настроек
     */
    public static function pluginsMenu()
    {
        PluginsPage::settingPage();
    }

    /**
     * Вывод настроек
     */
    public static function usersMenu()
    {
        UsersPage::settingPage();
    }
}