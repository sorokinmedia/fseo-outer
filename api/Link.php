<?php

namespace FseoOuter\api;

use FseoOuter\api\models\ApiAnswer;
use FseoOuter\api\models\RestMessage;

/**
 * Class Link
 * @package FseoOuter\api
 */
class Link
{
    /**
     * Link constructor.
     */
    public function __construct()
    {
        $version = '2';
        $namespace = 'wp/v' . $version;
        $post_cat = 'get_post_cat/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $post_cat, array(
            'methods' => 'GET',
            'callback' => [$this, 'getPostInnerCat'],
            'args' => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'post_id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
            ],
        ));
        $sister_cat = 'get_thematic_link/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $sister_cat, array(
            'methods' => 'GET',
            'callback' => [$this, 'getThematicLink'],
            'args' => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
                'post_id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
            ],
        ));
        $sister_cat = 'get_thematic_link_gk/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $sister_cat, array(
            'methods' => 'GET',
            'callback' => [$this, 'getThematicLinkGk'],
            'args' => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
            ],
        ));
        $main_cat = 'get_main_cat';
        register_rest_route($namespace, '/' . $main_cat, array(
            'methods' => 'GET',
            'callback' => [$this, 'getMainCat'],
            'args' => [
                'id' => [
                    'validate_callback' => function ($param, $request, $key) {
                        return is_numeric($param);
                    }
                ],
            ],
        ));
    }

    /**
     * Получает список статей этой рубрики и внутренние рубрики
     * урл вида сайт/wp-json/wp/v2/get_post_cat/1 - где цифра ID рубрики
     * или урл вида сайт/wp-json/wp/v2/get_post_cat/1?post_id=123
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getPostInnerCat(\WP_REST_Request $request)
    {
        $cat_id = $request->get_param('id');
        $post_id = $request->get_param('post_id');
        $result = $this->createArrayPostInnerCat($cat_id, $post_id);
        return new ApiAnswer([
            'response' => $result,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' => 'Посты и внутренние категории получены',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }

    /**
     * @param int $cat_id
     * @param int|null $post_id
     * @return array
     */
    public function createArrayPostInnerCat(int $cat_id, int $post_id = null): array
    {
        $resalt = [];
        $args_post = [ //Набор параметров для поиска
            'category__in' => $cat_id,
            'post_type' => 'post',
            'post_status' => 'publish',
            'showposts' => 9999,
            'exclude' => $post_id
        ];
        $posts = get_posts($args_post); //получаем посты
        $cats = get_categories('child_of=' . $cat_id); // получаем внутренние категории
        $post_data = [];
        $cat_data = [];
        foreach ($posts as $post) {
            $post = ['id' => $post->ID, 'title' => $post->post_title, 'link' => get_permalink($post->ID)];
            $post_data[] = $post;
        }
        foreach ($cats as $cat) {
            $cat = ['term_id' => $cat->term_id, 'name' => $cat->name, 'link' => get_category_link($cat->term_id)];
            $cat_data[] = $cat;
        }
        $resalt['post'] = $post_data;
        $resalt['inner_cat'] = $cat_data;
        return $resalt;
    }

    /**
     * Получает список для тематической линковки
     * урл вида сайт/wp-json/wp/v2/get_thematic_link/1 - где цифра ID рубрики
     * или урл вида сайт/wp-json/wp/v2/get_post_cat/1?post_id=123
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getThematicLink(\WP_REST_Request $request)
    {
        $cat_id = $request->get_param('id');
        $post_id = $request->get_param('post_id');
        $result = $this->createArrayPostThematic($cat_id, $post_id);
        return new ApiAnswer([
            'response' => $result,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' => 'Получено',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }

    /**
     * Собираем для тематической
     * @param int $cat_id
     * @param int|null $post_id
     * @param int|null $gk
     * @return array
     */
    public function createArrayPostThematic(int $cat_id, int $post_id = null, int $gk = null): array
    {
        $args_post = [ //Набор параметров для поиска
            'category__in' => $cat_id,
            'post_type' => 'post',
            'post_status' => 'publish',
            'showposts' => 9999,
            'exclude' => $post_id
        ];
        $posts = get_posts($args_post); //получаем посты
        $cats = get_categories('parent=' . $cat_id); // получаем внутренние категории
        if ($gk === null) {
            $cat = get_category($cat_id);
            $cats[] = $cat;
        }
        $common_count = count($posts) + count($cats); // считаем сколько постов и ссылок на категории
        $result['necessarily']['post'] = [];
        $result['necessarily']['inner_cat'] = [];
        $result['unnecessarily']['post'] = [];
        $result['unnecessarily']['inner_cat'] = [];
        $top_category = get_ancestors($cat_id, 'category');
        if (isset($top_category[0])) {
            $result['top_cat'] = $top_category[0];
        } else {
            $result['top_cat'] = null;
        }
        if ($posts) { // Добавляем посты если есть в результаты
            foreach ($posts as $post) {
                $post = ['id' => $post->ID, 'title' => $post->post_title, 'link' => get_permalink($post->ID)];
                $result['necessarily']['post'][] = $post;
            }
        }
        if ($cats) { // Добавляем категории если есть в результаты
            foreach ($cats as $cat) {
                $cat = ['term_id' => $cat->term_id, 'name' => $cat->name, 'link' => get_category_link($cat->term_id)];
                $result['necessarily']['inner_cat'][] = $cat;
            }
        }
        if ($gk !== true) {
            if ($common_count <= 30 && $cats) { // если общее кол-во меньше 30 и есть подрубрики чекаем их и пишет в необзяательные
                $result = $this->addInnerCheck($cat_id, $cats, $common_count, $result);
            } elseif ($common_count <= 30 && !$cats && !is_null($result['top_cat'])) { // если общее кол-во меньше 30 и нет подрубрик и есть родитель по нему ищем посты
                $cats = get_categories('parent=' . $result['top_cat']); // получаем внутренние категории
                $result = $this->addInnerCheck($result['top_cat'], $cats, $common_count, $result);
            }
        }
        return $result;
    }

    /**
     * @param int $main_cat
     * @param array $cats
     * @param int $common_count
     * @param array $result
     * @return array
     */
    public function addInnerCheck(int $main_cat, array $cats, int $common_count, array $result)
    {
        $check_category = 0; // счетчик для того чтобы узнать былили внутри подрубрики
        $cats_inner_common = []; // пишем подрубрики если есть
        if ($cats) { // доп проверка на наличие категорий
            foreach ($cats as $cat) {
                $posts_inner = get_posts([
                    'category__in' => $cat->term_id,
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'showposts' => 9999
                ]);
                if ($posts_inner) { // пишем необзятельные посты
                    foreach ($posts_inner as $post) {
                        $post = ['id' => $post->ID, 'title' => $post->post_title, 'link' => get_permalink($post->ID)];
                        $result['unnecessarily']['post'][] = $post;
                    }
                }
                $cats_inner = get_categories('parent=' . $cat->term_id); // получаем внутренние категории
                $cats_inner_common = array_merge($cats_inner_common, $cats_inner);
                if ($cats_inner) { // пишем необзятельные рубрики
                    $check_category++;
                    foreach ($cats_inner as $cat_inner) {
                        $result['unnecessarily']['inner_cat'][] = ['term_id' => $cat_inner->term_id, 'name' => $cat_inner->name, 'link' => get_category_link($cat_inner->term_id)];
                    }
                }
            }
            // Считаем есть ли достаточное кол-во
            $common_count = $common_count + count($result['unnecessarily']['inner_cat']) + count($result['unnecessarily']['post']);
            if ($check_category > 0 && $common_count <= 30) { // Если не хватило и внутри есть подрубрики, запускаем ещё раз функцию
                $result = $this->addInnerCheck($main_cat, $cats_inner_common, $common_count, $result);
            } elseif ($check_category === 0 && $common_count <= 30 && $main_cat != $result['top_cat'] && !is_null($result['top_cat'])) {// Если не хватило и это не родительская рубрика
                $cats = get_categories('parent=' . $result['top_cat']); // получаем внутренние категории
                $result = $this->addInnerCheck($result['top_cat'], $cats, $common_count, $result);
            }
        }
        return $result;
    }

    /**
     * Получает список для тематической линковки
     * урл вида сайт/wp-json/wp/v2/get_thematic_link/1 - где цифра ID рубрики
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getThematicLinkGk(\WP_REST_Request $request)
    {
        $cat_id = $request->get_param('id');
        $result = $this->createArrayPostThematic($cat_id, null, true);
        return new ApiAnswer([
            'response' => $result,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' => 'Получено',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }

    /**
     * Получает структуру сайта (родительские рубрики)
     * урл вида сайт/wp-json/wp/v2/get_main_cat
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function getMainCat(\WP_REST_Request $request)
    {
        $category_main = $this->createArrayMainCat();
        return new ApiAnswer([
            'response' => $category_main,
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' => 'Родительские категории получены',
                ]),
            ],
            'status' => ApiAnswer::STATUS_SUCCESS,
        ]);
    }

    /**
     * @return array
     */
    public function createArrayMainCat()
    {
        $categorys = get_categories('parent=0');
        $cat_data = [];
        foreach ($categorys as $cat) {
            $cat = ['term_id' => $cat->term_id, 'name' => $cat->name, 'link' => get_category_link($cat->term_id)];
            $cat_data[] = $cat;
        }
        return $cat_data;
    }

    /**
     * @param int $cat_id
     * @return array
     */
    public function createSisterCat(int $cat_id): array
    {
        $sisters_cats = get_categories('parent=' . $cat_id);
        $cat_data = [];
        foreach ($sisters_cats as $cat) {
            $cat = ['term_id' => $cat->term_id, 'name' => $cat->name, 'link' => get_category_link($cat->term_id)];
            $cat_data[] = $cat;
        }
        return $cat_data;
    }
}

/**
 * add custom function to rest_api_init action
 */
add_action('rest_api_init', function () {
    $link = new Link();
});
