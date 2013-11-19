<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Название сайта, описание, блок»
	Схематичный вид: 
		Название  Блок
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
	
$pt->div_end('ns-descr-block', 'wrap');

# end file