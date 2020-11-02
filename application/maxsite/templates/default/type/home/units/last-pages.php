<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * 2-11-2020
 */

/**
Универсальный юнит для вывода записей по множеству критериев

[unit]
file = last-pages.php
limit = 3
[/unit]

Значение ключей по-умолчанию см. переменную $def

 */

$home_cache_time = (int) mso_get_option('home_cache_time', 'templates', 0);
// $cache_key = getinfo('template') . '-' .  __FILE__ . '-' . mso_current_paged() . '-' . $UNIT_NUM;

if ($home_cache_time > 0 and $k = mso_get_cache($UNIT_UID)) {
    echo $k;
} else {
    $def = [
        'my_pages' => true, // если true то готовые записи уже в $PAGES

        'limit' => 3,
        'cat_id' => "0",
        'exclude_cat_id' => "0",
        'page_id' => "0",
        'pagination' => false,
        'type' => 'blog',
        'order' => 'page_date_publish',
        'order_asc' => 'desc',
        'cut' => '»»»',
        'show_cut' => false,
        'date_now' => true,
        'page_id_autor' => 0,
        'function_add_custom_sql' => '',
        'pages_reverse' => false,

        'thumb' => true,
        'thumb_width' => 100,
        'thumb_height' => 100,

        'thumb_class' => 'b-left mar15-r rounded',
        'thumb_link_class' => '',
        'thumb_link' => true,
        'thumb_add_start' => '',
        'thumb_add_end' => '',
        'thumb_type_resize' => 'resize_full_crop_center',

        'content' => true,
        'content_words' => 0,
        'content_chars' => 0,
        'content_cut' => '...',
        'content_start' => '<div class="mso-page-content clearfix">',
        'content_end' => '</div>',

        'line1' => '[title][date]',
        'line2' => '[thumb]',
        'line3' => '',
        'line4' => '[cat]',
        'line5' => '<p class="t-right"><a href="[page_url]">Читать дальше</a></p>',

        'line1_start' => '',
        'line1_end' => '',
        'line2_start' => '',
        'line2_end' => '',
        'line3_start' => '',
        'line3_end' => '',
        'line4_start' => '',
        'line4_end' => '',
        'line5_start' => '',
        'line5_end' => '',

        'title_start' => '<h4>',
        'title_end' => '</h4>',

        'block_start' => '<div class="layout-center flex flex-wrap pad5-rl">',
        'block_end' => '</div>',

        'page_start' => '<div class="w32 w48-tablet w100-phone pad20 mar15-tb bor1px bor-solid bor-gray400 rounded">',
        'page_end' => '</div>',

        'date' => 'j F Y, H:i',
        'date_start' => '<p class="italic t90 im-calendar"><time datetime="[page_date_publish_iso]">',
        'date_end' => '</time></p>',

        'cat_start' => '<p class="im-folder t90">',
        'cat_end' => '',
        'cat_sep' => ',&NBSP;',

        'tag_start' => '<p class="im-tag t90">',
        'tag_end' => '</p>',
        'tag_sep' => ',&NBSP;',
        'tag_class' => '',

        'author_start' => '',
        'author_end' => '',

        'read' => '»»»',
        'read_start' => '<p>',
        'read_end' => '</p>',

        'comments_count_start' => '',
        'comments_count_end' => '',

        'placehold' => true,
        'placehold_path' => 'https://via.placeholder.com/',
        'placehold_pattern' => '[W]x[H].png',
        'placehold_file' => 'data',
        'placehold_data_bg' => '#EEEEEE',

        'pagination_start' => '',
        'pagination_end' => '',
        'pagination_in_block' => true,

        'exclude_page_allow' => true,
        'exclude_page_add' => true,
    ];

    $UNIT = mso_merge_array($UNIT, $def);

    ob_start();

    if (isset($PAGES) and $PAGES and $UNIT['my_pages']) {
        // записи в $PAGES
        $b = new Block_pages(false);
        $b->set_pages($PAGES, $PAGINATION);
    } else {
        $b = new Block_pages([
            'limit'                   => $UNIT['limit'], // кол-во записей
            'cat_id'                  => $UNIT['cat_id'], // номера рубрик через запятую или пробел
            'exclude_cat_id'          => $UNIT['exclude_cat_id'], // исключить рубрики
            'page_id'                 => $UNIT['page_id'], // id записей
            'pagination'              => $UNIT['pagination'], // включить пагинацию
            'type'                    => $UNIT['type'], // тип записей (blog, static...)
            'order'                   => $UNIT['order'], // поле сортировки
            'order_asc'               => $UNIT['order_asc'], // порядок сортировки
            'cut'                     => $UNIT['cut'], // ссылка [cut]
            'show_cut'                => $UNIT['show_cut'], // показывать ссылку  [cut]
            'date_now'                => $UNIT['date_now'], // учитывать время публикации
            'page_id_autor'           => $UNIT['page_id_autor'], // номер автора записей
            'exclude_page_allow'      => $UNIT['exclude_page_allow'], // учитывать исключенные ранее страницы
            'function_add_custom_sql' => $UNIT['function_add_custom_sql'], // своя функция для sql-запроса
            'pages_reverse'           => $UNIT['pages_reverse'], // обратить порядок вывода записей
        ], $UNIT);
    }

    if ($b->go) {
        $b->output(array(
            'block_start'          => $UNIT['block_start'], // общий блок вывода 
            'block_end'            => $UNIT['block_end'],
                                   
            'content'              => $UNIT['content'], // контент записи
            'content_words'        => $UNIT['content_words'],
            'content_chars'        => $UNIT['content_chars'],
            'content_cut'          => $UNIT['content_cut'],
            'content_start'        => $UNIT['content_start'],
            'content_end'          => $UNIT['content_end'],
                                   
            'thumb'                => $UNIT['thumb'], // миниатюра
            'thumb_width'          => $UNIT['thumb_width'],
            'thumb_height'         => $UNIT['thumb_height'],
            'thumb_class'          => $UNIT['thumb_class'],
            'thumb_link_class'     => $UNIT['thumb_link_class'],
            'thumb_link'           => $UNIT['thumb_link'],
            'thumb_add_start'      => $UNIT['thumb_add_start'],
            'thumb_add_end'        => $UNIT['thumb_add_end'],
            'thumb_type_resize'    => $UNIT['thumb_type_resize'],
                                   
            'line1'                => $UNIT['line1'], // линия вывода до контента
            'line2'                => $UNIT['line2'], // линия вывода до контента
            'line3'                => $UNIT['line3'], // линия вывода до контента
            'line4'                => $UNIT['line4'], // линия вывода после контента
            'line5'                => $UNIT['line5'], // линия вывода после контента
                                   
            'line1_start'          => $UNIT['line1_start'], // обрамление линий
            'line1_end'            => $UNIT['line1_end'],
            'line2_start'          => $UNIT['line2_start'],
            'line2_end'            => $UNIT['line2_end'],
            'line3_start'          => $UNIT['line3_start'],
            'line3_end'            => $UNIT['line3_end'],
            'line4_start'          => $UNIT['line4_start'],
            'line4_end'            => $UNIT['line4_end'],
            'line5_start'          => $UNIT['line5_start'],
            'line5_end'            => $UNIT['line5_end'],
                                   
            'title_start'          => $UNIT['title_start'], // заголовок записи
            'title_end'            => $UNIT['title_end'],
                                   
            'page_start'           => $UNIT['page_start'], // блок записи
            'page_end'             => $UNIT['page_end'],
                                   
            'date'                 => $UNIT['date'], // формат даты
            'date_start'           => $UNIT['date_start'],
            'date_end'             => $UNIT['date_end'],
                                   
            'cat_start'            => $UNIT['cat_start'], // формат рубрики
            'cat_end'              => $UNIT['cat_end'],
            'cat_sep'              => $UNIT['cat_sep'],
                                   
            'tag_start'            => $UNIT['tag_start'], // формат меток
            'tag_end'              => $UNIT['tag_end'],
            'tag_sep'              => $UNIT['tag_sep'],
            'tag_class'            => $UNIT['tag_class'],
                                   
            'author_start'         => $UNIT['author_start'], // формат автора
            'author_end'           => $UNIT['author_end'],
                                   
            'read'                 => $UNIT['read'], // формат «Читать далее»
            'read_start'           => $UNIT['read_start'],
            'read_end'             => $UNIT['read_end'],
                                   
            'comments_count_start' => $UNIT['comments_count_start'], // колво комментариев
            'comments_count_end'   => $UNIT['comments_count_start'],
                                   
            'placehold'            => $UNIT['placehold'], // заглушка если нет миниатюры
            'placehold_path'       => $UNIT['placehold_path'],
            'placehold_pattern'    => $UNIT['placehold_pattern'],
            'placehold_file'       => $UNIT['placehold_file'],
            'placehold_data_bg'    => $UNIT['placehold_data_bg'],
                                   
            'pagination_start'     => $UNIT['pagination_start'], // пагинация
            'pagination_end'       => $UNIT['pagination_end'],
            'pagination_in_block'  => $UNIT['pagination_in_block'], // пагинация внутри или вне общего блока
                                   
            'exclude_page_add'     => $UNIT['exclude_page_add'], // добавлять полученные страницы в исключенные
                                   
            'UNIT'                 => $UNIT, // весь входящий юнит
        ));
    }
    
    $out = ob_get_flush();
    
    if ($out)
        mso_add_cache($UNIT_UID, $out, $home_cache_time * 60);
    else
        mso_add_cache($UNIT_UID, '', $home_cache_time * 60);
}

# end of file
