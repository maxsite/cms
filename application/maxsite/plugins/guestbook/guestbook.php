<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_head_meta('title', t('Гостевая книга') ); // meta title страницы

// стили свои подключим
mso_hook_add('head', 'guestbook_css');

function guestbook_css($a = array())
{
	if (file_exists(getinfo('template_dir') . 'guestbook.css')) $css = getinfo('stylesheet_url') . 'guestbook.css';
		else $css = getinfo('plugins_url') . 'guestbook/guestbook.css';
		
	echo '<link rel="stylesheet" href="' . $css . '" type="text/css" media="screen">' . NR;
	
	return $a;
}

# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

$CI = & get_instance();

$options = mso_get_option('plugin_guestbook', 'plugins', array());

if ( !isset($options['fields_arr']) ) 
	$options['fields_arr'] = array('name' => t('Ваше имя:'), 'text' => t('Ваш отзыв:')); 

if ( isset($options['text']) ) echo $options['text']; // из опций смотрим текст перед всем
if ( !isset($options['limit']) ) $options['limit'] = 10; // отзывов на страницу
if ( !isset($options['email']) ) $options['email'] = false; // отправка на email
if ( !isset($options['moderation']) ) $options['moderation'] = 1; // модерация
// формат вывода
if ( !isset($options['format']) ) $options['format'] = '<tr><td colspan="2" class="header"><a id="guestbook-[id]"></a>[date]</td></tr>
<tr><td class="t1"><b>Имя:</b></td><td class="t2">[name]</td></tr>
<tr><td class="t1"><b>Текст:</b></td><td class="t2">[text]</td></tr>
<tr><td colspan="2" class="space">&nbsp;</td></tr>'; 

// текст до цикла
if ( !isset($options['start']) ) $options['start'] = '<h2 class="guestbook">Отзывы</h2><table class="guestbook">';
 
// текст после цикла
if ( !isset($options['end']) ) $options['end'] = '</table>'; 


$session = getinfo('session'); // текущая сессия 

// тут приём post
if ( $post = mso_check_post(array('f_session_id', 'f_submit_guestbook', 'f_fields_guestbook', 'f_guestbook_captha')) )
{
	mso_checkreferer();
	
	$captcha = $post['f_guestbook_captha']; // это введенное значение капчи
	// которое должно быть вычисляем как и в img.php
	$char = md5($session['session_id'] . mso_slug(mso_current_url()));
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
	$char = substr( $char, 1, 4);
	if ($captcha != $char)
	{ // не равны
		echo '<div class="error">' . t('Привет роботам!') . '</div>';
		mso_flush_cache();
	}
	else
	{ // прошла капча, можно добавлять отзыв
		
		// pr($post);
		
		// данные для новой записи
		$ins_data = array (
			'guestbook_date' => date('Y-m-d H:i:s'),
			'guestbook_ip' => $session['ip_address'],
			'guestbook_browser' => $session['user_agent'],
			);
		
		if ( $options['moderation'] ) $ins_data['guestbook_approved'] = 0; // нужна модерация
		else $ins_data['guestbook_approved'] = 1; // сразу одобряем 
		
		// отправленные поля
		// сразу готовим для email
		$text_email = ''; 
		foreach( $options['fields_arr'] as $key => $val )
		{
			if ( isset($post['f_fields_guestbook'][$key]) ) 
			{
				$ins_data['guestbook_' . $key] = $post['f_fields_guestbook'][$key];
				$text_email .= $key . ': ' . $post['f_fields_guestbook'][$key] . "\n";
			}
		}
		

		// pr($ins_data);
		
		$res = ($CI->db->insert('guestbook', $ins_data)) ? '1' : '0';
		
		if ($res)
		{
			echo '<div class="update">' . t('Ваш отзыв добавлен!');
			if ( $options['moderation'] ) echo ' ' . t('Он будет опубликован после одобрения модератором.');
			echo '</div>';
			
			$text_email = t("Новая запись в гостевой книге") . ": \n" . $text_email;
			$text_email .= "\n" . t("Редактировать") . ": " . getinfo('siteurl') . 'admin/guestbook/editone/' 
						. $CI->db->insert_id() . "\n";
			
			if ( $options['email'] and mso_valid_email($options['email']) ) 
			{
				mso_mail($options['email'], t('Новая запись в гостевой книге'), $text_email);
			}
			
		}
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...') . '</div>';
		
		mso_flush_cache();
		
		// тут бы редирект, но мы просто убиваем сессию
		$CI->session->sess_destroy();
	}
}
else
{
	// тут форма, если не было post
	echo '<div class="guestbook_form"><form action="" method="post">' . mso_form_session('f_session_id');
	
	echo '<table style="width: 100%;">';
	
	foreach( $options['fields_arr'] as $key => $val )
	{
		echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t($val) . '</strong> </td><td class="td2">';
		
		if ($key != 'text')
		{
			echo '<input name="f_fields_guestbook[' . $key . ']" type="text" style="width: 99%;"></td></tr>';
		}
		else
		{ 
			echo '<textarea name="f_fields_guestbook[' . $key . ']" style="width: 99%; height: 100px;"></textarea></td></tr>';
		}
	}

	// капча из плагина капчи
	
	echo '<tr><td style="vertical-align: top; text-align: right;" class="td1"><strong>' . t('Введите нижние символы') . ' </td>
			<td style="text-align: left;" class="td2"><input type="text" name="f_guestbook_captha" value="" maxlength="4"> <img src="' 
			. getinfo('plugins_url') . 'captcha/img.php?image='
			. $session['session_id']
			. '&page='
			. mso_slug(mso_current_url())
			. '&code='
			. time()
			. '" title="' . t('Защита от спама: введите только нижние символы') . '" align="absmiddle"></td></tr>';

	
	echo '<tr><td class="td1">&nbsp;</td><td style="vertical-align: top; text-align: left;" class="td2"><input type="submit" class="submit" name="f_submit_guestbook" value="' . t('Отправить') . '"></td></tr>';
	
	echo '</table></form></div>';
}


