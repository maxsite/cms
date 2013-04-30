<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: menu-only.php

	Название: меню
	
	Описание: Одиночное меню, только в виде отдельного блока
	
	CSS-стили: 
		var_style.less:
			>	@import url('components/menu-only.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/menu-only/menu-only.php')) require($fn);
*/


$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('menu-only', 'wrap');
	
	if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);

$pt->div_end('menu-only', 'wrap');

# end file