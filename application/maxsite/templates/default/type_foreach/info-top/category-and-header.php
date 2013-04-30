<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод рубрик перед заголовком записи
*/

$p->format('edit', 'Edit', '<div class="right bg-yellow padding5 d-inline-block">', '</div>');
$p->format('cat', ' / ', '<div>', '</div>');

$p->html(NR . '<header>');

	$p->div_start('info info-top');
		$p->line('[cat]');
	$p->div_end('info info-top');

	$p->line('[edit][title]');
	
$p->html('</header>');

# end file