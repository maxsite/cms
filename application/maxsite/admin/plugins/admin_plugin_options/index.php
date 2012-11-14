<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



# функция автоподключения плагина
function admin_plugin_options_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_plugin_options_admin_init');
	mso_create_allow('admin_plugin_options', t('Админ-доступ к редактированию опций плагинов'));
}

# функция выполняется при указаном хуке admin_init
function admin_plugin_options_admin_init($args = array()) 
{

	if ( mso_check_allow('admin_plugin_options') ) 
	{
		$this_plugin_url = 'plugin_options'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# Четвертый - номер в меню
		
		// в меню не нужно
		// mso_admin_menu_add('plugins', $this_plugin_url, t('Опции плагинов'), 3);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/admin_plugin_options
		mso_admin_url_hook ($this_plugin_url, 'admin_plugin_options_admin');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_plugin_options_admin($args = array()) 
{
	if ( !mso_check_allow('admin_plugin_options') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройка плагина') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройка плагина') . ' - " . $args; ' );
	
	if ($plugin = mso_segment(3))
	{
		if ( !file_exists(getinfo('plugins_dir') . $plugin . '/index.php') )
		{
			echo t('Плагин не найден.');
			return $args;
		}	
		
		if (!function_exists($plugin . '_mso_options'))
		{
			echo t('Для данного плагина настроек не предусмотрено.');
			return $args;
		}
		else 
		{
			$fn = $plugin . '_mso_options';
			$fn();
		}
	}
	else
	{
		echo t('Неверно указан плагин.');
	}
}

# ключ, тип, ключи массива
# функция проверяет входящий post
# если все ок, то вносит новые значения в опции
# если post нет, то выводит форму с текущими значениями опций
function mso_admin_plugin_options($key, $type, $ar, $title = '', $info = '', $text_other = true)
{

	if ($title)
		echo '<h1><a href="">' . $title . '</a></h1>';
	else
		echo '<h1><a href="">' . t('Опции плагина') . '</a></h1>';
	

	if ($info)
		echo '<p class="info">' . $info . '</p>';
	else
		echo '<p class="info">' . t('Укажите необходимые опции плагина.') . '</p>';
		
	
	if ($text_other === true)
		echo '<p><a href="' . getinfo('site_admin_url') . 'plugins">' . t('Вернуться на страницу плагинов') . '</a></p>';
	elseif ($text_other) echo '<p>' . $text_other . '</p>';
	
	
	# тут получаем текущие опции
	$options = mso_get_option($key, $type, array() ); // получаем опции
	
	# здесь смотрим post
	# в post должен быть $key . '-' . $type
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', $key . '-' . $type)) )
	{
		# защита рефера
		mso_checkreferer();
		
		# наши опции
		$in = $post[$key . '-' . $type];
		
		if (isset($in['_mso_checkboxs'])) // есть чекбоксы
		{
			$ch_names = array_keys($in['_mso_checkboxs']); // получили все чекбоксы
			$t = array(); // временный массив
			foreach($ch_names as $val) // проверим каждый чекбокс
			{
				if (isset($in[$val])) $t[$val] = '1'; // если есть, значит отмечен
			}
			
			$t = array_merge($in['_mso_checkboxs'], $t); // объединим с чекбоксамии
			unset($in['_mso_checkboxs']); // удалим _mso_checkboxs
			$in = array_merge($in, $t); // объединим с $in
			// теперь в $in все чекбоксы
		}
		
		# перед проверкой удалим из $ar все типы info
		$ar1 = $ar;
		foreach($ar1 as $m => $val)
			if ($val['type'] == 'info')	unset($ar1[$m]);
		
		# проверяем их с входящим $ar - ключи должны совпадать
		# финт ушами: смотрим разность ключей массивов - красиво?
		# если будет разность, значит неверные входящие данные, все рубим
		if (array_diff(array_keys($ar1), array_keys($in))) die('Error key. :-(');
		
		$newoptions = array_merge($options, $in); // объединим
		
		if ( $options != $newoptions ) 
		{
			mso_add_option($key, $newoptions, $type); // обновим
			$options = $newoptions; // сразу обновим переменную на новые опции
			mso_flush_cache(); // сбросим кэш
		}
		
		echo '<div class="update">' . t('Обновлено!') . '</div>';
	}
	
	if ($ar) // есть опции
	{
		# тут генерируем форму
		$form = '';
		
		foreach($ar as $m => $val)
		{
			if ($val['type'] == 'info')
			{
				
				if (isset($val['id'])) $tag_id = ' id="' . $val['id'] . '"';
					else $tag_id= '';
				
				if (isset($val['class'])) $tag_class = ' ' . $val['class'];
					else $tag_class= '';
				
				$form .= '<div class="admin_plugin_options_info' . $tag_class . '"' . $tag_id . '>'; 
				
				if (isset($val['title'])) $form .= '<h3>' . $val['title'] . '</h3>'; 
				if (isset($val['text'])) $form .= '<p>' . $val['text'] . '</p>'; 
				
				$form .= '</div>'; 
				
				continue;
			}
			
			if (!isset($options[$m])) $options[$m] = $val['default'];
			
			$group_start = (isset($val['group_start'])) ? $val['group_start'] : '';
			
			$group_end = (isset($val['group_end'])) ? $val['group_end'] : '';
		
			
			/*
			// обрамление группы опций
			if (isset($val['group_start']))
			{
				if ($val['group_start']) $group_start = '<div class="admin_plugin_options">';
				else $group_start = '';
			}
			else $group_start = '<div class="admin_plugin_options">';
			
			if (isset($val['group_end']))
			{
				if ($val['group_end']) $group_end = '</div>';
				else $group_end = '<br>';
			}
			else $group_end = '</div>';
			
			*/
			
			if ($val['description']) $val['description'] = '<p class="nop"><span class="fhint">' . $val['description'] . '</span></p>';
			
			if ($val['type'] == 'text')
			{
				
				if (isset($val['itype'])) $itype = $val['itype'];
					else $itype = 'text';
				
				if ($itype == 'hidden')
				{
					$form .= $group_start . '<p><b>' 
							. $val['name'] . '</b>'
							. '<input type="' . $itype . '" value="'
							. htmlspecialchars($options[$m]) 
							. '" name="'
							. $key . '-' . $type . '[' . $m . ']'
							. '"></p>' 
							. $val['description']
							. $group_end . NR;
				}
				else
				{
					$form .= $group_start . '<p><label><b>' 
							. $val['name'] . '</b>'
							. '<br><input type="' . $itype . '" value="'
							. htmlspecialchars($options[$m]) 
							. '" name="'
							. $key . '-' . $type . '[' . $m . ']'
							. '"></label></p>' 
							. $val['description']
							. $group_end . NR;
				}
			}
			elseif ($val['type'] == 'textarea')
			{
				if (isset($val['rows'])) $rows = (int) $val['rows'];
				else $rows = 10;
				
				
				$form .= $group_start . '<p><label><b>' 
						. t($val['name']) . '</b>'
						. '<br><textarea rows="' . $rows . '" name="'
						. $key . '-' . $type . '[' . $m . ']'
						. '">'
						. htmlspecialchars($options[$m]) 
						. '</textarea></label></p>' 
						. $val['description'] 
						. $group_end . NR;
			}
			elseif ($val['type'] == 'checkbox')
			{
				$ch_val = $options[$m];
				
				if ($ch_val) $checked = 'checked="checked"';
					else $checked = '';
				
				$form .= $group_start  
						. '<p><label><input class="checkbox" type="checkbox" value="' . $ch_val . '"'
						. ' name="' . $key . '-' . $type . '[' . $m . ']' . '" ' . $checked . '> <b>'
						. $val['name']
						. '</b></label></p>' 
						. $val['description'] 
						. $group_end . NR;
				
				# поскольку не отмеченные чекбоксы не передаются в POST, сделаем массив чекбоксов в hidden
				$form .= '<input type="hidden" name="' . $key . '-' . $type . '[_mso_checkboxs][' . $m . ']" value="0">';	
						
			}
			elseif ($val['type'] == 'select')
			{
				$form .= $group_start . '<p><label><b>' 
						. $val['name'] . '</b>'
						. '<br><select name="'
						. $key . '-' . $type . '[' . $m . ']'
						. '">';
				
				// если есть values, то выводим - правила задания, как в ini-файлах
				if (isset($val['values']))
				{
					$values = explode('#', $val['values']);
					foreach( $values as $v ) 
					{
						$v = trim($v);
						$v_t = $v;
						
						$ar = explode('||', $v);
						if (isset($ar[0])) $v = trim($ar[0]);
						if (isset($ar[1])) $v_t = trim($ar[1]);
						
						if (htmlspecialchars($options[$m]) == $v) $checked = 'selected="selected"';
							else $checked = '';
						$form .= NR . '<option value="' . $v . '" ' . $checked . '>' . $v_t . '</option>';
					}
				}
				$form .= '</select></label></p>' 
						. $val['description']
						. $group_end . NR;
			}
			elseif ($val['type'] == 'radio')
			{
				$form .= $group_start . '<p><b>' 
						. $val['name']
						. '</b></p><p class="nop">';
						
				if ( !isset($val['delimer']) ) $delimer = '<br>';
					else $delimer = stripslashes($val['delimer']);
							
				// если есть values, то выводим - правила задания, как в ini-файлах
				if (isset($val['values']))
				{
					$values = explode('#', $val['values']);
					foreach( $values as $v ) 
					{
						$v = trim($v);
						$v_t = $v;
						
						$ar = explode('||', $v);
						if (isset($ar[0])) $v = trim($ar[0]);
						if (isset($ar[1])) $v_t = trim($ar[1]);
						
						if (htmlspecialchars($options[$m]) == $v) $checked = 'checked="checked"';
							else $checked = '';
						
						$form .= NR . '<label class="nocell"><input type="radio" value="' . $v . '" ' . $checked . ' name="' . $key . '-' . $type . '[' . $m . ']' . '"> ' . $v_t . '</label>' . $delimer;
					}
				}
				
				$form .= '</p>' . $val['description'] . '<hr>'. $group_end . NR;
			}
		}
		
		
		# выводим форму
		echo NR . '<form method="post" class="fform">' . mso_form_session('f_session_id');
		echo $form;
		echo NR . '<p class="br"><input type="submit" name="f_submit" value="' . t('Сохранить') . '"></p>';
		echo '</form>' . NR;
	}
	else
	{
		echo t('<p>Опции не определены.</p>') . NR;
	}
	
}

# end file