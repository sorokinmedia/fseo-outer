<?php

namespace FseoOuter\common\contents;

use FseoOuter\common\Mobile_Detect;

/**
 * Class ContentsPost
 * @package FseoOuter\common\contents
 */
class ContentsPost
{
    // defaults options
    static $main_content;

    protected static $inst;

    public $opt = [
        // Отступ слева у подразделов в px.
        'margin' => 10,
        // Теги по умолчанию по котором будет строиться содержание. Порядок имеет значение.
        // Можно указать атрибут class элемента: array('.foo','.two'). Можно указыать строкой: 'h2 h3 h4'
        'selectors' => array('h2', 'h3', 'h4', 'h5'),
        // Ссылка на возврат к содержанию. '' - убрать ссылку
        'to_menu' => 'к содержанию ↑',
        // Заголовок. '' - убрать заголовок
        'title' => 'Содержание:',
        // Css стили. '' - убрать стили
        'css' => '.kc-gotop{ display:block; text-align:right; } .kc-title{ font-style:italic; padding:1em 0; }',
        // Минимальное количество найденных тегов, чтобы содержание выводилось.
        'min_found' => 3,
        // Минимальная длина (символов) текста, чтобы содержание выводилось.
        'min_length' => 2000,
        // Ссылка на страницу для которой собирается содержание. Если содержание выводиться на другой странице...
        'page_url' => '',
        // Название шоткода
        'shortcode' => 'contents',
    ];

    // collect html contents
    public $contents;

    private $temp;

    public function __construct($args = [])
    {
        $this->set_opt($args);
        return $this;
    }

    ## статический экземпляр

    public function set_opt($args = [])
    {
        $this->opt = (object)array_merge($this->opt, $args);
    }

    public static function fseoContentsShortcode($content)
    {
        $social_post = (int)get_option('fseo_outer_contents', true);
        $social_cat = (int)get_option('fseo_cat_contents', true);
        if (($social_post === 1 && !is_category()) || ($social_cat === 1 && is_category())) {
            $content = preg_replace('#<span.*?id="more-(.*?)".*?></span>#', '<span id="more-\1"></span></p>' . '[contents]', $content);
            $args = [];
            $posts = self::init($args);
            return $posts->shortcode($content);
        }
        return $content;
    }

    public static function init($args = array())
    {
        self::$inst === null && self::$inst = new self($args);
        return self::$inst;
    }

    /**
     * Обрабатывает текст, превращает шоткод в нем в содержание.
     * @param (string) $content текст, в котором есть шоткод.
     * @return string Обработанный текст с содержанием, если в нем есть шоткод.
     */
    public function shortcode($content)
    {
        if (false === strpos($content, '[' . $this->opt->shortcode)) {
            return $content;
        }
        // получаем данные о содержании
        if (!preg_match('~^(.*)\[' . $this->opt->shortcode . '([^\]]*)\](.*)$~s', $content, $m)) {
            return $content;
        }
        $contents = $this->make_contents($m[3], $m[2]);
        return $m[1] . $contents . $m[3];
    }

    public function make_contents(& $content, $tags = 'h2 h3 h4 h5')
    {
        if (mb_strlen($content) < $this->opt->min_length) {
            return;
        } // выходим если текст короткий
        $content_temp = $content;
        $count = $this->check_contents_count($content_temp, $tags);
        $content_temp = $content;
        $_content = $this->get_contents_struct($content_temp, $tags);
        if (!$count || $count < $this->opt->min_found) {
            return;
        }
        $content = $_content; // опять работаем с важной $content
        // html содержания
        $contents = '';
        $hidden = (int)get_option('hidden_content');
        $lang = get_bloginfo('language');
        if ($lang === 'en-US') {
            $contents_title = 'Contents';
        } else {
            $contents_title = ($hidden === 1 ? "Показать содержание" : "Скрыть содержание");
        }
        $contents .= '<blockquote class="contents" id="kcmenu"><div class="contents_title">' . $contents_title . '</div>' . "\n";
        $contents .= '<ul class="contents '. ($hidden === 1 ? "kc_contents_hidden" : "") . '" ' . (!$this->opt->title ? ' id="kcmenu"' : '') . '><div class="right_contents">' . "\n" .
            implode('', $this->contents) .
            '</ul></blockquote>' . "\n";

        $this->contents = '<div class="contents-wrap">' . $contents . '</div>';
        return $this->contents = $contents;
    }

    /**
     * Заменяет заголовки в переданном тексте (по ссылке), создает и возвращает содержание.
     * @param (string)        $content текст на основе которого нужно создать содержание.
     * @param (array/string)  $tags    массив тегов, которые искать в переданном тексте.
     *                                 Можно указать: имена тегов "h2 h3" или классы элементов ".foo .foo2".
     * @return                string html код содержания.
     */

