<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: logo-block.php

	Название: «Лого, блок»
	
	Для блока используется ушка logo-block.
		
	Расположение: header
	
	CSS-стили: 
			>	@import url('components/logo-block.less');
		
	PHP-связи: 
			>	if ($fn = mso_fe('components/logo-block/logo-block.php')) require($fn);
			
			
*/

$pt = new Page_out; // подготавливаем объект для вывода

// если в опции явно указан адрес лого, то берем его
$logo = trim(mso_get_option('default_header_logo_custom', 'templates', false));

if (!$logo)
{	
	$logo = getinfo('stylesheet_url') . 'images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');
}

$logo = '<img src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '">';

if (!is_type('home')) $logo = $pt->link(getinfo('siteurl'), $logo);

// вывод
$pt->div_start('logo-block', 'wrap');

	$pt->div_start('r1');
		$pt->html($logo);
	$pt->div_end('r1');
	
	$pt->div_start('r2');
		if (function_exists('ushka')) echo ushka('logo-block');
	$pt->div_end('r2');
	
	$pt->clearfix();

$pt->div_end('logo-block', 'wrap');

# end file