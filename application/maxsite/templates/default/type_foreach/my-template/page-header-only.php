<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод только заголовка записи
*/

$p->format('edit', '<i class="i-edit t-gray600 hover-t-black" title="Edit page"></i>', '<div class="b-right mar10-t">', '</div>');

$p->format('title', '<h1 class="t-gray800 mar10-t t220">', '</h1>', false);

$p->html('<header class="mar20-b">');
	$p->line('[edit][title]');
$p->html('</header>');


# end of file