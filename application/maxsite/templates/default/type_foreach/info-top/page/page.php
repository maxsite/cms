<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', '<i class="fas fa-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');
$p->format('title', '<h1 class="t-gray800 mar10-t t220">', '</h1>', false);
$p->format('cat', ', ', '<span class="far fa-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('date', 'j F Y г.', '<time class="mar10-l b-right" datetime="[page_date_publish_iso]">', '</time>');

$p->html('<header class="mar30-t mar20-b">');
    $p->line('[edit][title]');
    $p->div_start('t-gray600 t90 mso-clearfix');
        $p->line('[cat][date]');
    $p->div_end('');
$p->html('</header>');

// учитываем опцию вывода записи на странице — выводим миниатюру в начале записи на всю ширину
$image_for_page_out = $p->meta_val('image_for_page_out');

if ($image_for_page_out != 'no-page' and $image_for_page_out != 'no') {

    $_width = 810;
    $_height = 535;

    if ($thumb = thumb_generate($p->meta_val('image_for_page'), $_width, $_height)) {
        $p->thumb = '<img class="w100 mar20-b" src="' . $thumb . '" alt="' . htmlspecialchars($p->val('page_title')) . '">';
        $p->block($p->thumb, '<div>', '</div>');
    }

    mso_set_val('show_thumb_type_page', false); // отключить в тексте миниатюры
}

# end of file
