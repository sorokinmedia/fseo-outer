<?php

namespace FseoOuter\common\setting;

/**
 * Class AddUser
 * @package FseoOuter\common\setting
 */
class AddUser
{
    /**
     * The user meta application password key.
     * @type string
     */
    const USERMETA_KEY_APPLICATION_PASSWORDS = '_application_passwords';

    /**
     * проверяет есть ли пользователь с данным логином
     * @param $username
     * @return false|WP_User
     */
    public static function checkUserExist($username)
    {
        return get_user_by('login', $username);
    }

    /**
     * @param $user_id
     * @param $name
     * @return array
     */
    public static function createNewApplicationPassword($user_id, $name)
    {
        $new_password = wp_generate_password(16, false);
        $hashed_password = wp_hash_password($new_password);

        $new_item = [
            'name' => $name,
            'password' => $hashed_password,
            'created' => time(),
            'last_used' => null,
            'last_ip' => null,
        ];
        $passwords = [];
        $passwords[] = $new_item;
        self::setUserApplicationPasswords($user_id, $passwords);
        return $new_password;
    }

    /**
     * Set a users application passwords.
     *
     * @since 0.1-dev
     *
     * @access public
     * @static
     *
     * @param int $user_id User ID.
     * @param array $passwords Application passwords.
     *
     * @return bool
     */
    public static function setUserApplicationPasswords($user_id, $passwords)
    {
        return update_user_meta($user_id, self::USERMETA_KEY_APPLICATION_PASSWORDS, $passwords);
    }

    /**
     * Get a users application passwords.
     *
     * @since 0.1-dev
     *
     * @access public
     * @static
     *
     * @param int $user_id User ID.
     * @return array
     */
    public static function getUserApplicationPasswords($user_id)
    {
        return get_user_meta($user_id, self::USERMETA_KEY_APPLICATION_PASSWORDS, true);
    }
}

