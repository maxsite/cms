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
		// если в тексте юнита есть вхождение @use_tmpl@
		// то разрешим в файле исполнять PHP через шаблонизатор
		if (strpos($home_units, '@use_tmpl@'))
		{
			$home_units = mso_tmpl_prepare($home_units, false);
			ob_start();
			eval($home_units);
			$home_units = ob_get_contents();
			ob_end_clean();
		}
		
		// ищем вхождение [unit] ... [/unit]
		$units = mso_section_to_array($home_units, '!\[unit\](.*?)\[\/unit\]!is', array('file'=>''));
		
		// pr($units);
		
		// подключаем каждый указанный unit 
		// _rules — php-условие, при котором юнит выводится
		// параметр file где указывается файл юнита в каталоге type/home/units/
		// если file нет, то проверяются другие параметры если есть:
		// html — выводится как есть текстом/ Можно использовать php-шаблонизатор {{ }} и {% %}
		// require — подключается файл в шаблоне (пусть относительно каталога шаблона)
		// ushka — ушка
		// component — компонент шаблона
		// option_key и option_type и option_default — опция
		if ($units) 
		{
			$UNIT_NUM = 0; // порядковый номер юнита (можно использовать для кэширования)
			
			foreach ($units as $UNIT)
			{
				$UNIT_NUM++;
				
				if (isset($UNIT['_rules']) and trim($UNIT['_rules']))
				{
					$rules = 'return ( ' . trim($UNIT['_rules']) . ' ) ? 1 : 0;';
					$rules_result = eval($rules); // выполяем
					if ($rules_result === false) $rules_result = 1; // возможно произошла ошибка
					if ($rules_result !== 1) continue;
				}
				
				if (trim($UNIT['file']))
				{
					// в подключаемом файле доступна переменная $UNIT — массив параметров
					if ($fn = mso_find_ts_file('type/home/units/' . trim($UNIT['file']))) require($fn);
				}
				elseif (isset($UNIT['html']) and trim($UNIT['html']))
				{
					eval(mso_tmpl_prepare(trim($UNIT['html']), false));
				}
				elseif (isset($UNIT['require']) and trim($UNIT['require']))
				{
					if ($fn = mso_fe(trim($UNIT['require']))) require($fn);
				}
				elseif (isset($UNIT['ushka']) and trim($UNIT['ushka']) and function_exists('ushka'))
				{
					echo ushka(trim($UNIT['ushka']));
				}
				elseif (isset($UNIT['component']) and trim($UNIT['component']))
				{
					if ($_fn = mso_fe( 'components/' . trim($UNIT['component']) . '/' . trim($UNIT['component']) . '.php' )) require($_fn);
				}
				elseif (isset($UNIT['option_key'], $UNIT['option_type'], $UNIT['option_default']) and trim($UNIT['option_key']) and trim($UNIT['option_type']) and trim($UNIT['option_default']))
				{
					echo mso_get_option(trim($UNIT['option_key']), trim($UNIT['option_type']), trim($UNIT['option_default']));
				}
				elseif (isset($UNIT['sidebar']) and trim($UNIT['sidebar']))
				{
					mso_show_sidebar($UNIT['sidebar']);
				}
			}
		}
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

# end file