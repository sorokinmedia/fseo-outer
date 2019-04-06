<?php

namespace FseoOuter\common;

use FseoOuter\common\setting\AddUser;

/**
 * Class AutoLogin
 * @package FseoOuter\common
 */
class AutoLogin
{
    /**
     * функция автологина для фабричных пользователей
     */
    public static function autoLogin()
    {
        if (isset($_GET['user'])) {
            $username = base64_decode($_GET['user']);
            $array = explode(':', $username);
            $link = base64_decode($_GET['link']);
            $user = get_user_by('login', $array[0]);
            $pass = get_user_meta($user->ID, AddUser::USERMETA_KEY_APPLICATION_PASSWORDS, true);
            $erp_users = ['fabrica', 'fabricav21', 'fabricav22', 'fabricav23', 'fabrica_wamble']; // пользователи из erp
            $user_old = wp_get_current_user();
            if (in_array($array[0], $erp_users)) {
                if (wp_check_password($array[1], $pass[0]['password'])) {
                    if (!is_wp_error($user)) {
                        if ($user_old->user_login !== $username) {
                            wp_clear_auth_cookie();
                            wp_set_current_user($user->ID);
                            wp_set_auth_cookie($user->ID);
                        }
                        wp_safe_redirect($link);
                        exit();
                    }
                }
            }
        }
    }
}