    public function check_contents_count($content, $tags)
    {
        $this->temp = new \stdClass();
        $this->temp->i = 0;
        $this->contents = [];
        if (is_string($tags) && $tags = trim($tags)) {
            $tags = array_map('trim', preg_split('~\s+~', $tags));
        }
        if (!$tags) {
            $tags = $this->opt->selectors;
        }
        // set patterns from given $tags
        $class_patt = $tag_patt = '';
        $level = array();
        foreach ($tags as $k => $val) {
            // class
            if ($val[0] === '.') {
                $val = substr($val, 1);
                $link = &$class_patt;
            } // html tag
            else {
                $link = &$tag_patt;
            }

            if ($link) $link .= '|';
            $link .= $val;
            $level[] = $val;
        }
        $this->temp->tag_level = array_flip($level);
        // заменяем все заголовки и собираем содержание в $this->contents
        $patt_in = [];
        $patt_suffix = '>(.*?)</(?:\\1)>';
        if ($class_patt) {
            $patt_in[] = '(?:<([^\s]+)([^>]*class=["\'][^>]*(' . $class_patt . ')[^>]*["\'][^>]*)' . $patt_suffix . ')';
        }
        if ($tag_patt) {
            $patt_in[] = '(?:<(' . $tag_patt . ')([^>]*)' . $patt_suffix . ')';
        }
        //var_dump($patt_in);
        $patt_in = implode('|', $patt_in);
        if (count($patt_in) > 1) {
            return __CLASS__ . ': don`t use tags and attributes selectors in the same time - use separately';
        }
        $_content = preg_replace_callback("@$patt_in@is", array(&$this, '__make_contents_callback'), $content, -1, $count);
        return $count;
    }

    public function get_contents_struct($content, $tags)
    {
        $this->temp = new \stdClass();
        $this->temp->i = 0;
        $this->contents = [];
        if (is_string($tags) && $tags = trim($tags)) {
            $tags = array_map('trim', preg_split('~\s+~', $tags));
        }
        if (!$tags) {
            $tags = $this->opt->selectors;
        }
        // set patterns from given $tags
        $class_patt = $tag_patt = '';
        $level = array();
        foreach ($tags as $k => $val) {
            // class
            if ($val[0] === '.') {
                $val = substr($val, 1);
                $link = &$class_patt;
            } // html tag
            else {
                $link = &$tag_patt;
            }

            if ($link) $link .= '|';
            $link .= $val;
            $level[] = $val;
        }
        $this->temp->tag_level = array_flip($level);
        // заменяем все заголовки и собираем содержание в $this->contents
        $patt_in = array();
        $patt_suffix = '>(.*?)</(?:\\1)>';
        if ($class_patt) {
            $patt_in[] = '(?:<([^\s]+)([^>]*class=["\'][^>]*(' . $class_patt . ')[^>]*["\'][^>]*)' . $patt_suffix . ')';
        }
        if ($tag_patt) {
            $patt_in[] = '(?:<(' . $tag_patt . ')([^>]*)' . $patt_suffix . ')';
        }
        $patt_in = implode('|', $patt_in);
        if (count($patt_in) > 1) {
            return __CLASS__ . ': don`t use tags and attributes selectors in the same time - use separately';
        }
        $_content = preg_replace_callback("@$patt_in@is", array(&$this, '__make_contents_callback'), $content, -1, $count);
        return $_content;
    }


    public function is_user_role($role, $user_id = null)
    {
        $user = is_numeric($user_id) ? get_userdata($user_id) : wp_get_current_user();

        if (!$user) {
            return false;
        }
        return in_array($role, (array)$user->roles);
    }

    // вырезает шоткод из контента
    public function strip_shortcode($text)
    {
        return preg_replace('~\[' . $this->opt->shortcode . '[^\]]*\]~', '', $text);
    }

    // callback функция для замены и сбора содержания
    private function __make_contents_callback($match)
    {
        $tag = $match[1];
        $attrs = $match[2];
        if (count($match) === 4) {
            $level_tag = $match[1];
            $title = $match[3];
        } elseif (count($match) === 5) {
            $level_tag = $match[3];
            $title = $match[4];
        } else {
            return 'parse error of preg_replace_callback() in ' . __CLASS__ . ' class';
        }
        $anchor = $this->__sanitaze_anchor($title);
        //die( print_r( $match ) );
        $level = @ $this->temp->tag_level[$level_tag];
        if ($level > 0) {
            $sub = ' class="sub sub_' . $level . '"';
        } else {
            $sub = ' class="top"';
        }
        // собираем содержание
        if ($level > 0) {
            $this->contents[] = "\t" . '<span' . $sub . '><i class="fa fa-caret-right" aria-hidden="true"></i><a href="' . $this->opt->page_url . '#' . $anchor . '">' . $title . '</a>&nbsp;&nbsp;&nbsp;&nbsp;</span>' . "\n";
        } else {
            $this->contents[] = "\t" . '</div><li' . $sub . '><a href="' . $this->opt->page_url . '#' . $anchor . '">' . $title . '</a></li><div class="right_contents">' . "\n";
        }
        // заменяем
        $out = '';
        $out .= '<a class="kc-anchor" name="' . $anchor . '"></a>' . "\n" . '<' . $tag . $attrs . '>' . $title . '</' . $tag . '>';
        return $out;
    }

    // транслитерация для УРЛ
    private function __sanitaze_anchor($str)
    {
        $detect = new Mobile_Detect();
        $conv = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ы' => 'Y', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );

        $str = strip_tags($str);
        $str = strtr($str, $conv);
        $str = strtolower($str);
        $str = preg_replace('/[^-a-z0-9_~+=$\*\.]+/u', '-', $str); // все ненужное на "-"
        $str = preg_replace('/-+/', '-', $str);
        if ($detect->isMobile()) {
            $utm = '?utm_source=mobile_table_of_content';
        } else {
            $utm = '?utm_source=table_of_content';
        }
        $str .= $utm;
        return $str;
    }
}
