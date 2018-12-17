<?php
namespace FseoOuter\common\view;

use FseoOuter\common\setting\AddUser;
use FseoOuter\common\setting\AddPlugin;
/**
 * Class UsersPage
 * @package FseoOuter\common\view
 */
class UsersPage
{
    public static function settingPage()
    { ?>
        <div class="wrap">
            <h2>Добавление фабричных пользователей</h2>
            <form method="post">
                <input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=fseo-outer-users" />
                <?php
                if ($_POST['_wp_http_referer'] == '/wp-admin/admin.php?page=fseo-outer-users' && $_POST['submit'] == 'Добавить пользователей fabrica(5) и пароли'){
                    if (!AddUser::checkUserExist('fabrica')){
                        $fabrica = wp_create_user('fabrica', wp_generate_password(16), 'fabrica.user@gmail.com');
                        echo '<div class="updated notice"><p>Пользователь fabrica добавлен</p></div>';
                        $fabrica_user = new \WP_User($fabrica);
                        $fabrica_user->set_role( 'administrator' );
                        $fabrica_password = AddUser::createNewApplicationPassword($fabrica, 'fabrica');
                        echo '<div class="updated notice"><p>Пользователю fabrica добавлен Application Password ' . $fabrica_password . '</p></div>';
                    }
                    if (!AddUser::checkUserExist('fabricav21')) {
                        $fabricav21 = wp_create_user('fabricav21', wp_generate_password(16), 'fabrica.userv21@gmail.com');
                        echo '<div class="updated notice"><p>Пользователь fabricaV21 добавлен</p></div>';
                        $fabricav21_user = new \WP_User($fabricav21);
                        $fabricav21_user->set_role('publishv21');
                        $fabricav21_password = AddUser::createNewApplicationPassword($fabricav21, 'fabrica21');
                        echo '<div class="updated notice"><p>Пользователю fabricaV21 добавлен Application Password ' . $fabricav21_password . '</p></div>';
                    }
                    if (!AddUser::checkUserExist('fabricav22')) {
                        $fabricav22 = wp_create_user('fabricav22', wp_generate_password(16), 'fabrica.userv22@gmail.com');
                        echo '<div class="updated notice"><p>Пользователь fabricaV22 добавлен</p></div>';
                        $fabricav22_user = new \WP_User($fabricav22);
                        $fabricav22_user->set_role('publishv22');
                        $fabricav22_password = AddUser::createNewApplicationPassword($fabricav22, 'fabrica22');
                        echo '<div class="updated notice"><p>Пользователю fabricaV22 добавлен Application Password ' . $fabricav22_password . '</p></div>';
                    }
                    if (!AddUser::checkUserExist('fabricav23')) {
                        $fabricav23 = wp_create_user('fabricav23', wp_generate_password(16), 'fabrica.userv23@gmail.com');
                        echo '<div class="updated notice"><p>Пользователь fabricaV23 добавлен</p></div>';
                        $fabricav23_user = new \WP_User($fabricav23);
                        $fabricav23_user->set_role('publishv23');
                        $fabricav23_password = AddUser::createNewApplicationPassword($fabricav23, 'fabrica23');
                        echo '<div class="updated notice"><p>Пользователю fabricaV23 добавлен Application Password ' . $fabricav23_password . '</p></div>';
                    }
                    if (!AddUser::checkUserExist('fabrica_wamble')) {
                        $fabrica_wamble = wp_create_user('fabrica_wamble', wp_generate_password(16), 'fabrica_wamble.userv@gmail.com');
                        echo '<div class="updated notice"><p>Пользователь fabrica_wamble добавлен</p></div>';
                        $fabrica_wamble_user = new \WP_User($fabrica_wamble);
                        $fabrica_wamble_user->set_role('wambleChecker');
                        $fabrica_wamble_password = AddUser::createNewApplicationPassword($fabrica_wamble, 'fabrica_wamble');
                        echo '<div class="updated notice"><p>Пользователю fabrica_wamble добавлен Application Password ' . $fabrica_wamble_password . '</p></div>';
                    }
                }
                ?>
                <p class="submit">
                    <?php if (is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_API) && is_plugin_active(AddPlugin::PLUGIN_DIR_APPLICATION_PASSWORDS) && is_plugin_active(AddPlugin::PLUGIN_DIR_WP_REST_META_ENDPOINTS)){ ?>
                        <?php if (AddUser::checkUserExist('fabrica')){
                            echo '<div class="updated notice"><p>Пользователь <strong>fabrica</strong> зарегистрирован</p></div>';
                        }?>
                        <?php if (AddUser::checkUserExist('fabricav21')){
                            echo '<div class="updated notice"><p>Пользователь <strong>fabricav21</strong> зарегистрирован</p></div>';
                        }?>
                        <?php if (AddUser::checkUserExist('fabricav22')){
                            echo '<div class="updated notice"><p>Пользователь <strong>fabricav22</strong> зарегистрирован</p></div>';
                        }?>
                        <?php if (AddUser::checkUserExist('fabricav23')){
                            echo '<div class="updated notice"><p>Пользователь <strong>fabricav23</strong> зарегистрирован</p></div>';
                        }?>
                    <?php if (AddUser::checkUserExist('fabrica_wamble')){
                        echo '<div class="updated notice"><p>Пользователь <strong>fabrica_wamble</strong> зарегистрирован</p></div>';
                    }?>
                        <?php if (!AddUser::checkUserExist('fabrica') || !AddUser::checkUserExist('fabricav21') || !AddUser::checkUserExist('fabricav22') || !AddUser::checkUserExist('fabricav23') || !AddUser::checkUserExist('fabrica_wamble')){ ?>
                            <h3>Добавить пользователей для фабрики, проставить им роли и пароли</h3>
                            <input type="submit" class="button-primary" value="Добавить пользователей fabrica(5) и пароли" name="submit" />
                        <?php } ?>
                    <?php } else { ?>
                        <h3>Необходимо установить плагины</h3>
                    <?php } ?>
                </p>
            </form>
        </div>
        <?php
    }
}