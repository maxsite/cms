<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Дополнения: Н. Громов (http://nicothin.ru/)
 */

# функция автоподключения плагина
function forms_autoload($args = array())
{
	mso_hook_add( 'content', 'forms_content'); # хук на вывод контента
}

# 
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
	
	$out = ''; // убиваем исходный текст формы
	
	//$r = preg_match_all('!\[email=(.*?)\]|\[redirect=(.*?)\]\[subject=(.*?)\]|\[field\](.*?)\[\/field\]|\[ushka=(.*?)\]!is', $text, $all);
	
	// на какой email отправляем
	$r = preg_match_all('!\[email=(.*?)\]!is', $text, $all);
	if ($r)
		$email = trim(implode(' ', $all[1]));
	else
		$email = mso_get_option('admin_email', 'general', 'admin@site.com');
	
	// тема письма
	$r = preg_match_all('!\[subject=(.*?)\]!is', $text, $all);
	if ($r)
		$subject = trim(implode(' ', $all[1]));
	else
		$subject = tf('Обратная связь');
	
	
	// куда редиректить после отправки
	$r = preg_match_all('!\[redirect=(.*?)\]!is', $text, $all);
	if ($r)
		$redirect = trim(implode(' ', $all[1]));
	else
		$redirect = '';
	
	
	// eirf к форме
	$r = preg_match_all('!\[ushka=(.*?)\]!is', $text, $all);
	if ($r)
		$ushka = trim(implode(' ', $all[1]));
	else
		$ushka = '';
	
	// отправить копию на ваш email
	$r = preg_match_all('!\[nocopy\]!is', $text, $all);
	if ($r)
		$forms_subscribe = false;
	else
		$forms_subscribe = true;
	
	// кнопка Сброс формы
	$r = preg_match_all('!\[noreset\]!is', $text, $all);
	if ($r)
		$reset = false;
	else
		$reset = true;	
	
	
	// pr($all);
	
	
	// поля формы
	$r = preg_match_all('!\[field\](.*?)\[\/field\]!is', $text, $all);
	
	$f = array(); // массив для полей
	if ($r)
	{
		$fields = $all[1];
		
		/* 
		pr($fields);
		pr($email);
		pr($redirect);
		pr($subject);
		pr($ushka);
		*/
		
		
		if ($subject)
		{
			// поле тема письма делаем в виде обязательнного поля select.
			
			// формируем массив для формы
			$subject_f['require'] = 1;
			//$subject_f['type'] = 'select';
			
			$subject_f['type'] = (mb_strpos($subject, '#') === false ) ? 'text' : 'select';
			
			// если это одиночное поле, но при этом текст сабжа начинается
			// с _ то ставим тип hidden
			if ($subject_f['type'] == 'text' and mb_strpos($subject, '_') === 0 ) 
			{
				$subject = mb_substr($subject . ' ', 1, -1, 'UTF-8');
				$subject_f['type'] = 'hidden'; 
			}
			
			$subject_f['description'] = tf('Тема письма');
			//$subject_f['tip'] = t('Выберите тему письма');
			$subject_f['values'] = $subject;
			$subject_f['value'] = $subject;
			$subject_f['default'] = '';

			// преобразования, чтобы сделать ключ для поля 
			$f1['subject'] = $subject_f; // у поля тема будет ключ subject
			foreach($f as $key=>$val) $f1[$key] = $val; 
			$f = $f1;
		}
		
		$i = 0;

		foreach ($fields as $val)
		{
			$val = trim($val);
			
			if (!$val) continue;
			
			$val = str_replace(' = ', '=', $val);
			$val = str_replace('= ', '=', $val);
			$val = str_replace(' =', '=', $val);
			$val = explode("\n", $val); // разделим на строки
			$ar_val = array();
			foreach ($val as $pole)
			{
				$pole = preg_replace('!=!', '_VAL_', $pole, 1);
				
				$ar_val = explode('_VAL_', $pole); // строки разделены = type = select
				if ( isset($ar_val[0]) and isset($ar_val[1]))
					$f[$i][$ar_val[0]] = $ar_val[1];
			}
			
			
			$i++;
		}
		
		if (!$f) return ''; // нет полей - выходим
		
		// теперь по-идее у нас есть вся необходимая информация по полям и по форме
		// смотрим есть ли POST. Если есть, то проверяем введенные поля и если они корректные, 
		// то выполняем отправку почты, выводим сообщение и редиректимся
		
		// если POST нет, то выводим обычную форму
		// pr($f);
		
		if ($_POST) $_POST = mso_clean_post(array(
			'forms_antispam1' => 'integer',
			'forms_antispam2' => 'integer',
			'forms_antispam' => 'integer',
			'forms_name' => 'base',
			'forms_email' => 'email',
			'forms_session' => 'base',
			));
		
		if ( $post = mso_check_post(array('forms_session', 'forms_antispam1', 'forms_antispam2', 'forms_antispam',
					'forms_name', 'forms_email',  'forms_submit' )) )
		{
			mso_checkreferer();
			
			$out .= '<div class="forms-post">';
			// верный email?
			if (!$ok = mso_valid_email($post['forms_email']))
			{
				$out .= '<div class="message error small">' . tf('Неверный email!') . '</div>';
			}
			
			// антиспам 
			if ($ok)
			{
				$antispam1s = (int) $post['forms_antispam1'];
				$antispam2s = (int) $post['forms_antispam2'];
				$antispam3s = (int) $post['forms_antispam'];
				
				if ( ($antispam1s/984 + $antispam2s/765) != $antispam3s )
				{ // неверный код
					$ok = false;
					$out .= '<div class="message error small">' . tf('Неверная сумма антиспама') . '</div>';
				}
			}
			
			if ($ok) // проверим обязательные поля
			{
				foreach ($f as $key=>$val)
				{
					if ( $ok and isset($val['require']) and $val['require'] == 1 ) // поле отмечено как обязательное
					{
						if (!isset($post['forms_fields'][$key]) or !$post['forms_fields'][$key]) 
						{
							$ok = false;
							$out .= '<div class="message error small">' . tf('Заполните все необходимые поля!') . '</div>';
						}
					}
					if (!$ok) break;
				}
			}
			
			// всё ок
			if ($ok)
			{
				//pr($post);
				// pr($f);
				// pr($redirect);
				// pr($email);
				// pr($subject);
				
				// формируем письмо и отправляем его
				
				if (!mso_valid_email($email)) 
					$email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
					
				$message = t('Имя: ') . $post['forms_name'] . "\n";
				$message .= t('Email: ') . $post['forms_email'] . "\n";
				
				foreach ($post['forms_fields'] as $key=>$val)
				{
					//pr($key);
					if ($key === 'subject' and $val)
					{
						$subject = $val;
						
						//pr($subject);
						continue;
					}
					
					$message .= $f[$key]['description'] . ': ' . $val . "\n\n";
				}
				
				if ($_SERVER['REMOTE_ADDR'] and $_SERVER['HTTP_REFERER'] and $_SERVER['HTTP_USER_AGENT']) 
				{
					$message .= "\n" . tf('IP-адрес: ') . $_SERVER['REMOTE_ADDR'] . "\n";
					$message .= tf('Отправлено со страницы: ') . $_SERVER['HTTP_REFERER'] . "\n";
					$message .= tf('Браузер: ') . $_SERVER['HTTP_USER_AGENT'] . "\n";
				}
				
				// pr($message);
				
				$form_hide = mso_mail($email, $subject, $message, $post['forms_email']);
				
				if ( $forms_subscribe and isset($post['forms_subscribe']) ) 
					mso_mail($post['forms_email'], tf('Вами отправлено сообщение:') . ' ' . $subject, $message);
				
				
				$out .= '<div class="message ok small">' . tf('Ваше сообщение отправлено!') . '</div><p>' 
						. str_replace("\n", '<br>', htmlspecialchars($subject. "\n" . $message)) 
						. '</p>';
				
				if ($redirect) mso_redirect($redirect, true);

			}
			else // какая-то ошибка, опять отображаем форму
			{
				$out .= forms_show_form($f, $ushka, $forms_subscribe, $reset, $subject);
			}
			
			
			$out .= '</div>';
			
			$out .= mso_load_jquery('jquery.scrollto.js');
			$out .= '<script>$(document).ready(function(){$.scrollTo("div.forms-post", 500);})</script>';

		}
		else // нет post
		{
			$out .= forms_show_form($f, $ushka, $forms_subscribe, $reset, $subject);
		}
	}

	return $out;
}

