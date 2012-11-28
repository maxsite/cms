<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: menu-search.php

	Название: «Меню и поиск»
	
	Описание: Слева меню, справа форма поиска
	
	Расположение: header
	
	Схематичный вид: 
		Меню | Меню      [Поиск]
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/menu-search.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/menu-search/menu-search.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-search', 'wrap');

	$pt->div_start('r1');
		if ($fn = mso_fe('components/menu/menu.php')) require($fn);
	$pt->div_end('r1');
	
	$pt->div_start('r2');	
		if ($fn = mso_fe('components/_search/_search.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->clearfix();

$pt->div_end('menu-search', 'wrap');
