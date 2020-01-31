<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

// префикс опций компонента
$prefix = 'footer-copy-stat_';

// условие вывода компонента - php-условие как в виджетах
if ($rules = trim(mso_get_option($prefix . 'rules_output', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$text = mso_get_option($prefix . 'text', getinfo('template'), '&copy; {NAME_SITE}, {YEAR}. Работает на <a href=https://max-3000.com/>MaxSite CMS</a> {STATISTIC} {LOGIN}');

$container_css = mso_get_option($prefix . 'container_css', getinfo('template'), 'bg-gray900 t-white t90 pad20-tb links-no-color hide-print');

$delim = mso_get_option($prefix . 'delim', getinfo('template'), ' | ');

if (strpos($text, '{STATISTIC}') !== false) {
	$CI = &get_instance();
	$statistic =  $delim . sprintf('Время: {elapsed_time} ' . $delim . ' SQL: %s ' . $delim . ' Память: {memory_usage}', $CI->db->query_count) . '<!--global_cache_footer--> ';
} else
	$statistic = '';

if (strpos($text, '{LOGIN}') !== false) {
	if (is_login())
		$login = $delim . ' <a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> ' . $delim
			. '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
	else
		$login = $delim . ' <a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';
} else
	$login = '';


$text = str_replace('{NAME_SITE}', getinfo('name_site'), $text);
$text = str_replace('{YEAR}', date('Y'), $text);
$text = str_replace('{STATISTIC}', $statistic, $text);
$text = str_replace('{LOGIN}', $login, $text);

echo '<div class="layout-center-wrap ' . $container_css . '"><div class="layout-wrap">' . $text . '</div></div>';

# end of file
