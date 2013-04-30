<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	Файл: date-menu2.php

	Название: «Дата и меню2»
	
	Расположение: header
	
	Схематичный вид: 
		дата | Меню 2
		

	PHP-связи: 
			>	if ($fn = mso_fe('components/date-menu2/date-menu2.php')) require($fn);
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
	
	$pt->clearfix();

$pt->div_end('date-menu2', 'wrap');

# end file
