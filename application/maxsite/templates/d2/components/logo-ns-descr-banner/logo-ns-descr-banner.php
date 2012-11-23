<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: logo-ns-descr-banner.php

	Название: «Лого, название, описание и баннер в шапке»
	
	Для баннера используется ушка logo-banner.
		
	Расположение: header
	
	Схематичный вид: 
		(лого) Название сайта				(баннер)
		       Описание		

	CSS-стили: 
			>	@import url('components/logo-ns-descr-banner.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/logo-ns-descr-banner/logo-ns-descr-banner.php')) require($fn);
			
			
*/

$pt = new Page_out; // подготавливаем объект для вывода

$logo = getinfo('stylesheet_url') . 'images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');

$logo = '<img src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '">';

// вывод
$pt->div_start('logo-ns-descr-banner', 'wrap');

	$pt->div_start('r1');
		$pt->html($logo);
	$pt->div_end('r1');
	
	$pt->div_start('r2');
		$pt->div($pt->name_site(), 'name_site');
		$pt->div(getinfo('description_site'), 'description_site');
	$pt->div_end('r2');
	
	$pt->div_start('r3');
		if (function_exists('ushka')) echo ushka('logo-banner');
	$pt->div_end('r3');
	
	$pt->clearfix();

$pt->div_end('logo-ns-descr-banner', 'wrap');

# end file