<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Название сайта, меню и блок»
	Описание: Слева название сайта, меню, справа произвольный блок
	Схематичный вид: 
		Название  Меню | Меню      Блок
*/

$pt = new Page_out; // подготавливаем объект для вывода

$name_site = mso_get_option('ns-menu-block-name_site', 'templates', getinfo('name_site'));

$block = mso_get_option('ns-menu-block-block', 'templates', '');

if (!is_type('home')) $name_site = $pt->link(getinfo('siteurl'), $name_site);

// цвет в опции
if ($style_ns = mso_get_option('ns-menu-block-name_site-color', 'templates', ''))
{
	$style_ns = 'color: #' . $style_ns;
}

// цвет в опции
if ($style_bl = mso_get_option('ns-menu-block-block-color', 'templates', ''))
{
	$style_bl = 'color: #' . $style_bl;
}

// вывод
$pt->div_start('ns-menu-block', 'wrap');
	
	$pt->div($name_site, 'r1', 'div', $style_ns);	
	
	// ns-menu-block-name_site-color
	
	$pt->div_start('r2');
		if ($fn = mso_fe('components/_menu/_menu.php')) require($fn);
	$pt->div_end('r2');
	
	$pt->div($block, 'r3', 'div', $style_bl);	
	
$pt->div_end('ns-menu-block', 'wrap');

# end file