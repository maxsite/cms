<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

// если не указан файл, выходим
if (!$ff = trim(mso_get_option('file2_file', getinfo('template'), ''))) return;

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('file2_rules_output', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

if ($fn = mso_fe($ff)) {
	if (mso_get_option('file2_use_tmpl', getinfo('template'), ''))
		eval(mso_tmpl($fn));
	else
		require $fn;
}

# end of file
