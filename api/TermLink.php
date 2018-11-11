<?php
namespace FseoOuter\api;

use FseoOuter\api\models\ApiAnswer;
use FseoOuter\api\models\RestMessage;

/**
 * Class TermLink
 * @package FseoOuter\api
 */
class TermLink
{
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $post_cat = 'get_term_text/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $post_cat, [
            'methods' => 'GET',
            'callback' => [$this, 'getTermText'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
        ]);
        $update = 'update_cat_meta';
        register_rest_route($namespace, '/' . $update,[
            'methods' => 'POST',
            'callback' => [$this, 'updateTermText'],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
    }

    /**
     * Получает текст категории (для линковки)
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getTermText(\WP_REST_Request $request)
    {
        $cat_id = $request->get_param('id');
        $text = get_term_meta($cat_id, 'cat_top_description');
        return new ApiAnswer([
            'response' => $text,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' =>'Текст категории получен',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }

    /**
     * Обновлет текст в категории (для линковки)
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function updateTermText(\WP_REST_Request $request)
    {
        $params = $request->get_body_params();
        update_term_meta( $params['id'], 'cat_top_description', $params['text']);
        return new ApiAnswer([
            'response' => null,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' =>'Текст обновлен',
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
    $link = new TermLink();
});