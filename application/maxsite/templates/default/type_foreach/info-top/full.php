<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (is_type('page')) {
    require 'page.php';

    return;
}
$_width = mso_get_val('thumb-width1', 640); //ширина
$_height = mso_get_val('thumb-height1', 480); //высота

if ($thumb = thumb_generate($p->meta_val('image_for_page'), $_width, $_height)) {
    $p->thumb = '<img class="w100" src="' . $thumb . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';

    $p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '">' . $p->thumb . '<div></div></a>';
} else {
    if ($thumb_placehold = mso_get_val('thumb-placehold', false))
        $p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '"><img class="w100" src="' . $thumb_placehold . '" alt=""><div></div></a>';
    else
        $p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '"><img class="w100" src="' . mso_holder($_width, $_height, '', '#eeeeee') . '" alt=""><div></div></a>';
}

$p->format('title', '<h1 class="mar0 t150 small-caps b-inline">', '</h1>', true);
$p->format('cat', ' / ', '<span class="im-folder t90" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('tag', ' ', '<div class="mar5-t"><span class="t90">', '</span></div>');
$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="b-inline b-right  t90">', '</time>');

// можно сделать верхний отступ
if (is_type('home') and $p->num == 1) echo '<div class="pad20-t"></div>';

if (!$p->last)
    $p->div_start('flex flex-wrap-phone bor1 bor-dotted-b bor-gray400 pad30-b mar30-t');
else
    $p->div_start('flex flex-wrap-phone pad30-b mar30-t mar50-b');

$p->div_start('w30 w200px-min w100-phone pad5-t pad20-b-phone');
$p->line('[thumb]');
$p->div_end('');

$p->div_start('w70 w100-phone pad30-l pad0-phone');
$p->line('[title]');
$p->line('<div class="mar15-t mso-clearfix">[cat][date]</div>');
$p->content_chars(220, ' [...]', '<p class="mar15-tb hover-no-underline">', '</p>');

// $p->line('[tag]');
$p->div_end('');

$p->div_end('');

mso_set_val('my-page-content-full', false); // отключить вывод дальнейшего текста

# end of file
