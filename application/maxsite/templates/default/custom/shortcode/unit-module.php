<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
    Подключение модуля юнитов
    
    [module]модуль[/module]
    [module]pages/page1[/module]

*/

mso_shortcode_add('module', 'my_shortcode_module');

function my_shortcode_module($attr)
{
    if (!isset($attr[2]) or !$attr[2]) return; // не указан модуль

    ob_start();
    mso_units_out('@module ' . $attr[2]);
    $out = ob_get_contents();   
    ob_end_clean();

    return $out;
}

# end of file