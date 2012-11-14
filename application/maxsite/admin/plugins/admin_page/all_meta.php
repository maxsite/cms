<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# получение всех мета из ini-файла 
	# результат в  $all_meta
	# мета-поля, которые следует здесь отобразить описываются в ini-файле.

	$all_meta = '';
	
	
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
	
	// подключаем meta.ini из текущего шаблона
	// при этом складываем их с дефолтным
	
	$meta_def = mso_get_ini_file( getinfo('shared_dir') . 'meta/meta.ini'); // можно использовать дефолтный
	$all = array_merge($all, $meta_def);
	
	if (file_exists(getinfo('template_dir') . 'meta.ini')) 
	{
		$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'meta.ini' );
		if ($meta_templ) $all = array_merge($all, $meta_templ);
	}
	
	if (file_exists(getinfo('template_dir') . 'custom/my_meta.ini')) 
	{
		$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'custom/my_meta.ini' );
		if ($meta_templ) $all = array_merge($all, $meta_templ);
	}
	
	// описание см. shared/blanks/custom/_my_meta.php
	if (file_exists(getinfo('template_dir') . 'custom/my_meta.php'))
	{
		require( getinfo('template_dir') . 'custom/my_meta.php' );
	}
	
	// pr($all);
	// проходимся по всем ini-опциям
	// для совместимости используем вместо meta_  options_
	foreach ($all as $key=>$row)
	{
		if ( isset($row['options_key']) ) $options_key = stripslashes(trim($row['options_key']));
			else continue;
		
		if ($options_key == 'tags') continue; // метки отдельно идут
		
		if ( !isset($row['type']) ) $type = 'textfield';
			else $type = stripslashes(trim($row['type']));
		
		if ( !isset($row['values']) ) $values = '';
			else $values = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['values']))));
			
		if ( !isset($row['description']) ) $description = '';
			else $description = _mso_ini_check_php(stripslashes( trim( t($row['description']))));
			
		if ( !isset($row['delimer']) ) $delimer = '<br>';
			else $delimer = stripslashes($row['delimer']);	
			
		if ( !isset($row['default']) ) $default = '';
			else $default = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['default']))));
		
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
			$f .= '<input type="text" name="' . $name_f . '" value="' . $value . '">' . NR;
		}
		elseif ($type == 'textarea')
		{
			if ( !isset($row['rows']) ) $rr = '';
				else $rr = 'rows="' . (int) $row['rows'] . '" ';
				
			$f .= '<textarea ' . $rr . 'name="' . $name_f . '">'. $value . '</textarea>' . NR;
		}
		elseif ($type == 'radio')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				foreach( $values as $val ) 
				{
					if ($value == trim($val)) $checked = 'checked="checked"';
						else $checked = '';
						
					$f .= '<label><input type="radio" name="' . $name_f . '" value="' . trim($val) . '" ' 
							. $checked . '> ' . trim($val) . '</label>' . $delimer . NR;
				}
			}
		}
		elseif ($type == 'select')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				//$f .= '<select style="width: 99%;" name="' . $name_f . '">';
				$f .= '<select name="' . $name_f . '">';
				
				foreach( $values as $val ) 
				{
				//	if ($value == trim($val)) $checked = 'selected="selected"';
				//		else $checked = '';
				//	$f .= NR . '<option value="' . trim($val) . '" ' . $checked . '>' . trim($val) . '</option>';
				
					// $val может быть с || val - текст
					
					$val = trim($val);
					$val_t = $val;
					
					$ar = explode('||', $val);
					if (isset($ar[0])) $val = trim($ar[0]);
					if (isset($ar[1])) $val_t = trim($ar[1]);
					
					if ($value == $val) $checked = 'selected="selected"';
						else $checked = '';
					$f .= NR . '<option value="' . $val . '" ' . $checked . '>' . $val_t . '</option>';
				}
				$f .= NR . '</select>' . NR;
			}
		}
		elseif ($type == 'checkbox')
		{
			if ($value) $checked = 'checked="checked"';
				else $checked = '';

			$f .= '<input type="hidden" value="0" name="' . $name_f . '">';
			$f .= '<label><input type="checkbox" value="1" name="' . $name_f . '" ' . $checked . '> ' 
			. $key . '</label>' 
			. NR;
		}
		
		if ($description) $f .= '<p>' .  t($description) . '</p>';
		$key = '<h3>' . t($key) . '</h3>';
		
		// $all_meta .= '<div>' . $key . NR . $f . '</div>';
		
		if (isset($row['page_type']))
		{
			$page_type = stripslashes(trim($row['page_type']));
			$all_meta .= '<div class="page_meta_block ' . $page_type . '">' . $key . NR . $f . '</div>';
		}
		else
			$all_meta .= '<div>' . $key . NR . $f . '</div>';
		
	}


# end file