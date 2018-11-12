<?php
namespace FseoOuter\api;

/**
 * Класс для работы с рубриками через апи
 * Class Term
 */
class Term
{
    /**
     * all_post constructor.
     */
    public function __construct()
    {
        // указываем роутинг для API
        $version = '2';
        $namespace = 'wp/v' . $version;
        $all_terms = 'all-terms';
        register_rest_route($namespace, '/' . $all_terms, [
            'methods' => 'GET',
            'callback' => [$this, 'getAllTerms'],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
        $cluster_delete = 'cluster_meta_delete/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $cluster_delete, [
            'methods' => 'POST',
            'callback' => [$this, 'clusterMetaDelete'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
        $space_category = 'space_category/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $space_category, [
            'methods' => 'GET',
            'callback' => [$this, 'getSpaceCategory'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
        ]);
        $term_route = 'term-route/(?P<id>\d+)';
        register_rest_route($namespace, '/' . $term_route, [
            'methods' => 'GET',
            'callback' => [$this, 'termRoute'],
            'args' => [
                'id' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ],
            ],
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ]);
    }

    /**
     * get only top cats
     * @return array
     */
    public function getTopCats()
    {
        return get_terms([
            'taxonomy' => 'category',
            'parent' => 0,
            'hide_empty' => false
        ]); // возвращаем массив топовых категорий
    }

    /**
     * get childs count by parent_id
     * @param $parent_id
     * @return integer
     */
    public function getChildsCount($parent_id)
    {
        return get_terms([
            'taxonomy' => 'category',
            'parent' => $parent_id,
            'fields' => 'count',
            'hide_empty' => false
        ]); // возвращаем количество дочерних категорий
    }

    /**
     * get child cats from parent cat, recursive
     * @param $parent_id
     * @param $depth
     * @return array
     */
    public function getChilds($parent_id, $depth)
    {
        $childs = get_terms([
            'taxonomy' => 'category',
            'parent' => $parent_id,
            'hide_empty' => false
        ]); // получаем дочерние категории для родителя
        foreach ($childs as $child){
            $cats[$child->term_id] = $depth . $child->name; // пишем дочернюю категорию с нужным количеством тире
            if ($this->getChildsCount($child->term_id)){ // если есть дочерние уровнем ниже
                $cats = $cats + $this->getChilds($child->term_id, $depth . '—'); // рекурсивно пишем и их, но добавляем еще одно тире
            }
        }
        return $cats;
    }

    /**
     * get childs from very top cats
     * @param $top_id
     * @return array
     */
    public function getTopChilds($top_id)
    {
        $parents = get_terms([
            'taxonomy' => 'category',
            'parent' => 0,
            'include' => [$top_id],
            'hide_empty' => false
        ]); // все дочерние топовой категории
        foreach ($parents as $parent){
            $cats[$parent->term_id] = $parent->name; // пишем в массив топовую категорию
            if ($this->getChildsCount($parent->term_id)){ // если есть дочерние
                $cats = $cats + $this->getChilds($parent->term_id, '—'); // пишем дочерние, добавляем тире
            }
        }
        return $cats;
    }

    /**
     * get the final hierarchical array of cats
     * @param $object
     * @return \WP_REST_Response
     */
    public function getAllTerms($object)
    {
        $tops = $this->getTopCats(); // получаем топовые категории
        $cats = []; // результирующий массив term_id=>name
        foreach ($tops as $top){
            $cats = $cats + $this->getTopChilds($top->term_id); // получаем дочерние топовых категорий
        }
        return new \WP_REST_Response($cats, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * transfer data from post to term, delete draft post
     * @param $request
     * @return \WP_REST_Response
     */
    public function clusterMetaDelete(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $term = get_term($id, 'category');
        update_term_meta( $term->term_id, 'cat_title', '');
        update_term_meta( $term->term_id, 'cat_top_description', '');
        update_term_meta( $term->term_id, 'seo_title', '');
        update_term_meta( $term->term_id, 'seo_description', '');
        update_term_meta( $term->term_id, 'seo_keywords', '');
        update_term_meta( $term->term_id, 'cat_template', '');
        return new \WP_REST_Response($term, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * get category for space
     * @param $request
     * @return \WP_REST_Response
     */
    public function getSpaceCategory(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $category = get_category($id);
        $posts = new \WP_Query( array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'category__in' => $category->term_id
        ) );
        $link = get_term_link($category->term_id, 'category');
        $count = $posts->found_posts;
        $category->count = (int) $count;
        $category->link = $link;
        return new \WP_REST_Response($category, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * получить полный путь для категории
     * @param $request
     * @return \WP_REST_Response
     */
    public function termRoute(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        $term = get_term($id, 'category');
        $route = $term->name;
        if ($term->parent != 0){
            $route = $this->addParentTerm($term->parent, $route) . ' - ' . $route;
        }
        return new \WP_REST_Response($route, 200); // возвращаем ответ с кодом 200 и массивом категорий
    }

    /**
     * рекурсивная функция для добавления родителей в роут
     * @param $parent_id
     * @param $route
     * @return string
     */
    public function addParentTerm($parent_id, $route)
    {
        $parent = get_term($parent_id, 'category');
        $route = $parent->name;
        if ($parent->parent != 0){
            $route = $this->addParentTerm($parent->parent,$route) . ' - ' . $route;
        }
        return $route;
    }
}

/**
 * add custom function to rest_api_init action
 */
add_action('rest_api_init', function () {
    $all_terms = new Term();
});