<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Перелинковка страниц'),
	'description' => t('Плагин для внутренней прелинковки страниц путем анализа наиболее часто встречающихся слов.'),
	'version' => '1.4',
	'author' => 'Максим',
	'editors' => '<a href="http://wave.fantregata.com/page/work-for-maxsite">Wave</a>',
	'plugin_url' => '',
	'author_url' => 'https://maxsite.org/',
	'group' => 'seo',
	'options_url' => getinfo('site_admin_url') . 'perelinks',
);

# end of file