// тут последние отзывы с пагинацией
// нам нужна все поля таблицы
// вначале определим общее количество записей
$pag = array(); // пагинация
$pag['limit'] = $options['limit']; // записей на страницу
$pag['type'] = ''; // тип

$CI->db->select('guestbook_id');
$CI->db->from('guestbook');
$CI->db->where('guestbook_approved', '1');
$query = $CI->db->get();
$pag_row = $query->num_rows();

if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']); // всего станиц пагинации

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}

// теперь получаем сами записи
$CI->db->from('guestbook');
$CI->db->where('guestbook_approved', '1');
$CI->db->order_by('guestbook_date', 'desc');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
	else $CI->db->limit($pag['limit']);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	$out = '';
	foreach ($books as $book) 
	{
		if (is_login()) 
			$tl = '<br><a href="' . getinfo('siteurl') . 'admin/guestbook/editone/' . $book['guestbook_id'] . '">'
				. t('Редактировать') . '</a>';
		else $tl = '';

		// pr($book);
		$out .= '<a id="guestbook-' . $book['guestbook_id'] . '"></a>';
		$out .= str_replace( 
			array(
				'[id]', 
				'[ip]', 
				'[browser]', 
				'[date]', 
				'[name]', 
				'[text]', 
				'[title]', 
				'[email]', 
				'[icq]', 
				'[site]', 
				'[phone]', 
				'[custom1]', 
				'[custom2]', 
				'[custom3]', 
				'[custom4]', 
				'[custom5]'), 
			array(
				$book['guestbook_id'],
				$book['guestbook_ip'],
				$book['guestbook_browser'],
				mso_date_convert('Y-m-d H:i:s', $book['guestbook_date']),
				htmlspecialchars($book['guestbook_name']),
				str_replace("\n", "<br>", htmlspecialchars($book['guestbook_text']) . $tl),
				htmlspecialchars($book['guestbook_title']),
				htmlspecialchars($book['guestbook_email']),
				htmlspecialchars($book['guestbook_icq']),
				htmlspecialchars($book['guestbook_site']),
				htmlspecialchars($book['guestbook_phone']),
				htmlspecialchars($book['guestbook_custom1']),
				htmlspecialchars($book['guestbook_custom2']),
				htmlspecialchars($book['guestbook_custom3']),
				htmlspecialchars($book['guestbook_custom4']),
				htmlspecialchars($book['guestbook_custom5'])
			), $options['format']);
	}
	if ($out) echo $options['start'] . $out . $options['end'];
}

// здесь пагинация
mso_hook('pagination', $pag);

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
?>