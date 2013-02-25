<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

global $CONTENT_OUT, $MAIN_OUT; // $MAIN_OUT - только для совместимости со старыми шаблонами. Не использовать!

$CONTENT_OUT = $MAIN_OUT = ob_get_contents();
ob_end_clean();

// если есть custom/main-template.php, то испольузм его
if ($fn = mso_fe('custom/main-template.php'))
{
	require($fn);
}
else
{
	// main-шаблон вывода находится в meta-поле page_template
	// это определено в shared/meta/meta.ini
	// если метаполе не задано, то может использоваться main/type/page/main.php
	if (is_type('page') and isset($pages) and isset($pages[0]))
	{
		if ($page_template = mso_page_meta_value('page_template', $pages[0]['page_meta']))
		{
			if ($fn = mso_fe('main/' . $page_template . '/main.php')) 
			{	
				mso_set_val('main_file', $fn); // выставляем путь к файлу
			}
		}
		elseif ($fn = mso_fe('main/type/page/main.php')) // предопределенный файл
		{	
			mso_set_val('main_file', $fn); // выставляем путь к файлу
		}
		else
		{	
			if($page_template = mso_get_option('main_template_page', 'templates', '')) // опция
			{
				if ($fn = mso_fe('main/' . $page_template . '/main.php')) 
				{	
					mso_set_val('main_file', $fn); // выставляем путь к файлу
				}
			}
		}
		
	}
	else
	{
		// возможно есть main-файл по type
		// в main/type/home/main.php
		if ($fn = mso_fe('main/type/' . getinfo('type') . '/main.php')) 
		{	
			mso_set_val('main_file', $fn); // выставляем путь к файлу
		}
		else
		{
			// возможно указана опця
			// main_template_TYPE 
			// опции заданы в ini-файлах
			if ($page_template = mso_get_option('main_template_' . getinfo('type'), 'templates', ''))
			{
				if ($fn = mso_fe('main/' . $page_template . '/main.php')) 
				{	
					mso_set_val('main_file', $fn); // выставляем путь к файлу
				}
			}
		}
	}
}

$fn_main = mso_get_val('main_file', '');

if ($fn_main and file_exists($fn_main)) 
{
	require($fn_main);
}
else 
{
	$fn_main = getinfo('template_dir') . 'main.php';
	
	// может быть задан main-файл по-умолчанию в опции main_template_default
	if ($page_template = mso_get_option('main_template_default', 'templates', ''))
	{
		if ($fn = mso_fe('main/' . $page_template . '/main.php')) 
		{	
			$fn_main = $fn; // выставляем путь к файлу
		}
	}
	
	require($fn_main);
}

# end file