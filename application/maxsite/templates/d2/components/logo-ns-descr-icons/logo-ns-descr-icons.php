<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	Файл: ns-descr-icons.php

	Описание: Навазние и описание сайта слева. Справа иконки соцсетей.
	
	Расположение: header
	
	Схематичный вид: 
		(лого) Название сайта				(иконки)
		       Описание					
		
	CSS-стили: 
		components/ns-descr-icons.less
	
		var_style.less:
			>	@import url('components/logo-ns-descr-icons.less');
		
	PHP-связи: 
		custom/header_components.php
			>	require(getinfo('template_dir') . 'components/logo-ns-descr-icons/logo-ns-descr-icons.php');
*/

$_p = new Page_out; // подготавливаем объект для вывода

$logo = getinfo('stylesheet_url') . 'images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');

$logo = '<img src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '">';


// вывод
$_p->div_start('logo-ns-descr-icons', 'wrap');

	$_p->div_start('r1');
		$_p->html($logo);
	$_p->div_end('r1');
	
	$_p->div_start('r2');
		$_p->div($_p->name_site(), 'name_site');
		$_p->div(getinfo('description_site'), 'description_site');
	$_p->div_end('r2');
	
	$_p->div_start('r3');	
		if ($fn = mso_fe('components/_social/_social.php')) require($fn);
	$_p->div_end('r3');
	
	$_p->clearfix();

$_p->div_end('logo-ns-descr-icons', 'wrap');
