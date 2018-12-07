<?php
namespace FseoOuter\api;

use FseoOuter\common\setting\AddPlugin;
use FseoOuter\api\models\RestMessage;
use FseoOuter\api\models\ApiAnswer;

/**
 * Класс для работы с пользователями
 * Class User
 */
class PluginUpdate
{
    /**
     * all_post constructor.
     */
    public function __construct()
    {
        // указываем роутинг для API
        $version = '2';
        $namespace = 'wp/v' . $version;
        $reset_password = 'install_activate';
        register_rest_route($namespace, '/' . $reset_password, [
            'methods' => 'POST',
            'callback' => [$this, 'pluginUpdate'],
            /*'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },*/
        ]);
    }

    /**
     * Сбрасываем пароли на фабричных пользователей
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function pluginUpdate(\WP_REST_Request $request)
    {
        $params = $request->get_body_params();
        if (!is_plugin_active($params['path'])) {
            AddPlugin::installPlugin($params['name']);
        }
        if (is_plugin_inactive($params['path'])) {
            AddPlugin::activatePlugin($params['path']);
        }
        return new ApiAnswer([
            'response' =>  null,
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
    $plugin = new PluginUpdate();
});