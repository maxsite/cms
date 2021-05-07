<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$p->format('edit', 'Edit page', '<span class="im-edit mar20-l t-gray600 t90 link-no-color">', '</span>');
$p->format('title', '<h1 class="t-gray700 mar20-t mar10-b t220">', '</h1>', false);
$p->format('cat', ', ', '<span class="im-bookmark t-gray600 link-no-color t90" title="' . tf('Рубрика записи') . '">', '</span>');
$p->format('tag', ' / ', '<span class="im-tag mar20-l t-gray600 link-no-color t90" title="' . tf('Метка записи') . '">', '</span>');
$p->format('author', '<span class="im-user t-gray600 link-no-color t90" title="' . tf('Автор') . '">', '</span>');
$p->format('comments_count', '<span class="im-comments mar20-l t-gray600 link-no-color t90">' . tf('Комментарии') . ': ', '</span>');
$p->format('view_count', '<span class="im-chart-bar mar20-l t-gray600 link-no-color t90">' . tf('Просмотров') . ': ', '</span>');

$date_d = mso_date_convert('j', $p->val('page_date_publish'));
$date_m = mso_date_convert('F', $p->val('page_date_publish'), true, false, 'января февраля марта апреля мая июня июля августа сентября октября ноября декабря');
$date_y = mso_date_convert('Y', $p->val('page_date_publish'));

$p->html('<header class="mar30-tb flex">');
    $p->div_start('w80');
    $p->line('[title]');
    $p->line('[cat][tag][edit]');
    $p->line('<div>[author][comments_count][view_count]</div>');
    $p->div_end('');

    $p->div_start('flex-grow0 t-center t-gray600 bor1 bor-gray200 bor-solid-tb pad20-rl pad10-tb flex-as-center');
        $p->html('<div class="t200">' . $date_d . '</div>');
        $p->html('<div class="t120">' . $date_m . '</div>');
        $p->html('<div class="t130">' . $date_y . '</div>');
    $p->div_end('');
$p->html('</header>');

# end of file
