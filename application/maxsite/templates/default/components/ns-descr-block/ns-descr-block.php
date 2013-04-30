<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: ns-descr-block.php

	Название: «Название сайта, описание, блок»
	
	Схематичный вид: 
		Название  Блок
		
	CSS-стили: 
		var_style.less:
			>	@import url('components/ns-descr-block.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/ns-descr-block/ns-descr-block.php')) require($fn);
*/

$pt = new Page_out; // подготавливаем объект для вывода

$block = mso_get_option('ns-descr-block-block', 'templates', '');

// вывод
$pt->div_start('ns-descr-block', 'wrap');
	
	$pt->div_start('r1');
		$pt->div($pt->name_site(), 'name_site');
		$pt->div(getinfo('description_site'), 'description_site');
	$pt->div_end('r1');
		
	$pt->div($block, 'r2');	
	
	$pt->clearfix();

$pt->div_end('ns-descr-block', 'wrap');

# end file