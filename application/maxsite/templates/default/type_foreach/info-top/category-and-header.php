<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод рубрик перед заголовком записи
*/

$p->format('edit', 'Edit', '<div class="b-right bg-yellow pad5 t80">', '</div>');
$p->format('cat', ' &gt; ', '<div>', '</div>');

$p->html(NR . '<header class="mar20-b">');

	$p->div_start('info info-top');
		$p->line('[cat]');
	$p->div_end('info info-top');

	$p->line('[edit][title]');
	
$p->html('</header>');

# end file