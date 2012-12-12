<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// Пример смены шаблона main.php на тот, который указан в мета-поле page_template записи
// шаблон вывода хранится как main/templates/ШАБЛОН/main.php
// файл аналогичен основному main.php

// алгоритм преключения main.php может быть произвольным

/*

В custom/my_meta.ini добавить:

	[Шаблон записи]
	options_key = page_template
	type = select
	description = "Укажите шаблон записи"
	default = ""
	values =  "<?php 
		$dirs = mso_get_dirs(getinfo('template_dir') . 'main/templates/', array(), 'main.php');
		echo ' ||Нет #' . implode($dirs, '#');
	?>"



// только для типа page
// при условии наличия мета - page_template
// и если есть файл в main/templates/ШАБЛОН/main.php
 
if (is_type('page') and isset($pages) and isset($pages[0]))
{
	if ($page_template = mso_page_meta_value('page_template', $pages[0]['page_meta']))
	{
		if ($fn = mso_fe('main/templates/' . $page_template . '/main.php')) 
		{	
			mso_set_val('main_file', $fn); // выставляем путь к файлу
		}
	}
}

*/
