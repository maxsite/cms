<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: search-icons.php

	Название: «Поиск и иконки»
	
	Схематичный вид: 
		[Поиск] (иконки)
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/search-icons.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/search-icons/search-icons.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

// вывод
$pt->div_start('search-icons', 'wrap');

	$pt->div_start('r1');	
		if ($fn = mso_fe('components/_search/_search.php')) require($fn);
	$pt->div_end('r1');
	
	$pt->div_start('r2');
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	$pt->div_end('r3');

	$pt->clearfix();

$pt->div_end('search-icons', 'wrap');

# end file