<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод только заголовка записи
*/

$p->format('edit', 'Edit', '<div class="right bg-yellow padding5 d-inline-block">', '</div>');

$p->html(NR . '<header>');
	$p->line('[edit][title]');
$p->html('</header>');

# end file