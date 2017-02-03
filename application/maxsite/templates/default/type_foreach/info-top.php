<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	info-top файл
	типовой вывод этого шаблона
*/

$p->format('edit', '<i class="i-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');

$p->format('title', '<h1 class="t-gray700 bor-double-b bor3px bor-gray300 pad5-b">', '</h1>', !is_type('page'));

$p->format('date', 'j F Y г.', '<time datetime="[page_date_publish_iso]" class="i-calendar">', '</time>');

$p->format('view_count', '<span class="i-eye mar15-l">' . tf('Просмотров') . ': ', '</span>');

$p->format('comments_count', '<span class="i-comment mar15-l">' . tf('Комментарии') . ': ', '</span>');

$p->format('cat', '<i class="i-bookmark-o mar10-l"></i>', '<br><span class="i-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');

$p->format('tag', '<i class="i-tag mar10-l"></i>', '<br><span class="i-tags links-no-color" title="' . tf('Метка записи') . '">', '</span>');

$p->html(NR . '<header class="mar20-b">');

	$p->line('[edit][title]');
	
	$p->div_start('info info-top t-gray600 t90');
		$p->line('[date][view_count][comments_count][cat][tag]');
	$p->div_end('info info-top');

$p->html('</header>');

# end of file