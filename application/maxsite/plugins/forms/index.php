<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function forms_autoload($args = array())
{
	mso_hook_add( 'content', 'forms_content'); # хук на вывод контента
}

# функции плагина
function forms_content($text = '')
{
	if (strpos($text, '[form]') !== false) 
		$text = preg_replace_callback('!\[form\](.*?)\[/form\]!is', 'forms_content_callback', $text );
	
	return $text;
}

# callback-функция
# вся логика формы
function forms_content_callback($matches)
{
	$text = $matches[1];
	
	$text = str_replace("\r", "", $text);
	$text = str_replace('&nbsp;', ' ', $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace('<br />', "<br>", $text);
	$text = str_replace('<br>', "\n", $text);
	$text = str_replace("\n\n", "\n", $text);
	$text = str_replace('     ', ' ', $text);
	$text = str_replace('    ', ' ', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	$text = str_replace("\n ", "\n", $text);
	$text = str_replace("\n\n", "\n", $text);
	$text = trim($text);
	
	// число антиспама привязано и к сессии
	$ses = getinfo('session');
	$ses = preg_replace("/\D/", '', $ses['session_id']);
	$ses = substr($ses, 0, 4);
	
	$antispam_num = date('jw') + date('t') * date('G') + $ses;
	
	// служебная секция [options]
	$def = array(
		'email' => mso_get_option('admin_email', 'general', 'admin@site.com') , // на какой email отправляем
		'subject' => '', // subject email если пусто, то используется из [field]
		'from' => '', // from email если пусто, то используется из [field]
		'redirect' => '', // куда редиректить после отправки
		'redirect_pause' => '2', // пауза перед редиректом секунд
		'ushka' => '', // ушка к форме
		'reset' => 1, // показать кнопку Сброс формы
		'require_title' => '*', // текст для обязательного поля
		'antispam' => tf('Наберите число'), // вопросы антиспама
		'antispam_ok' => $antispam_num, // правильный ответ на антиспам — задается автоматом
		'captcha' => 0, // по-умолчанию сторонняя капча не используется
	);
	
	$options = mso_section_to_array($text, 'options', $def, true);
	
	// если секции [options] нет, то ставим дефолт
	if (!$options) $options[0] = $def;
	
	if ($options) $options = $options[0];

	// служебная секция [files] 
	$def = array(
		'file_count' => 0, // количество полей - если 0, то полей нет
		'file_type' => 'jpg|jpeg|png|svg', // загружаемые типы файлов
		'file_max_size' => 200, // максимальный размер файла в КБ
		'file_description' => 'Скриншоты', // название поля
		'file_tip' => tf('Выберите для загрузки файлы (jpg, jpeg, png, svg) размером до 200 Кб'), // подсказка
	);

	$files = mso_section_to_array($text, 'files', $def, true);
	
	if ($files) 
	{
		$files = $files[0]; // только одна секция
		
		// если полей меньше 1, то обнуляем массив
		if ((int) $files['file_count'] < 1) $files = array(); 
	}
	
	// поля формы [field]
	$def = array(
		'require' => 0, // обязательное поле 
		'type' => 'text', // тип поля по-умолчанию
		'description' => '', // название поля
		'placeholder' => '', // подсказка в поле
		'tip' => '', // подсказка после поля
		'value' => '', // значение по-умолчанию
		'attr' => '', // прочие атрибуты поля
		'clean' => 'base', // фильтрация поля
		'values' => '', // значение для select через #
		'default' => '', // дефолтное значение для select
		'subject' => 0, // поле испольуется как subject письма 
		'from' => 0, // поле испольуется как from (от кого) письма 
	);
	
	$fields = mso_section_to_array($text, 'field', $def, true);
	
	$options = array_map('trim', $options);
	
	// pr($options);
	// pr($files);
	// pr($fields);
	// pr($text);
	
	// html-формат вывода 

	$format['container_class'] = 'mso-forms'; // css-класс для div-контейнера формы
	
	$format['textarea'] = '<p><label><span>[description][require_title]</span><textarea [field_param]>[value]</textarea></label>[tip]</p>';
	
	$format['checkbox'] = '<p><label><span></span><input [field_param] value="1" type="checkbox"> [description]</label>[tip]</p>';
	
	$format['select'] = '<p><label><span>[description][require_title]</span><select [field_param]>[option]</select></label>[tip]</p>';
	
	$format['input'] = '<p><label><span>[description][require_title]</span><input [field_param]></label>[tip]</p>';

	$format['tip'] = '<span class="mso-forms-tip">[tip]</span>';
	
	$format['file_description'] = '<p><label><span>[file_description]</span></label></p>';
	$format['file_field'] = '<p>[file_field]</p>';
	$format['file_tip'] = '<p><span class="mso-forms-tip">[file_tip]</span></p>';
	
	$format['message_ok'] = '<p class="mso-forms-ok">' . tf('Ваше сообщение отправлено') . '</p>';
	$format['message_error'] = '<p class="mso-forms-error">[message_error]</p>';
	
	$format['antispam'] = '<p><label><span>[antispam] [antispam_ok][require_title]</span>[input]</label></p>';

	$format['captcha'] = '[captcha]';
	
	$format['buttons'] = '<p class="mso-forms-buttons">[submit] [reset]</p>';
	
	$format['mail_field'] = '[description]: [post_value] [NR]';

	
	// подключаем файл формата из текущего шаблона
	if ($fn = mso_fe('custom/plugins/forms/format.php')) require($fn);
	
	$out = ''; // затираем исходный текст формы
	
	if ($_POST)
	{
		// если это отправка
		$result_post = forms_content_post($options, $files, $fields, $format);
		
		// в $result_post результат отправки
		
		if ($result_post['show_ok']) // всё ок
		{
			$out .= $format['message_ok'];
			
			if ($options['redirect'])
			{
				// редирект через N секунд
				header('Refresh: ' . $options['redirect_pause'] . '; url=' . $options['redirect']);
			}
		}
		elseif ($result_post['show_error']) // есть ошибки
		{
			// вывод сообщений об ошибках

			foreach($result_post['show_error'] as $error)
			{
				$out .= str_replace('[message_error]', tf($error), $format['message_error']);
			}
			
			if ($result_post['show_form']) // нужно показать форму
			{
				$out .= forms_show_form($options, $files, $result_post['fields'], $format);
			}
		}
	}
	else
	{
		// выводим форму
		$out .= forms_show_form($options, $files, $fields, $format);
	}
	
	return $out;
}


# выводим форму
function forms_show_form($options, $files, $fields, $format)
{
	$out = '';

	if (!$fields) return ''; // нет полей — выходим
	
	// удаляем временные файлы вложений перед отображением формы
	if($files) mso_flush_cache(false, 'forms_attaches/');
		
	// pr($fields);
	
	// начальная часть всегда однакова ???
	$out .= '<div class="' . $format['container_class'] . '"><form method="post" enctype="multipart/form-data">' . mso_form_session('forms_session');

	foreach($fields as $key => $field)
	{
		$field = array_map('trim', $field);
		
		// pr($field);
		
		// ключ для каждого отправляемого поля
		$field_name = 'forms_fields[' . $key . ']';
		
		$description = $field['description']; // название поля
		
		// подсказка в поле
		$placeholder = ($field['placeholder']) ? ' placeholder="' . htmlspecialchars($field['placeholder']) . '"' : '';
		
		// обязательные поля
		if ($field['require'])
		{
			// подсказка что это обязательное поле
			$require_title = ' ' . $options['require_title'];
		
			// если поле обязательное ставим ему required
			$required = ' required';
		}
		else
		{
			$require_title = '';
			$required = '';
		}
		
		// подсказка после поля
		$tip = $field['tip'] ? str_replace('[tip]', $field['tip'], $format['tip']) : ''; 
		
		$attr = ($field['attr']) ? ' ' . $field['attr'] : ''; // атрибуты поля
		
		$value = htmlspecialchars($field['value']); // значение value по-умолчанию
		
		// если был POST, то ставим его (он уже обработан)
		if (isset($field['post_value'])) $value = $field['post_value'];
		
		
		if ($field['type'] == 'textarea')
		{
			// комбинируем name + placeholder + $required + $attr
			$field_param = 'name="' . $field_name . '" ' . $placeholder . $required . $attr;
			
			$out .= str_replace(array('[description]', '[require_title]', '[field_param]', '[value]', '[tip]'), array($description, $require_title, $field_param, $value, $tip), $format['textarea']);
		}
		elseif ($field['type'] == 'checkbox')
		{
			// дефолтное значение 0 или 1
			$checked = $field['default'] ? ' checked="checked"' : '';
			
			$field_param = 'name="' . $field_name . '" ' . $attr . $checked;
			
			// cкрытый input для того, чтобы передать неотмеченный чекбокс будет - value="0"
			$out .= '<input name="' . $field_name . '" value="0" type="hidden">';
			
			$out .= str_replace(array('[description]', '[field_param]', '[tip]'), array($description, $field_param, $tip), $format['checkbox']);
		}
		elseif ($field['type'] == 'select')
		{
			if (! $values = $field['values']) continue; // не указаны значение
			
			$default = $field['default']; // дефолтное значение
			
			$values = explode('#', $values);
			
			$option = '';
			
			foreach ($values as $o)
			{
				$selected = ($o == $default) ? ' selected="selected"' : '';
				
				$option .= '<option' . $selected . '>' . htmlspecialchars(tf($o)) . '</option>';
			}
			
			$field_param = 'name="' . $field_name . '" ' . $attr;
			
			$out .= str_replace(array('[description]', '[require_title]', '[field_param]', '[option]', '[tip]'), array($description, $require_title, $field_param, $option, $tip), $format['select']);
		}
		else // обычный input с любым type
		{
			// комбинируем name + placeholder + $required + $attr
			$field_param = 'name="' . $field_name . '" value="' . $value . '" type="' . $field['type'] . '"'. $placeholder . $required . $attr;
			
			$out .= str_replace(array('[description]', '[require_title]', '[field_param]', '[tip]'), array($description, $require_title, $field_param, $tip), $format['input']);
		}
	}
	
	// поля для $files вывести 
	if ($files)
	{
		$out .= forms_files_fields($files, $format);
	}
	
	// антиспам
	if (isset($options['captcha']) and $options['captcha'])
	{
		ob_start();
		mso_hook('comments_content_end');
		$captcha = ob_get_contents(); ob_end_clean();
		$require_title = ' ' . $options['require_title'];
			
		$out .= str_replace(array('[captcha]', '[require_title]'), array($captcha, $require_title), $format['captcha']);
	}
	elseif ($options['antispam'])
	{
		$antispam = $options['antispam'];
		$antispam_ok = $options['antispam_ok'];
		$require_title = ' ' . $options['require_title'];
		$input = '<input class="mso-forms-antispam" type="text" name="forms_fields[antispam]" required>';
		
		$out .= str_replace(array('[antispam]', '[antispam_ok]', '[input]', '[require_title]'), array($antispam, $antispam_ok, $input, $require_title), $format['antispam']);
	}
	
	$submit = '<button type="submit">' . tf('Отправить') . '</button>';
	
	if (!$options['reset']) 
		$reset = '';
	else
		$reset = '<button type="reset">' . tf('Очистить форму') . '</button>';
	
	$out .= str_replace(array('[submit]', '[reset]'), array($submit, $reset), $format['buttons']);
	
	if (function_exists('ushka') and $options['ushka']) $out .= ushka($options['ushka']);
	
	// конец формы
	$out .= '</form></div>';

	
	return $out;
}

// поля для загрузки файлов в форме
function forms_files_fields($files, $format)
{
	$out = '';
	
	$out .= $files['file_description'] ? str_replace('[file_description]', $files['file_description'], $format['file_description']) : '';
	
	for( $it = 1; $it <= $files['file_count']; $it++ )
	{
		$out .= str_replace('[file_field]', '<input name="forms_files[]" type="file">', $format['file_field']);
	}
	
	$out .= ($files['file_tip']) ? str_replace('[file_tip]', $files['file_tip'], $format['file_tip']) : '';
		
	return $out;
}


# обработка POST
function forms_content_post($options, $files, $fields, $format)
{
	$result['show_error'] = array(); // каждый элемент сообщение об ошибке 
	$result['show_form'] = false;
	$result['show_ok'] = false;
	$result['fields'] = array(); // массив полей в случае ошибок
	
	$out = '';
	
	// принимаем post
	if ( $post = mso_check_post(array('forms_session', 'forms_fields')) )
	{
		mso_checkreferer();
		
		// pr($options);
		// pr($post);
		
		// антиспам
		
		if (isset($options['captcha']) and $options['captcha'])
		{
			if ( !mso_hook('comments_new_captcha') )
			{
				$result['show_error'][] = tf('Неверно заполнено поле антиспама');
				$result['show_form'] = true;
				$result['fields'] = $fields;
			
				return $result;
			}
		}
		elseif ($options['antispam'])
		{
			if (
				!isset($post['forms_fields']['antispam'])
				or
				((int) $post['forms_fields']['antispam'] !== (int) $options['antispam_ok'])
				)
			{
				$result['show_error'][] = tf('Неверно заполнено поле антиспама');
				$result['show_form'] = true;
				$result['fields'] = $fields;
			
				return $result;
			}

		}
		
		$subject_key = false; // если у поля отмечен subject, то ставим номер поля
		$from_key = false; // если у поля отмечен from, то ставим номер поля
		
		// добавляем в массив $field полученные значения и сразу их чистим через mso_clean_str()
		foreach($fields as $key => $field)
		{
			if (isset($fields[$key]['post_value'])) unset($fields[$key]['post_value']);
			
			$field = array_map('trim', $field);
			
			if (isset($post['forms_fields'][$key]))
			{
				$p_v = mso_clean_str($post['forms_fields'][$key], $field['clean']);
				
				$fields[$key]['post_value'] = $p_v;
				
				// обязательное поле и не получены данные (браузер должен был это сам отсеить)
				if ($field['require'] and !$p_v)
				{
					$result['show_error'][] = tf('Неверно заполнено поле: ') . $field['description'];
				}
			}
			
			if ($field['subject']) $subject_key = $key;
			if ($field['from']) $from_key = $key;
		}
		
		// если есть ошибки то выходим
		if ($result['show_error'])
		{
			$result['show_form'] = true;
			$result['fields'] = $fields;
			
			return $result;
		}
		
		// если были ошибки, то уже вышли из функции
		
		$prefs = []; // дополнительные опции для mso_mail
		
		// если есть вложения
		if ($file_attaches = forms_files_post($files))
		{
			// формируем вложения к письму
			$prefs['attach'] = $file_attaches; 
		}
		
		// формируем само письмо
		
		// куда приходят письма
		$email = $options['email']; 
		
		if (!mso_valid_email($email))
			$email = mso_get_option('admin_email', 'general', 'admin@site.com'); 
		
		// тема письма может быть в опциях
		$subject = $options['subject'];
		
		if (!$subject) // нет, значит ищем в полях
			$subject = $fields[$subject_key]['post_value'];
			
		// тема письма может быть в опциях
		$from = $options['from'];
		
		if (!$from) // нет, значит ищем в полях
			$from = $fields[$from_key]['post_value'];		
		
		// pr($fields);
		
		$message = '';
		
		foreach($fields as $field)
		{
			$description = $field['description'];
			$post_value = $field['post_value'];
			
			$m = $format['mail_field'];
			
			$m = str_replace('[description]', $description, $m); 
			$m = str_replace('[post_value]', $post_value, $m); 
			$m = str_replace('[NR]', NR, $m); 
			
			$message .= $m;
		}
		
		// добавляем служебную информацию
		$message .= tf('IP: ') . $_SERVER['REMOTE_ADDR'] . NR;
		$message .= tf('Браузер: ') . $_SERVER['HTTP_USER_AGENT'] . NR;
		
		mso_hook('forms_send', $post);
		
		// pr($email);
		// pr($subject);
		// pr($from);
		// pr($message);
		// pr($prefs);
		
		// тут отправка почты
		mso_mail($email, $subject, $message, $from, $prefs);
		
		// удаляем временные файлы вложений
		if($files) mso_flush_cache(false, 'forms_attaches/');
		
		$result['show_ok'] = true;
	}
	else
	{
		$result['show_error'][] = tf('Ошибка сессии');
	}
	
	return $result;
}


// загрузка файлов по POST
function forms_files_post($files)
{
	// возвращать нужно данные для $prefs

	if (!$files) return false; // файлы не нужны
	
	if (!isset($_FILES)) return false; // не были отправлены файлы
	
	require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
					
	$_FILES = mso_prepare_files('forms_files'); // переформатируем массив присланных файлов
		
	// формирование папки для временных файлов вложений
	$cache_folder = getinfo('cache_dir').'forms_attaches/';
	
	if( !file_exists($cache_folder) ) mkdir($cache_folder, 0777);
		
	// параметры для mso_upload
	// конфиг CI-библиотеки upload
	$mso_upload_ar1 = array( 
		'upload_path' => $cache_folder,
		'allowed_types' => $files['file_type'],
		'max_size' => $files['file_max_size'],
		'overwrite' => true,
	);

	$mso_upload_ar2 = array( // массив прочих опций
		'userfile_resize' => false, // нужно ли менять размер
		'userfile_water' => false, // нужен ли водяной знак
		'userfile_mini' => false, // делать миниатюру?
		'prev_size' => false, // превьюху не делаем
		'mini_make' => false, // не создаём папку mini
		'prev_make' => false, // не создаём папку _mso_i
		'message1' => '', // не выводить сообщение о загрузке каждого файла
		//'message2' => '',
	);
		
	$CI = & get_instance();
	
	$file_attaches = array();
		
	foreach ($_FILES as $f_key => $f_info)
	{
		ob_start();
		$res = mso_upload($mso_upload_ar1, $f_key, $mso_upload_ar2);
		$msg = ob_get_contents(); // сообщения, если были ошибки
		ob_end_clean();
			
		if (!$msg && $res)
		{
			$up_data = $CI->upload->data();	
				
			// формируем список вложений к письму
			$file_attaches[] = $up_data['full_path'];
		}
		else
		{
			// ошибки не выводим
			// $out .= '<div class="message error small">' . strip_tags($msg) . '</div>';
			break;
		}
	}
				
	return $file_attaches;
}


# end of file