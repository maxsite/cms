<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

$component = basename(dirname(__FILE__));

# mso_delete_option_mask($component . '-', getinfo('template'));

// условие вывода компонента | php-условие как в виджетах
if ($rules = trim(mso_get_option($component . '-rules', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

$optionsINI = mso_get_defoptions_from_ini(__DIR__ . '/options.ini');

$text = mso_get_option($component . '-text', '', '', $optionsINI);
$container = mso_get_option($component . '-container', '', '', $optionsINI);
$delim = mso_get_option($component . '-delim', '', '', $optionsINI);

$statistic = $login = '';

if (strpos($text, '[STATISTIC]') !== false) {
	$CI = &get_instance();
	$statistic =  $delim . sprintf('Время: {elapsed_time} ' . $delim . ' SQL: %s ' . $delim . ' Память: {memory_usage}', $CI->db->query_count) . '<!--global_cache_footer--> ';
}

if (strpos($text, '[LOGIN]') !== false) {
	if (is_login())
		$login = $delim . ' <a href="' . getinfo('siteurl') . 'admin">' . tf('Управление') . '</a> ' . $delim . '<a href="' . getinfo('siteurl') . 'logout">' . tf('Выйти') . '</a>';
	else
		$login = $delim . ' <a href="' . getinfo('siteurl') . 'login">' . tf('Вход') . '</a>';
}

$text = str_replace('[NAME_SITE]', getinfo('name_site'), $text);
$text = str_replace('[YEAR]', date('Y'), $text);
$text = str_replace('[STATISTIC]', $statistic, $text);
$text = str_replace('[LOGIN]', $login, $text);

echo '<div class="layout-center-wrap ' . $container . '"><div class="layout-wrap">' . $text . '</div></div>';

# end of file
