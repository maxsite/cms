<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
    (c) MaxSite CMS, http://max-3000.com/
*/

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('file1_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

if ($fn = mso_fe(mso_get_option('file1_file', getinfo('template'), ''))) 
{
	if (mso_get_option('file1_use_tmpl', getinfo('template'), ''))
		eval(mso_tmpl($fn));
	else
		require $fn;
}

# end of file