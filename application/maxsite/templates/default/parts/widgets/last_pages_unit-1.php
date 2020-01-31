<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод в виджете

// записи в виде миниатюры и заголовка записи
// Примечание: этот же самый вывод можно получить с помощью виджета last_pages_unit

// 1. Активируйте плагин any_file
// 2. Активируйте плагин last_pages_unit
// 3. Разместите в сайдбаре виджет: any_file_widget lastpagesunit-1
// 4. В настройках виджета 
//		- укажите файл: TEMPLATE/parts/widgets/last_pages_unit-1.php

if (function_exists('last_pages_unit_widget_custom')) 
{
	echo last_pages_unit_widget_custom(array(
'header' => '',
'prefs' => '
order_asc = random
limit = 3
line1 = [thumb]
line2 = [title]
line3 = 
line4 = 
line5 = 
page_start = <div class="mar15-tb flex">
page_end = </div>
title_start = <div class="flex-grow1 mar10-l">
title_end = </div>
block_start= <div>
block_end = </div>
content = 0
thumb_class = w100px-min reyes hover-no-filter
thumb_link_class = 
thumb_width = 100
thumb_height = 100
',
'_lastpagesunit-1')); 

}
