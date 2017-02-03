<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод рубрик перед заголовком записи
*/

$p->format('edit', '<i class="i-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');

$p->format('title', '<h1 class="t-gray700 bor-double-b bor3px bor-gray300 pad5-b">', '</h1>', !is_type('page'));

$p->format('cat', '<i class="i-bookmark-o mar10-l"></i>', '<span class="i-bookmark" title="' . tf('Рубрика записи') . '">', '</span>');

$p->html(NR . '<header class="mar20-b">');

	$p->line('[edit][title]');
	
	$p->div_start('info info-top t-gray600 t90');
		$p->line('[cat]');
	$p->div_end('info info-top');

$p->html('</header>');


# end of file