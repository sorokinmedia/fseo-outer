<?php
namespace FseoOuter\api;
use FseoOuter\api\models\ApiAnswer;
use FseoOuter\api\models\RestMessage;
use FseoOuter\common\helpers\Translit;

/**
 * Класс для работы с постами через апи
 * Class Post
 */
class Post
{
    /**
     * all_post constructor.
     */
    public function __construct()
    {
        // указываем роутинг для API
        $version = '2';
        $namespace = 'wp/v' . $version;
        $all_posts = 'all-post/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $all_posts, [
            'methods' => 'GET',
            'callback' => [$this, 'getAllPosts'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            }
        ]);
        $post_to_term = 'post_to_term/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $post_to_term, [
            'methods' => 'GET',
            'callback' => [$this, 'postToTerm'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            /*'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },*/
        ]);
        $post_width_comments = 'post-with-comments/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $post_width_comments, [
            'methods' => 'GET',
            'callback' => [$this, 'postWithComments'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            }
        ]);
        $post_width_comments_count = 'post-with-comment-count/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $post_width_comments_count, [
            'methods' => 'GET',
            'callback' => [$this, 'postWithCommentCount'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            }
        ]);
        $check_slug = 'check_slug';
        register_rest_route($namespace, '/' . $check_slug,[
            'methods' => 'POST',
            'callback' => [$this, 'checkSlug'],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
    }

    /**
     * get the final post object
     * @param $request
     * @return \WP_REST_Response
     */
    public function getAllPosts(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $post = get_post($id);
        $post_all = [];
        $post_all['cat_title'] = $post->post_title;
        $post_all['cat_top_description'] = get_the_post_thumbnail($post, 'medium', ['class' => 'alignleft']) . $post->post_content;
        $post_all['seo_title'] = get_post_meta($post->ID, '_aioseop_title', true);
        $post_all['seo_description'] = get_post_meta($post->ID, '_aioseop_description', true);
        $post_all['seo_keywords'] = get_post_meta($post->ID, '_aioseop_keywords', true);
        $post_all['cat_template'] = 'category-fseo.php';
        return new \WP_REST_Response($post_all, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * transfer data from post to term, delete draft post
     * @param $request
     * @return \WP_REST_Response
     */
    public function postToTerm(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $post = get_post($id);
        $terms = get_the_category($post->ID);
        $term = $terms[0];
        update_term_meta( $term->term_id, 'cat_title', $post->post_title);
        $site_url = get_site_url();
        $thumb_size = (in_array($site_url, ['http://myrealproperty.ru/'])) ? 'large' : 'medium';
        if( ! function_exists('activate_plugin') ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        update_term_meta( $term->term_id, 'cat_top_description', get_the_post_thumbnail($post, $thumb_size, ['class' => 'alignleft']) . $post->post_content);
        if (!get_term_meta($term->term_id, 'cat_comments', true)) {
            $my_post = [
                'post_type' => 'cat_comm',
                'post_title' => 'Cat comm для категории ' . $term->term_id,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
            ];
            $p_id = wp_insert_post($my_post);
            if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') || is_plugin_active('all-in-one-seo-pack-pro/all-in-one-seo-pack.php')) {
                update_post_meta($p_id, '_aioseop_noindex', 'on');
                update_post_meta($p_id, '_aioseop_nofollow', 'on');
            }
            update_post_meta($p_id, 'term_id', $term->term_id);
            update_term_meta($term->term_id, 'cat_comments', $p_id);
        }

        if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') || is_plugin_active('all-in-one-seo-pack-pro/all-in-one-seo-pack.php')) {
            update_term_meta($term->term_id, 'seo_title', get_post_meta($post->ID, '_aioseop_title', true));
            update_term_meta($term->term_id, 'seo_description', get_post_meta($post->ID, '_aioseop_description', true));
            update_term_meta($term->term_id, 'seo_keywords', get_post_meta($post->ID, '_aioseop_keywords', true));
        } else {
            $yoast = get_option('wpseo_taxonomy_meta');
            $yoast['category'][$term->term_id]['wpseo_title'] = get_post_meta($post->ID, '_yoast_wpseo_title', true);
            $yoast['category'][$term->term_id]['wpseo_desc'] = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
            update_option('wpseo_taxonomy_meta', $yoast);
        }
        update_term_meta( $term->term_id, 'cat_template', 'category-fseo.php');
        wp_delete_post( $post->ID);
        return new \WP_REST_Response($term, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * get the final post object
     * @param $request
     * @return \WP_REST_Response
     */
    public function postWithComments(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $post = get_post($id);
        $query = 'select c.comment_ID, c.comment_content from wp_comments as c
                  where c.comment_post_ID = %d AND c.comment_approved=1';
        global $wpdb;
        $comments = $wpdb->get_results( $wpdb->prepare($query, $id), ARRAY_A );
        $response = [
            'post' => [
                'title' => $post->post_title,
                'content' => $post->post_content
            ],
            'comments' => $comments,
        ];
        return new \WP_REST_Response($response, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * get the final post object
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function postWithCommentCount(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $post = get_post($id);
        $count = wp_count_comments($post->ID);
        $response = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'comment_count' => (int) $count->approved
        ];
        return new \WP_REST_Response($response, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * Проверяет наличие слага
     * @param \WP_REST_Request $request
     * @return ApiAnswer
     */
    public function checkSlug(\WP_REST_Request $request)
    {
        $params = $request->get_body_params();
        if (!empty($params['slug'])) {
            $params['slug'] = Translit::strToUrl($params['slug']);
            global $wpdb;
            $counts = $wpdb->get_var($wpdb->prepare("SELECT count(post_name) FROM " . $wpdb->posts . " WHERE post_name like '%s'", $params['slug']));
            if ($counts >= 1) {
                $counts = $counts + 1;
                $params['slug'] = $params['slug'] . '-' . $counts;
            }

            if (!wp_update_post([
                'ID' => $params['id'],
                'post_name' => $params['slug']
            ], true)) {
                return new ApiAnswer([
                    'response' => $params['slug'],
                    'messages' => [
                        new RestMessage([
                            'type' => RestMessage::TYPE_SERVER_ERROR,
                            'message' => 'ОШбика сохранения поста',
                        ]),
                    ],
                    'status' => ApiAnswer::STATUS_SUCCESS,
                ]);
            }
        }
        return new ApiAnswer([
            'response' => $params['slug'],
            'messages' => [
                new RestMessage([
                    'type' => RestMessage::TYPE_SUCCESS,
                    'message' =>'Слаг получен',
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
    $all_post = new Post();
});