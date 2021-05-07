<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 */

$component = basename(dirname(__FILE__));

// mso_delete_option_mask($component . '-', getinfo('template'));

// условие вывода компонента | php-условие как в виджетах
if ($rules = trim(mso_get_option($component . '-rules', getinfo('template'), ''))) {
    $rules_result = eval('return ( ' . $rules . ' ) ? 1 : 0;');

    if ($rules_result === false) $rules_result = 1;
    if ($rules_result !== 1) return;
}

$optionsINI = mso_get_defoptions_from_ini(__DIR__ . '/options.ini');

$modules = trim(mso_get_option($component . '-modules', '', '', $optionsINI));
$modules = str_replace("\r", '', $modules); // win-dos
$modules = explode("\n", $modules);

if (!$modules) return;

$dir = trim(mso_get_option($component . '-dir', '', '', $optionsINI));

if ($dir) {
    $dir .= '/';
    mso_set_val('mso_units_out_modulesDir', $dir);
} else {
    $dir = 'modules/';
}

// в модуле можно указать параметры юнита
// berry1 || block = ca2.php ^ color1 = t-red200 ^ color2 = bg-red700

foreach ($modules as $m) {
	mso_units_out('@module ' . $m);
}

# end of file
