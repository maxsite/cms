<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
	Файл: menu-icons.php

	Название: «Меню и иконки»
	
	Описание: Слева меню, справа социконки
	
	Расположение: header
	
	Схематичный вид: 
		Меню | Меню      (иконки)
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/menu-icons.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/menu-icons/menu-icons.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-icons', 'wrap');

	$pt->div_start('r1');
		if ($fn = mso_fe('components/menu/menu.php')) require($fn);
	$pt->div_end('r1');
	
	$pt->div_start('r2');	
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->clearfix();

$pt->div_end('menu-icons', 'wrap');

# end file
