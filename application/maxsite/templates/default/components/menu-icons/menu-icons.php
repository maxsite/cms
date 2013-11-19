<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Меню и иконки»
	Описание: Слева меню, справа социконки
	Схематичный вид: 
		Меню | Меню      (иконки)
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-icons', 'wrap');

	$pt->div_start('r1');
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r1');
	
	$pt->div_start('r2');	
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	$pt->div_end('r2');
	
$pt->div_end('menu-icons', 'wrap');

# end file