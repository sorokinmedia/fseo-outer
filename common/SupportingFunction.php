<?php
namespace FseoOuter\common;

/**
 * Вспомогательный класс
 * Class SupportingFunction
 * @package FseoOuter\common
 */
class SupportingFunction
{
    /**
     * Парсинг поста - картинки, блоки, видео
     */
    public function parseArticleText() {
        $articleID = get_the_ID();
        $post = get_post($articleID);
        $text = $post->post_content;
        preg_match_all('/<img[^>]+>/i',$text, $imgs);
        preg_match_all('/\[\/embed\]/i',$text, $frames);
        preg_match_all('|<div class=\"warning\">(.*?)</div>|is',$text, $divs_warnings);
        preg_match_all('|<div class=\"advice\">(.*?)</div>|is',$text, $divs_advice);
        preg_match_all('|<div class=\"stop\">(.*?)</div>|is',$text, $divs_stop);
        preg_match_all('|<div class=\"zakon\">(.*?)</div>|is',$text, $divs_zakon);
        preg_match_all('|href=\"([^\"]+)|i', $text, $links);


        $outLinkCounter = 0;
        $docsCounter = 0;
        foreach($links[0] as $link){
            if(mb_strpos($link, get_site_url()) == false) $outLinkCounter++;
            if(
                mb_strpos($link, '.doc')  ||
                mb_strpos($link, '.docx') ||
                mb_strpos($link, '.txt')  ||
                mb_strpos($link, '.pdf')  ||
                mb_strpos($link, '.ods')
            ) $docsCounter++;
        }
        //var_dump($links);
        $thmb = get_the_post_thumbnail($articleID) ? 1 : 0;
        $value = [
            'blocks' => count($divs_warnings[0]) + count($divs_stop[0]) + count($divs_advice[0]),
            'zakon' => count($divs_zakon[0]),
            'images' => $thmb + count($imgs[0]),
            'videos' => count($frames[0]),
            'out_links' => $outLinkCounter,
            'docs' => $docsCounter
        ];
        update_post_meta($articleID, 'post_parsing', json_encode($value));
    }
}