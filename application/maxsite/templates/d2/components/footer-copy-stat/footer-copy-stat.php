<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
(c) http://maxsite.org/

	Файл: footer-copy-stat.php

	Расположение: footer
	
	CSS-стили: 
		components/footer-copy-stat.less
	
		var_style.less:
			>	@import url('components/footer-copy-stat.less');
		
	PHP-связи: 
		custom/header_components.php
			>	require(getinfo('template_dir') . 'components/footer-copy-stat.php');
*/

$p = new Page_out;

$p->div_start('footer-copy-stat', 'wrap');

	$p->div('&copy; ' . getinfo('name_site') . ', ' . date('Y'), 'copyright');
	
	$p->div_start('statistic');
		
		$CI = & get_instance();	
		
		echo sprintf(
					tf('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Шаблон: <a href="http://maxsite.org/">MAX</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}')
						, $CI->db->query_count) 
				. '<!--global_cache_footer--> | ';
	
		if (is_login())
		{
			echo $p->link(getinfo('siteurl') . 'admin', tf('Управление'))
				. ' | '
				. $p->link(getinfo('siteurl') . 'logout', tf('Выйти'));
		}
		else
		{
			echo $p->link(getinfo('siteurl') . 'login', tf('Вход'));
		}
		
	$p->div_end('statistic');

$p->div_end('footer-copy-stat', 'wrap');
