<?php
namespace FseoOuter\common\supporting;

/**
 * Class Category
 * @package FseoOuter\common\contents
 */
class Category
{

    /**
     * Возвращает ID основной категории
     * @return int|mixed
     */
    public static function getCurrCategory()
    {
        if (is_single()) {
            $postid = get_curr_post();
            $cat = get_the_category($postid);
            $cat_id = $cat[0]->term_id;
        } elseif (is_category()) {
            $cat_id = get_query_var('cat');
        } else {
            $cat_id = 0;
        }
        return $cat_id;
    }

}