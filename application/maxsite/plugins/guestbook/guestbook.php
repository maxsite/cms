<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

global $MSO;

mso_head_meta('title', t('Гостевая книга') ); // meta title страницы


# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo '<div class="mso-page-only"><div class="mso-page-content mso-type-guestbook-content">';

$CI = & get_instance();

$options = mso_get_option('plugin_guestbook', 'plugins', array());

if ( !isset($options['fields_arr']) ) 
	$options['fields_arr'] = array('name' => t('Ваше имя:'), 'text' => t('Ваш отзыв:')); 

if ( isset($options['text']) ) echo $options['text']; // из опций смотрим текст перед всем
if ( !isset($options['limit']) ) $options['limit'] = 10; // отзывов на страницу
if ( !isset($options['email']) ) $options['email'] = false; // отправка на email
if ( !isset($options['moderation']) ) $options['moderation'] = 1; // модерация

// формат вывода
if ( !isset($options['format']) or !$options['format']) $options['format'] = '<div class="mso-guestbook">
<h5><b>[name]</b> - [date]</h5>
<p>[text]</p>
<hr>
</div>';

// текст до цикла
if ( !isset($options['start']) ) $options['start'] = '<h2>Отзывы</h2>';
 
// текст после цикла
if ( !isset($options['end']) ) $options['end'] = ''; 


$session = getinfo('session'); // текущая сессия 

// тут приём post
if ( $post = mso_check_post(array('f_session_id', 'f_submit_guestbook', 'f_fields_guestbook', 'f_guestbook_captha')) )
{
	mso_checkreferer();
	
	$captcha = $post['f_guestbook_captha']; // это введенное значение капчи
	
	$char = mso_md5($MSO->data['session']['session_id'] . mso_current_url());
	$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('1', '5', '8', '2', '7', '9'), $char);
	$char = substr($char, 1, 4);
	
	if ($captcha != $char) // не равны
	{ 
		echo '<div class="mso-message-error">' . t('Неверно введены нижние символы! Вернитесь назад и повторите попытку.') . '</div>';
		mso_flush_cache();
	}
	else
	{ 
		// прошла капча, можно добавлять отзыв
		
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
			echo '<div class="mso-message-ok">' . t('Ваш отзыв добавлен!');
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
		else echo '<div class="mso-message-error">' . t('Ошибка добавления в базу данных...') . '</div>';
		
		mso_flush_cache();
		
		// тут бы редирект, но мы просто убиваем сессию
		$CI->session->sess_destroy();
	}
}
else
{
	// тут форма, если не было post
	echo '<div class="mso-guestbook"><form method="post">' . mso_form_session('f_session_id');
	
	foreach( $options['fields_arr'] as $key => $val )
	{
		echo '<p><label><span>' . t($val) . '</span>';
		
		if ($key != 'text')
		{
			echo '<input name="f_fields_guestbook[' . $key . ']" type="text"></label></p>';
		}
		else
		{ 
			echo '<textarea name="f_fields_guestbook[' . $key . ']" rows="10"></textarea></label></p>';
		}
	}

	// капча из плагина капчи
	
	if (!function_exists('create_captha_img')) require_once(getinfo('plugins_dir') . 'captcha/index.php');
	
	$captcha = '<img src="' 
			. create_captha_img(mso_md5($MSO->data['session']['session_id'] . mso_current_url()))
			. '" title="' . t('Защита от спама: введите только нижние символы') . '">';
			
	echo '<p><label><span>' . t('Нижние символы:') . $captcha . '</span>
		<input type="text" name="f_guestbook_captha" value="" maxlength="4" required></label></p>';
	
	echo '<p><button type="submit" class="i submit" name="f_submit_guestbook">' . t('Отправить') . '</button></p>';
	
	echo '</form></div>';
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
			$tl = '<p><a href="' . getinfo('siteurl') . 'admin/guestbook/editone/' . $book['guestbook_id'] . '">'
				. t('Редактировать') . '</a></p>';
		else $tl = '';

		// pr($book);
		$out .= '<a id="guestbook-' . $book['guestbook_id'] . '"></a>';
		
		$guestbook_text = htmlspecialchars($book['guestbook_text']) . "\n";
		$guestbook_text = str_replace(array("\r\n", "\r"), "\n", $guestbook_text);
		$guestbook_text = preg_replace('!(.*)\n!', "<p>$1</p>", $guestbook_text);
		$guestbook_text = str_replace('<p></p>', "", $guestbook_text);
		
		// pr($guestbook_text, 1);
		
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
				'[custom5]',
				'[url]'
			), 
			array(
				$book['guestbook_id'],
				$book['guestbook_ip'],
				$book['guestbook_browser'],
				mso_date_convert('Y-m-d H:i:s', $book['guestbook_date']),
				htmlspecialchars($book['guestbook_name']),
				$guestbook_text,
				htmlspecialchars($book['guestbook_title']) . '&nbsp;',
				htmlspecialchars($book['guestbook_email']) . '&nbsp;',
				htmlspecialchars($book['guestbook_icq']) . '&nbsp;',
				htmlspecialchars($book['guestbook_site']) . '&nbsp;',
				htmlspecialchars($book['guestbook_phone']) . '&nbsp;',
				htmlspecialchars($book['guestbook_custom1']) . '&nbsp;',
				htmlspecialchars($book['guestbook_custom2']) . '&nbsp;',
				htmlspecialchars($book['guestbook_custom3']) . '&nbsp;',
				htmlspecialchars($book['guestbook_custom4']) . '&nbsp;',
				htmlspecialchars($book['guestbook_custom5']) . '&nbsp;',
				getinfo('siteurl') . 'guestbook#guestbook-' . $book['guestbook_id'] // http://site/guestbook#guestbook-164
			), $options['format']);
	}
	if ($out) echo $options['start'] . $out . $options['end'];
}

// здесь пагинация
mso_hook('pagination', $pag);

echo '</div></div>';

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file