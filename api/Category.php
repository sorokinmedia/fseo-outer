<?php
namespace FseoOuter\api;

use FseoOuter\api\models\ApiAnswer;
use FseoOuter\api\models\RestMessage;

/**
 * Class Category
 * @package FseoPlugins\plugins\api
 *
 * работа с категориями
 */
class Category
{
    /**
     * Category constructor.
     */
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $category = 'category/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $category, array(
            'methods' => 'GET',
            'callback' => [$this, 'getCategory'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
        ));
    }

    /**
     * Получает категорию
     * урл вида сайт/wp-json/wp/v2/category/1 - где цифра ID рубрики
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getCategory(\WP_REST_Request $request)
    {
        $cat_id = $request->get_param('id');
        $category = get_category($cat_id);
        $top_description = stripcslashes(get_term_meta($cat_id, 'cat_top_description', true));
        $category->text = $top_description;
        $category->status = 'draft';
        if (trim($category->text) != ''){
            $category->status = 'publish';
        }
        $category->link = get_category_link($cat_id);
        // TODO: нужна нормальная REST модель для формирования нужного ответа
        return new ApiAnswer([
            'response' => [
                'id' => $category->term_id,
                'name' => $category->name,
                'slug' => $category->slug,
                'link' => $category->link,
                'text' => $category->text,
                'status' => $category->status,
                'parent' => $category->parent,
                'count' => $category->count,
            ],
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' =>'Данные получены',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }
}

/**
 * добавляем endpoint к API
 */
add_action('rest_api_init', function() {
    $category = new Category();
});