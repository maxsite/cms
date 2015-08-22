<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

 /*
* подключение любого файла шаблона

[unit]
file = require.php
require = имя файла
[/unit]

*/

if ( isset($UNIT['require']) and trim($UNIT['require']))
{
	if ($fn = mso_fe(trim($UNIT['require']))) require($fn);
}


# end file