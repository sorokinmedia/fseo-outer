<?php

use FseoOuter\common\supporting\Category;

/**
 * блок навигации для category.php, замена заголовка
 */
function category_page_nav()
{
    if (is_category()) {
        $cat_id = Category::getCurrCategory();
        $cat = get_category($cat_id);
        if ($cat->parent != '0') {
            $p_cat = get_category($cat->parent);
        }
        $childs = get_categories('child_of=' . $cat->term_id);
        //Замена заголовка на шлаблоне f-seo-cat
        $cat_title = get_term_meta($cat->term_id, 'cat_title', true); // Получение всех полей из админки
        $templates = new Custom_Category_Templates();
        $template = $templates->get_category_template(); // Получает какой шаблон используется
        ?>
        <div class="cat_page_nav">
            <h1>
                <?php if ($cat_title && $template === 'category-fseo-php') {
                    echo $cat_title;
                } else {
                    echo $cat->name;
                } ?>
            </h1>
            <!--noindex-->
            <div class="cat_page_nav_links">
                Вы просматриваете раздел <strong><?= $cat->name ?></strong>
                <?php if (!empty($p_cat)) { ?>
                    , расположенный в большом разделе <a class="strong" rel="nofollow"
                                                         href="<?= get_category_link($p_cat->term_id) ?>"
                                                         title="<?= $p_cat->name ?>"><?= $p_cat->name ?></a>
                <?php } ?>
                <?php if (count($childs)) { ?>
                    <div class="cat_page_nav_childs">
                        Подразделы:
                        <ul>
                            <?php wp_list_categories('title_li=&depth=1&optioncount=0&child_of=' . $cat->term_id); ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <!--/noindex-->
            <?php require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            if (is_plugin_active('fseo-plugins/f-seo-plugins.php')) {
                if ($template === 'category-fseo-php' || $template === 'fseo-cat-php') {
                    echo do_shortcode('[rek_top]'); // после заголовка
                }
            } ?>
        </div>
    <?php }
}

/**
 * блок навигации для category.php, замена заголовка
 */
function category_page_nav_en()
{
    if (is_category()) { ?>
        <?php $cat_id = Category::getCurrCategory();
        $cat = get_category($cat_id);
        if ($cat->parent != '0') {
            $p_cat = get_category($cat->parent);
        }
        $childs = get_categories('child_of=' . $cat->term_id);
        // Замена заголовка на шлаблоне f-seo-cat
        $cat_title = get_term_meta($cat->term_id, 'cat_title', true); // Получение всех полей из админки
        $templates = new Custom_Category_Templates();
        $template = $templates->get_category_template(); // Получает какой шаблон используется
        ?>
        <div class="cat_page_nav">
            <h1><?php
                if ($cat_title && $template === 'category-fseo-php') {
                    echo $cat_title;
                } else {
                    echo $cat->name;
                } ?></h1>
            <!--noindex-->
            <div class="cat_page_nav_links">
                You are currently viewing section <strong><?= $cat->name ?></strong>
                <?php if (!empty($p_cat)) { ?>
                    , located in a large section <a class="strong" rel="nofollow"
                                                    href="<?= get_category_link($p_cat->term_id); ?>"
                                                    title="<?= $p_cat->name; ?>"><?= $p_cat->name; ?></a>
                <?php } ?>
                <?php if (count($childs)) { ?>
                    <div class="cat_page_nav_childs">
                        Subsections:
                        <ul>
                            <?php wp_list_categories('title_li=&depth=1&optioncount=0&child_of=' . $cat->term_id); ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <!--/noindex-->
        </div>
    <?php }
}
