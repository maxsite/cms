<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Описание: Название и описание сайта слева. Справа иконки соцсетей.
	Схематичный вид: 
		(лого) Название сайта				(иконки)
		       Описание					
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
$pt->div_start('logo-ns-descr-icons clearfix', 'wrap');

	$pt->div_start('r1');
		$pt->html($logo);
	$pt->div_end('r1');
	
	$pt->div_start('r2');
		$pt->div($pt->name_site(), 'name_site');
		$pt->div(getinfo('description_site'), 'description_site');
	$pt->div_end('r2');
	
	$pt->div_start('r3');	
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	$pt->div_end('r3');
	
	// $pt->clearfix();

$pt->div_end('logo-ns-descr-icons', 'wrap');

# end file