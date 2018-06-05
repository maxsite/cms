<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	произвольный блок в подвале
	
*/

// условие вывода компонента
// php-условие как в виджетах
if ($rules = trim(mso_get_option('footer_rules_output', getinfo('template'), '')))
{
	$rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');
	if ($rules_result === false) $rules_result = 1; 
	if ($rules_result !== 1) return; // выход
}

$footer_block1 = mso_get_option('footer_block1', getinfo('template'), '<div class="bg-gray800 t-white pad20 t-center">
Блок подвала
</div>'); 

eval(mso_tmpl_prepare($footer_block1)); 

# end of file