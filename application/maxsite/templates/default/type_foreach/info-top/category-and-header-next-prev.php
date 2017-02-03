<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	info-top файл
	вывод рубрик перед заголовком записи
	плюс навигация next/prev
*/

if ($fn = mso_fe('type_foreach/_next-prev.php')) require($fn);
if ($fn = mso_fe('type_foreach/info-top/category-and-header.php')) require($fn);

# end of file