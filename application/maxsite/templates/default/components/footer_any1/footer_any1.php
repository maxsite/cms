<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
    (c) MaxSite CMS, http://max-3000.com/
*/

if (!$opt = mso_get_option('footer_any1_block', getinfo('template'), '')) return;

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('footer_any1_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$CI = & get_instance();	

$copy_maxsite = sprintf( tf('Работает на <a href="//max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}'), $CI->db->query_count) . '<!--global_cache_footer--> | ';

if (is_login())
	$login = '<a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> | '
		. '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
else
	$login = '<a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';

// используем php-шаблонизатор
eval(mso_tmpl_prepare($opt));

# end of file