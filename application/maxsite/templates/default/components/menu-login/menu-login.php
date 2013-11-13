<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: меню и логин
	Описание: Слева меню, справа логин-форма
	Схематичный вид: 
		Меню | Меню      логин
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