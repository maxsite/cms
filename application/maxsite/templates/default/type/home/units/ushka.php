<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* вывод ушки

[unit]
file = ushka.php
ushka = название ушки
[/unit]

*/

if ( isset($UNIT['ushka']) and trim($UNIT['ushka']) and function_exists('ushka') ) echo ushka(trim($UNIT['ushka']));


# end file