<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	Вывод в подвале копирайтов и статистики.
*/

$CI = & get_instance();	

$copy_maxsite = sprintf( tf('Работает на <a href="//max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}'), $CI->db->query_count) . '<!--global_cache_footer--> | ';

if (is_login())
	$login = '<a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> | '
		. '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
else
	$login = '<a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';

?>
<div class="layout-center-wrap bg-gray900 t-white t80 links-no-color"><div class="layout-wrap pad10">
	<div class="">&copy; <?= getinfo('name_site') ?>, <?= date('Y') ?></div>
	<div class="links-no-color hover-no-color"><?= $copy_maxsite . $login ?></div>
</div></div>
