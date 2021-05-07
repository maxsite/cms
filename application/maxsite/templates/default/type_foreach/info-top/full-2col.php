<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// вывод контента для всех full-записей (главная, рубрики и т.п.)

if (is_type('page')) {
    require 'page/page.php';

    return;
}

$_width = mso_get_val('thumb-width1', 640); //ширина
$_height = mso_get_val('thumb-height1', 480); //высота

if ($thumb = thumb_generate($p->meta_val('image_for_page'), $_width, $_height)) {
    $img = '<img class="w100" src="' . $thumb . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';
} else {
    if ($thumb_placehold = mso_get_val('thumb-placehold', false))
        $img = '<img class="w100" src="' . $thumb_placehold . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';
    else
        $img = '<img class="w100" src="' . mso_holder($_width, $_height, '', '#eeeeee', '#444444') . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';
}

$p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '">' . $img . '<div></div></a>';

$p->format('title', '<h2 class="t130 mar20-t b-inline">', '</h2>', true);
$p->format('cat', ', ', '<span class="im-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="b-inline b-right">', '</time>');
$p->format('read', '<span class="t90 bg-primary600 pad10-rl pad5-tb t-primary50 hover-bg-primary700 trans05-all">' . tf('Читать') . ' <i class="im-angle-right mar5-l icon0"></i></span>', '', '');

$p->div_start('mar30-t');
$p->line('[thumb]');

$p->html('<header class="mar10-b mso-clearfix">');
$p->line('[title]');
$p->div_start('t-gray500 mar10-b t90 link-no-color');
$p->line('[cat][date]');
$p->div_end('');
$p->html('</header>');

// mar50-b — отступ для корректного размещения READ (у него абс. позиционирование)
$p->content_chars(220, ' [...]', '<p class="mar15-t mar50-b hover-no-underline t90">', '</p>');

$p->line('<div class="hover-no-underline pos-absolute pos0-b pos0-r pad5-tb">[read]</div>');
$p->div_end('');

mso_set_val('my-page-content-full', false); // отключить вывод дальнейшего текста

# end of file
