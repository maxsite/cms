<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод только заголовка записи
*/

$p->format('edit', 'Edit', '<div class="b-right bg-yellow pad5 t80">', '</div>');

$p->html(NR . '<header class="mar20-b">');
	$p->line('[edit][title]');
$p->html('</header>');

# end file