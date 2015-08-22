<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* вывод компонента

[unit]
file = component.php
component = компонент
[/unit]

*/


if (!isset($UNIT['component']) or !trim($UNIT['component'])) return;

if ($fn = mso_fe( 'components/' . trim($UNIT['component']) . '/' . trim($UNIT['component']) . '.php' )) require($fn);

# end file