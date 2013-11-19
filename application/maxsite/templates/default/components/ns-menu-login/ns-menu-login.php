<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Название сайта, меню и логин
	Описание: Слева название сайта, меню, справа логин-форма
	Схематичный вид: 
		Название  Меню | Меню      логин
*/

$pt = new Page_out; // подготавливаем объект для вывода

$name_site = mso_get_option('ns-menu-login-name_site', 'templates', getinfo('name_site'));

if (!is_type('home')) $name_site = $pt->link(getinfo('siteurl'), $name_site);

// вывод
$pt->div_start('ns-menu-login', 'wrap');
	
	$pt->div($name_site, 'r1');	
		
	$pt->div_start('r2');
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->div_start('r3');
		if ($fn = mso_fe('components/_login/_login.php')) require($fn);
	$pt->div_end('r3');
	
$pt->div_end('ns-menu-login', 'wrap');

# end file