<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Дата и меню2»
	Расположение: header
		дата | Меню 2
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('date-menu2', 'wrap');

	$pt->div_start('r1');
		echo mso_date_convert('D, j F Y г.', date('Y-m-d H:i:s'), true, 'Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье', 'января февраля марта апреля мая июня июля августа сентября октября ноября декабря');
	$pt->div_end('r1');
	
	$pt->div_start('r2');	
		if ($fn = mso_fe('components/menu2/menu2.php')) require($fn);
	$pt->div_end('r2');

$pt->div_end('date-menu2', 'wrap');

# end file