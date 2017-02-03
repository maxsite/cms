<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод только заголовка записи
*/

$p->format('title', '<h1 class="t-gray700 bor-double-b bor3px bor-gray300 pad5-b">', '</h1>', !is_type('page'));

$p->format('edit', '<i class="i-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');

$p->html(NR . '<header class="mar20-b">');
	$p->line('[edit][title]');
$p->html('</header>');

# end of file