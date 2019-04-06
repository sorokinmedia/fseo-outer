<?php

namespace FseoOuter\api;

/**
 * Class Wamble
 * добавляет endpoint для API, который получит кол-во постов требующих растошнотки
 */
class Wamble
{
    const DATE_START = '2016-01-26';
    const DATE_FINISH = '2017-04-08';

    /**
     * Wamble constructor.
     */
    public function __construct()
    {
        // указываем роутинг для API
        $version = '2';
        $namespace = 'wp/v' . $version;
        $base1 = 'wamble_posts_count';
        register_rest_route($namespace, '/' . $base1, [
            'methods' => 'GET',
            'callback' => [$this, 'wamblePostsCount'],
            'args' => [],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
        $base2 = 'wamble_posts';
        register_rest_route($namespace, '/' . $base2, [
            'methods' => 'GET',
            'callback' => [$this, 'wamblePosts'],
            'args' => [],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            }
        ]);
    }

    /**
     * получить кол-во постов в периоде
     * @param $request
     * @return \WP_REST_Response
     */
    public function wamblePostsCount(\WP_REST_Request $request)
    {
        global $wpdb;
        $count = $wpdb->get_var(
            $wpdb->prepare("
                select count(p.ID) 
                from " . $wpdb->prefix . "posts as p
                where p.post_status='publish' AND p.post_type='post' 
                AND p.post_date_gmt BETWEEN %s AND %s",
                self::DATE_START,
                self::DATE_FINISH
            )
        );
        return new \WP_REST_Response((int)$count, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * получить ID постов в периоде
     * @param $request
     * @return \WP_REST_Response
     */
    public function wamblePosts(\WP_REST_Request $request)
    {
        global $wpdb;
        $posts = $wpdb->get_col(
            $wpdb->prepare("
                select p.ID from " . $wpdb->prefix . "posts as p
                where p.post_status='publish' AND p.post_type='post' 
                AND p.post_date_gmt BETWEEN %s AND %s",
                self::DATE_START,
                self::DATE_FINISH
            )
        );
        $posts = array_map(function ($value) {
            return (int)$value;
        }, $posts); // конверт строк в integer
        return new \WP_REST_Response($posts, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }
}

/**
 * add custom function to rest_api_init action
 */
add_action('rest_api_init', function () {
    $wamble = new Wamble();
});
