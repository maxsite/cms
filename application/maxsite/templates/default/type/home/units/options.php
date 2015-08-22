<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* вывод произвольной опции

[unit]
file = options.php
key = ключ
type = тип
default = значение по-умолчанию
[/unit]

*/




if (!isset($UNIT['key']) or !trim($UNIT['key'])) return; // нет ключа, выходим

$key = trim($UNIT['key']);
$type = (isset($UNIT['type'])) ? trim($UNIT['type']) : 'template';
$default = (isset($UNIT['default'])) ? trim($UNIT['default']) : '';
 
echo mso_get_option($key, $type, $default);

# end file