<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if ($f = mso_page_foreach('home-head-meta')) require($f);

# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

mso_set_val('exclude_page_id', array()); // исключаем вывод записей из нижних блоков

// если указан текст перед всеми записями, то выводим и его
if (mso_get_option('home_text_do', 'templates', ''))
{
	if ($fn = mso_find_ts_file('type/home/units/home-text-top.php')) require($fn);
}

// top-запись
if (mso_get_option('home_page_id_top', 'templates', '0'))
{
	if ($fn = mso_find_ts_file('type/home/units/home-top-page.php')) require($fn);
}

// последняя запись
if (mso_get_option('home_last_page', 'templates', '0'))
{
	if ($fn = mso_find_ts_file('type/home/units/home-last-page.php')) require($fn);
}

if ($fn = mso_find_ts_file('type/home/my_home.php')) // вывод в своём шаблоне - в shared файла нет!
{
	require($fn);
}
else
{	
	// свой вариант вывода главной на основе опций с [unit]
	$home_units = false;
	
	// если есть units.php, то получаем из него текст с [unit]
	if ($fn = mso_find_ts_file('type/home/units.php')) 
	{
		$home_units = file_get_contents($fn);
	}
	else
	{
		$home_units = mso_get_option('home_units', getinfo('template'), ''); // или из опции
	}
	
	if ($home_units)
	{
		mso_units_out($home_units);
	}
	else // типовой вывод главной
	{
		// блоки рубрик на главной
		if (mso_get_option('home_cat_block', 'templates', '0'))
		{
			// обычный вывод
			if (mso_get_option('home_full_text', 'templates', '1'))
			{
				if ($fn = mso_find_ts_file('type/home/units/home-cat-block-full.php')) require($fn);
			}
			else // списком
			{
				if ($fn = mso_find_ts_file('type/home/units/home-cat-block-list.php')) require($fn);
			}
		}
		else // последние записи
		{
			// обычный вывод
			if (mso_get_option('home_full_text', 'templates', '1'))
			{
				if ($fn = mso_find_ts_file('type/home/units/home-full.php')) require($fn);
			}
			else // списком
			{
				if ($fn = mso_find_ts_file('type/home/units/home-list.php')) require($fn);
			}
		}
	}
}
	
# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

# end of file