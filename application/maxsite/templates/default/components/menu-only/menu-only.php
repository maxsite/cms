<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: меню
	Описание: Одиночное меню, только в виде отдельного блока
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-only', 'wrap');
	
	if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);

$pt->div_end('menu-only', 'wrap');

# end file