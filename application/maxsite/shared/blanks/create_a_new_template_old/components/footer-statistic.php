<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>

	<div class="footer-statistic margin-left10"><?php 
		
		$CI = & get_instance();	
		echo sprintf(
		tf('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}')
		, $CI->db->query_count) . '<!--global_cache_footer-->';
	
		if (is_login())
			echo ' | <a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') 
					. '</a> | <a href="' . getinfo('siteurl') . 'logout' . '">' . tf('Выйти') . '</a>';
		else
			echo ' | <a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';

	?></div>
