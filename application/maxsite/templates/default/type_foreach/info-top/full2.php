<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if (is_type('page')) {
    require 'page.php';

    return;
}

$_width = mso_get_val('thumb-width2', 800); //ширина
$_height = mso_get_val('thumb-height2', 600); //высота

if ($thumb = thumb_generate($p->meta_val('image_for_page'), $_width, $_height)) {
    $p->thumb = '<a class="my-hover-img" href="' . $p->page_url() . '"><img class="w100" src="' . $thumb . '" alt="' . htmlspecialchars($p->val('page_title')) . '"><div></div></a>';
}

$p->format('title', '<h1 class="t180 mar15-tb">', '</h1>', true);
$p->format('cat', ' / ', '<span class="im-bookmark t-gray700 t90" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="b-inline b-right  t90">', '</time>');
$p->format('edit', '<i class="im-edit" title="">Edit page</i>', '<div>', '</div>');

$p->div_start('bor4 bor-dotted-b bor-gray300 pad30-b mar30-t mar20-b');
$p->line('[thumb]');
$p->line('[title]');
$p->line('<div class="mar20-b">[cat][date]</div>');
$p->content_chars(500, '', '<p class="mar10-tb">', '[...]</p>');
// $p->line('[edit]');
$p->div_end('');

mso_set_val('my-page-content-full', false); // отключить вывод дальнейшего текста

# end of file
