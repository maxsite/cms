<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: ns-menu-block.php

	Название: «Название сайта, меню и блок»
	
	Описание: Слева название сайта, меню, справа произвольный блок
	
	Расположение: header
	
	Схематичный вид: 
		Название  Меню | Меню      Блок
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/ns-menu-block.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/ns-menu-block/ns-menu-block.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

$name_site = mso_get_option('ns-menu-block-name_site', 'templates', getinfo('name_site'));

$block = mso_get_option('ns-menu-block-block', 'templates', '');

if (!is_type('home')) $name_site = $pt->link(getinfo('siteurl'), $name_site);

// вывод
$pt->div_start('ns-menu-block', 'wrap');
	
	$pt->div($name_site, 'r1');	
		
	$pt->div_start('r2');
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->div($block, 'r3');	
	
	$pt->clearfix();

$pt->div_end('ns-menu-block', 'wrap');

# end file