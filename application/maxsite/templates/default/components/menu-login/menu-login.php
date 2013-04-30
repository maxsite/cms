<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: menu-block.php

	Название: меню и логин
	
	Описание: Слева меню, справа логин-форма
	
	Расположение: header
	
	Схематичный вид: 
		Меню | Меню      логин
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/menu-login.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/menu-login/menu-login.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-login', 'wrap');
	
	$pt->div_start('r1');
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r1');
	
	$pt->div_start('r2');
		if ($fn = mso_fe('components/_login/_login.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->clearfix();

$pt->div_end('menu-login', 'wrap');

# end file