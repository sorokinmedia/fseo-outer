<?php

use FseoOuter\common\supporting\Category;

/**
 * замена заголовка
 * @return void
 */
function category_page_nav()
{
    if (is_category()) {
        echoCategoryTitle();
    }
}

/**
 * замена заголовка, англ версия
 * @deprecated
 * @return void
 */
function category_page_nav_en()
{
    if (is_category()) {
        echoCategoryTitle();
    }
}

/**
 * вывод h1 и рекламного блока
 * @return void
 */
function echoCategoryTitle()
{
    $cat_id = Category::getCurrCategory();
    $cat = get_category($cat_id);
    // Замена заголовка на шлаблоне f-seo-cat
    $cat_title = get_term_meta($cat->term_id, 'cat_title', true); // Получение всех полей из админки
    $templates = new Custom_Category_Templates();
    $template = $templates->get_category_template(); // Получает какой шаблон используется
    ?>
    <div class="cat_page_nav">
        <h1>
            <?= ($cat_title && $template === 'category-fseo-php') ? $cat_title : $cat->name ?>
        </h1>
    </div>
    <?php require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('fseo-plugins/f-seo-plugins.php')) {
        if ($template === 'category-fseo-php' || $template === 'fseo-cat-php') {
            echo do_shortcode('[rek_top]'); // после заголовка
        }
    }
}
