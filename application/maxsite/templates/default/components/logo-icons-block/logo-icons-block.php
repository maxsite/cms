<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) MaxSite CMS, http://max-3000.com/

	Название: «Лого, блок, социконки, блок»
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

$block = mso_get_option('logo-icons-block', 'templates', '');
$block0 = mso_get_option('logo-icons-block0', 'templates', '');

// вывод
$pt->div_start('logo-icons-block', 'wrap');

	$pt->div_start('r1');
		$pt->html($logo);
	$pt->div_end('r1');
	
	$pt->div_start('r2');

			$pt->div($block0, 'r3');
	
		$pt->div_start('r4');
			if ($fn = mso_fe('components/_social/_social.php')) require($fn);
			$pt->div($block, 'block');
		$pt->div_end('r4');	
		
	$pt->div_end('r2');	

$pt->div_end('logo-icons-block', 'wrap');

# end file