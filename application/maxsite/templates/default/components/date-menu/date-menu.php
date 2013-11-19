<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Дата и меню»
	Схематичный вид: 
		дата | Меню 
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('date-menu', 'wrap');

	$pt->div_start('r1');
		echo mso_date_convert('D, j F Y г.', date('Y-m-d H:i:s'), true, 'Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье', 'января февраля марта апреля мая июня июля августа сентября октября ноября декабря');
	$pt->div_end('r1');
	
	$pt->div_start('r2');	
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r2');
	
$pt->div_end('date-menu', 'wrap');

# end file