function forms_show_form($f = array(), $ushka = '', $forms_subscribe = true, $reset = true, $subject = '')
{
	$out = '';

	$antispam1 = rand(1, 10);
	$antispam2 = rand(1, 10);
	
	$id = 1; // счетчик для id label
	
	if ($subject)
	{
		// поле тема письма делаем в виде обязательнного поля select.
		
		// формируем массив для формы
		$subject_f['require'] = 1;
		
		// если в  subject есть #, то это несколько значений - select
		// если нет, значит обычное текстовое поле
		
		$subject_f['type'] = (mb_strpos($subject, '#') === false ) ? 'text' : 'select';
		
		// если это одиночное поле, но при этом текст сабжа начинается
		// с _ то ставим тип hidden
		if ($subject_f['type'] == 'text' and mb_strpos($subject, '_') === 0 ) 
		{
			$subject = mb_substr($subject . ' ', 1, -1, 'UTF-8');
			$subject_f['type'] = 'hidden'; 
		}
		
		$subject_f['description'] = tf('Тема письма');
		//$subject_f['tip'] = t('Выберите тему письма');
		$subject_f['values'] = $subject;
		$subject_f['value'] = $subject;
		$subject_f['default'] = '';
		
		// pr($subject_f);

		// преобразования, чтобы сделать ключ для поля 
		$f1['subject'] = $subject_f; // у поля тема будет ключ subject
		
		//pr($f1);
		
		foreach($f as $key=>$val) $f1[$key] = $val; 
		$f = $f1;
		
		//pr($f1);
	}

	$out .= NR . '<div class="forms"><form method="post" class="plugin_forms fform">' . mso_form_session('forms_session');
	
	$out .= '<input type="hidden" name="forms_antispam1" value="' . $antispam1 * 984 . '">';
	$out .= '<input type="hidden" name="forms_antispam2" value="' . $antispam2 * 765 . '">';
	
	// обязательные поля
	$out .= '<p><label class="ffirst ftitle" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . tf('Ваше имя*') . '</label><span><input name="forms_name" type="text" value="" placeholder="' . tf('Ваше имя') . '" required id="id-' . $id . '"></span></p>';
	
	$out .= '<p><label class="ffirst ftitle" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . tf('Ваш email*') . '</label><span><input name="forms_email" type="email" value="" placeholder="' . tf('Ваш email') . '" required id="id-' . $id . '"></span></p>';
	
	
	// тут указанные поля в $f
	// pr($f);
	foreach ($f as $key=>$val)
	{
		if (!isset($val['type'])) continue;
		if (!isset($val['description'])) $val['description'] = '';
		
		$val['type'] = trim($val['type']);
		$val['description'] = trim($val['description']);
		
		if (isset($val['require']) and  trim($val['require']) == 1) 
		{
			$require = '*';
			$require_title = ' title="' . tf('Обязательное поле') . '"';
			$required = ' required';
		}		
		else 
		{
			$require = '';
			$require_title = '';
			$required = '';
		}
		
		if (isset($val['attr']) and  trim($val['attr'])) $attr = ' ' . trim($val['attr']);
			else $attr = '';
		
		if (isset($val['value']) and  trim($val['value'])) $pole_value = htmlspecialchars(tf(trim($val['value'])));
			else $pole_value = '';
			
		if (isset($val['placeholder']) and  trim($val['placeholder'])) $placeholder = ' placeholder="' . htmlspecialchars(tf(trim($val['placeholder']))) . '"';
			else $placeholder = '';	
			
		$description = t(trim($val['description']));
		
		if (isset($val['tip']) and trim($val['tip']) ) $tip = NR . '<p class="nop"><span class="ffirst"></span><span class="fhint">'. trim($val['tip']) . '</span></p>';
			else $tip = '';
			
		if ($val['type'] == 'text') #####
		{
			//type_text - type для input HTML5
			if (isset($val['type_text']) and  trim($val['type_text'])) $type_text = htmlspecialchars(trim($val['type_text']));
				else $type_text = 'text';
			
			$out .= NR . '<p><label class="ffirst ftitle" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span><input name="forms_fields[' . $key . ']" type="' . $type_text . '" value="' . $pole_value . '" id="id-' . $id . '"' . $placeholder . $required . $attr . '></span></p>' . $tip;

		}
		elseif ($val['type'] == 'select') #####
		{
			if (!isset($val['default'])) continue;
			if (!isset($val['values'])) continue;
			
			$out .= NR . '<p><label class="ffirst ftitle" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span><select name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $attr . '>';
			
			$default = trim($val['default']);
			$values = explode('#', $val['values']);
			foreach ($values as $value)
			{
				$value = trim($value);
				
				if (!$value) continue; // пустые опции не выводим
				
				if ($value == $default) $checked = ' selected="selected"';
					else $checked = '';
				
				$out .= '<option' . $checked . '>' . htmlspecialchars(tf($value)) . '</option>';
			}
			
			$out .= '</select></span></p>' . $tip;

		}
		elseif ($val['type'] == 'textarea') #####
		{
			$out .= NR . '<p><label class="ffirst ftitle ftop" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span><textarea name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $placeholder . $required. $attr . '>' . $pole_value . '</textarea></span></p>' . $tip;
		
		}
		elseif ($val['type'] == 'hidden') #####
		{
			$out .= NR . '<input name="forms_fields[' . $key . ']" type="hidden" value="' . $pole_value . '" id="id-' . $id . '"' . $attr . '>';
		}
		
		
	}
	
	// обязательные поля антиспама и отправка и ресет
	$out .= NR . '<p><label class="ffirst ftitle" for="id-' . ++$id . '">' . $antispam1 . ' + ' . $antispam2 . ' =</label>';
	$out .= '<span><input name="forms_antispam" type="text" required maxlength="3" value="" placeholder="' . tf('Укажите свой ответ') . '" id="id-' . $id . '"></span><p>';
	
	if ($forms_subscribe)
		$out .= NR . '<p><span class="ffirst"></span><label><input name="forms_subscribe" value="" type="checkbox"  class="forms_checkbox"> ' . tf('Отправить копию письма на ваш e-mail') . '</label></p>';
	
	$out .= NR . '<p><span class="ffirst"></span><span class="submit"><button name="forms_submit" type="submit" class="forms_submit">' . tf('Отправить') . '</button>';
	
	if ($reset) $out .= ' <button name="forms_clear" type="reset" class="forms_reset">' . tf('Очистить форму') . '</button>';
	
	$out .= '</span></p>';
	
	if (function_exists('ushka')) $out .= ushka($ushka);
	
	$out .= '</form></div>' . NR;
	
	return $out;
}

# функции плагина
function forms_content($text = '')
{
	if (strpos($text, '[form]') !== false) $text = preg_replace_callback('!\[form\](.*?)\[/form\]!is', 'forms_content_callback', $text );
	return $text;
}

# end file