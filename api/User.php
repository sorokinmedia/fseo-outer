<?php

use FseoOuter\common\setting\AddUser;
use FseoOuter\api\models\RestMessage;
use FseoOuter\api\models\ApiAnswer;

/**
 * Класс для работы с пользователями
 * Class User
 */
class User
{
    /**
     * all_post constructor.
     */
    public function __construct()
    {
        // указываем роутинг для API
        $version = '2';
        $namespace = 'wp/v' . $version;
        $reset_password = 'reset_password';
        register_rest_route($namespace, '/' . $reset_password, [
            'methods' => 'POST',
            'callback' => [$this, 'resetPassword'],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
    }

    /**
     * Сбрасываем пароли на фабричных пользователей
     * @param WP_REST_Request $request
     * @return ApiAnswer
     */
    public function resetPassword(WP_REST_Request $request)
    {
        $data = [];
        if ($user = AddUser::checkUserExist('fabrica')){
            $fabrica = $user->ID;
            $fabrica_password = AddUser::createNewApplicationPassword($fabrica, 'fabrica');
            array_push($data, ['fabrica' => base64_encode($fabrica_password)]);
        }
        if ($user = AddUser::checkUserExist('fabricav21')){
            $fabrica21 = $user->ID;
            $fabrica21_password = AddUser::createNewApplicationPassword($fabrica21, 'fabrica21');
            array_push($data, ['fabrica21' => base64_encode($fabrica21_password)]);
        }
        if ($user = AddUser::checkUserExist('fabricav22')){
            $fabrica22 = $user->ID;
            $fabrica22_password = AddUser::createNewApplicationPassword($fabrica22, 'fabrica22');
            array_push($data, ['fabrica22' => base64_encode($fabrica22_password)]);
        }
        if ($user = AddUser::checkUserExist('fabricav23')){
            $fabrica23 = $user->ID;
            $fabrica23_password = AddUser::createNewApplicationPassword($fabrica23, 'fabrica23');
            array_push($data, ['fabrica23' => base64_encode($fabrica23_password)]);
        }
        return new ApiAnswer([
            'response' =>  $data,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' =>'Получено',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }
}

/**
 * add custom function to rest_api_init action
 */
add_action('rest_api_init', function () {
    $users = new User();
});