<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// получение всех мета из ini-файлов
// результат в  $all_meta $custom_meta $custom_meta_i
// мета-поля, которые следует здесь отобразить описываются в ini-файле.

$all_meta = ''; // основные + custom/meta.ini
$custom_meta = $custom_meta_i = ''; // custom/my_meta.ini отдельной tab-вкладкой

// получим одним запросом все мета поля 
if ($id)
{
	$CI->db->select('meta_value, meta_key');
	$CI->db->where( array ('meta_id_obj' => $id, 'meta_table' => 'page' ) );
	$query = $CI->db->get('meta');
	
	$page_all_meta = array();
	foreach ($query->result_array() as $row)
	{
		$page_all_meta[$row['meta_key']][] = $row['meta_value'];
	}
}
else
{
	$page_all_meta = array();
}

// pr($page_all_meta);

require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом

// получим все данные из ini-файла
$all = mso_get_ini_file( $MSO->config['admin_plugins_dir'] . 'admin_page/meta.ini');

//pr($all);

// можно использовать дефолтный из shared_dir
$meta_def = mso_get_ini_file( getinfo('shared_dir') . 'meta/meta.ini'); 
$all = array_merge($all, $meta_def);


// meta.ini в template_dir — больше не использовать! — оставил пока как временное решение
// использовать custom/meta.ini — будет вместе с основными мета
if (file_exists(getinfo('template_dir') . 'meta.ini')) 
{
	$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'meta.ini' );
	if ($meta_templ) $all = array_merge($all, $meta_templ);
}

if (file_exists(getinfo('template_dir') . 'custom/meta.ini')) 
{
	$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'custom/meta.ini' );
	if ($meta_templ) $all = array_merge($all, $meta_templ);
}


// pr($all);
$all_meta = all_meta_parse($all, $page_all_meta);

// custom/my_meta.ini — отдельной вкладкой
$custom_meta = array();

if (file_exists(getinfo('template_dir') . 'custom/my_meta.ini')) 
{
	$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'custom/my_meta.ini' );
	if ($meta_templ) $custom_meta = $meta_templ;
}

// для особых случаев
if (file_exists(getinfo('template_dir') . 'custom/my_meta.php'))
	require( getinfo('template_dir') . 'custom/my_meta.php' );

$custom_meta = all_meta_parse($custom_meta, $page_all_meta);

// custom_meta может и не быть в текущем шаблоне, поэтому делаем их вывод отдельно в формате tabs
if 	($custom_meta)
{
	$custom_meta = '<div class="mso-tabs-box custom-meta tabs_bordered">' . $custom_meta . '</div>';
	$custom_meta_i = '<li class="mso-tabs-elem i-custom-meta"><span>' . t('Шаблонные') . '</span></li>';
}


