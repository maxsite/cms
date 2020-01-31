<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

// если нет текста, то выходим
if (!$opt = mso_get_option('any2_block', getinfo('template'), '')) return;

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('any2_rules_output', getinfo('template'), ''))) {
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	
	if ($rules_result === false) $rules_result = 1;
	if ($rules_result !== 1) return;
}

// используем php-шаблонизатор
eval(mso_tmpl_prepare($opt));

# end of file
