<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', '<i class="im-edit t-gray600 hover-t-gray950" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');
$p->format('title', '<h1 class="t-gray800 t220 mar10-b">', '</h1>', false);
$p->format('date', 'j F Y г.', '<time class="mar10-l b-right im-calendar" datetime="[page_date_publish_iso]">', '</time>');
$p->format('cat', ', ', '<span class="im-bookmark mar20-r" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('view_count', '<span class="im-chart-bar mar20-r">' . tf('Просмотров') . ': ', '</span>');
$p->format('comments_count', '<span class="im-comments mar20-r">' . tf('Комментарии') . ': ', '</span>');
$p->format('tag', ' / ', '<div class="im-tags mar5-t" title="' . tf('Метка записи') . '">', '</div>');

$p->html('<header class="mar30-t mar20-b">');
    $p->line('[edit][title]');
    $p->div_start('t-gray600 t90 b-clearfix');
    $p->line('[cat][view_count][comments_count][date]');
    $p->line('[tag]');
    $p->div_end('');
$p->html('</header>');

# end of file