// парсинг ini
function all_meta_parse($all, $page_all_meta)
{	
	$all_meta = '';
	
	// проходимся по всем ini-опциям
	// для совместимости используем вместо meta_  options_
	foreach ($all as $key=>$row)
	{
		if ( isset($row['options_key']) ) 
			$options_key = stripslashes(trim($row['options_key']));
		else 
			continue;
		
		if ($options_key == 'tags') continue; // метки отдельно идут
		
		if ( !isset($row['type'])) 
			$type = 'textfield';
		else 
			$type = stripslashes(trim($row['type']));
		
		if (!isset($row['values'])) 
			$values = '';
		else 
			$values = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['values']))));
		
		if (!isset($row['description'])) 
			$description = '';
		else 
			$description = _mso_ini_check_php(stripslashes( trim( t($row['description']))));
		
		if (!isset($row['delimer'])) 
			$delimer = '<br>';
		else 
			$delimer = stripslashes($row['delimer']);	
		
		if (!isset($row['default'])) 
			$default = '';
		else 
			$default = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['default']))));
		
		if (!isset($row['placeholder'])) 
			$placeholder = '';
		else 
			$placeholder = ' placeholder="' . _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['placeholder'])))) . '"';
		
		// дополнительные атрибуты
		$attr = (isset($row['attr'])) ? ' ' . trim($row['attr']) : '';
		
		$options_present = true; // признак, что опция есть в базе
		
		// получаем текущее значение 
		if (isset($page_all_meta[$options_key])) // есть в мета
		{
			foreach ($page_all_meta[$options_key] as $val)
			{
				$value = htmlspecialchars($val);
			}
		}
		else 
		{
			$options_present = false;
			$value = $default; // нет значание, поэтому берем дефолт
		}
		
		$f = NR; 

		$name_f = 'f_options[' . $options_key . ']'; // название поля 
		
		if ($type == 'textfield')
		{
			$value = str_replace('_QUOT_', '&quot;', $value);
			
			// в этом типе может быть свой type для input
			if ( !isset($row['textfield_type']) ) 
				$textfield_type = 'text';
			else 
				$textfield_type = stripslashes($row['textfield_type']);
			
			$f .= '<input type="' . $textfield_type . '" name="' . $name_f . '" value="' . $value . '"' . $placeholder . $attr . '>' . NR;
		}
		elseif ($type == 'color')
		{
			$f .= mso_load_jquery('jscolor.js', getinfo('common_url') . 'jquery/jscolor/');
			
			$f .= '<input type="text" name="' . $name_f . '" value="' . $value . '" class="color"' . $placeholder . '>' . NR;
		}
		elseif ($type == 'datetime') // пока тестовый вариант 
		{
			
			// http://xdsoft.net/jqplugins/datetimepicker/
			
			$f .= mso_load_jquery('jquery.datetimepicker.full.min.js', getinfo('common_url') . 'jquery/datetimepicker/');
			
			$id_datetimepicker = md5($name_f);
			
			$f .= '<input type="text" name="' . $name_f . '" value="' . $value . '" id="' . $id_datetimepicker . '"' . $placeholder . '>' . NR;
			
			if (isset($row['datetime_options'])) 
				$datetime_options = $row['datetime_options']; 
			else
				$datetime_options = 'format:"Y-m-d H:i:s", lang:"ru"';

			if (isset($row['datetime_locale'])) 
				$datetime_locale = '$.datetimepicker.setLocale("' . trim($row['datetime_locale']) . '");'; 
			else
				$datetime_locale = '$.datetimepicker.setLocale("ru");';
			
			$f .= '<script>' . $datetime_locale . '$("#' . $id_datetimepicker . '").datetimepicker({' . $datetime_options . '});</script>';
		}
		elseif ($type == 'textarea')
		{
			if (isset($row['rows']))
			{
				if ($row['rows'] == 'auto') 
				{
					$rr = count(explode("\n", $value));
					if ($rr > 20) $rr = 20;
				}
				else
					$rr = (int) $row['rows'];
			}
			else
			{
				$rr = count(explode("\n", $value));
				if ($rr > 20) $rr = 20;
			}
			
			if ($rr < 2)  $rr = 2;
			
			
			$f .= '<textarea rows="' . $rr . '" name="' . $name_f . '"' . $placeholder . $attr . '>'. $value . '</textarea>' . NR;
		}
		elseif ($type == 'radio')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				foreach( $values as $val ) 
				{
					$f .= '<label><input type="radio" name="' . $name_f . '" value="' . trim($val) . '"' . _set_checked($value, $val) . '> ' . trim($val) . '</label>' . $delimer . NR;
				}
			}
		}
		elseif ($type == 'select')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				$f .= '<select name="' . $name_f . '">';
				
				foreach($values as $val) 
				{
					// $val может быть с || val - текст
					
					$val = trim($val);
					$val_t = $val;
					
					$ar = explode('||', $val);
					if (isset($ar[0])) $val = trim($ar[0]);
					if (isset($ar[1])) $val_t = trim($ar[1]);
					
					$f .= NR . '<option value="' . $val . '"' . _set_checked($value, $val, 'selected') . '>' . $val_t . '</option>';
				}
				
				$f .= NR . '</select>' . NR;
			}
		}
		elseif ($type == 'checkbox')
		{
			$f .= '<input type="hidden" value="0" name="' . $name_f . '">';
			$f .= '<label><input type="checkbox" value="1" name="' . $name_f . '" ' . _set_checked($value) . '> ' . $key . '</label>' . NR;
		}
		
		if ($description) $f .= '<p class="italic">' .  t($description) . '</p>';
		
		$key = '<p class="w25 w100-tablet bold">' . t($key) . '</p>';
		
		// $all_meta .= '<div>' . $key . NR . $f . '</div>';
		
		if (isset($row['page_type']))
		{
			$page_type = stripslashes(trim($row['page_type']));
			$all_meta .= '<div class="page_meta_block ' . $page_type . '"><div class="flex flex-wrap pad10-tb">' . $key . NR . '<div class="w75 w100-tablet">' . $f . '</div></div></div>';
		}
		else
			$all_meta .= '<div class="page_meta_block"><div class="flex flex-wrap pad10-tb">' . $key . NR . '<div class="w75 w100-tablet">' . $f . '</div></div></div>';
	}
	
	return $all_meta;
}

// вспомогательная функция — ставит checked="checked" или selected
function _set_checked($value, $val = false, $z = 'checked')
{
	if ($val === false) 
	{
		if ($value) 
			return ' ' . $z . '="' . $z . '"';
		else 
			return '';
	}
	
	if ($value == trim($val)) 
		return ' ' . $z . '="' . $z . '"';
	else 
		return '';
}


# end of file