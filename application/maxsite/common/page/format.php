<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// переменную $page мы объявляем как глобальную - в ней содержится массив текущей выводимой из type-файла страницы
// эта $page — foreach цикл вывода в type/page (и т.п.)
// вместо этого нужно использовать $pageData = mso_get_val('mso_pages', 0, true);
global $page;

// получить ссылку на редактирование страницы
function mso_page_edit_link($id = 0, $title = 'Редактировать', $do = '', $posle = '', $echo = true)
{
	$id = (int) $id;

	if (!$id) return '';
	
	$out = '';

	if (is_login()) 
		$out = $do . '<a href="' . getinfo('site_admin_url') . 'page_edit/' . $id . '">' . tf($title) . '</a>' . $posle;
	
	if ($echo) 
		echo $out;
	else
		return $out;
}

// получить ссылки на рубрики указанной страницы
function  mso_page_cat_link($cat = [], $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'category', $link = true)
{
	if (!$cat) return '';

	// получим массив рубрик из mso_cat_array_single
	$all_cat = mso_cat_array_single();

	$out = '';

	if ($type) $type .= '/';

	foreach ($cat as $id) {
		if ($link) {
			$out .=  '<a href="'
				. getinfo('site_url')
				. $type
				. $all_cat[$id]['category_slug']
				. '">'
				. htmlspecialchars($all_cat[$id]['category_name'])
				. '</a>   ';
		} else
			$out .= htmlspecialchars($all_cat[$id]['category_name']) . '   ';
	}

	$out = trim($out);
	$out = str_replace('   ', $sep, $out);

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

// получить ссылки на метки указанной страницы
function mso_page_tag_link($tags = [], $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'tag', $link = true, $class = '')
{
	if (!$tags) return '';
	if ($class) $class = ' class="' . $class . '"';

	$out = '';

	if ($type) $type .= '/';

	foreach ($tags as $tag) {
		if ($link) {
			$out .=  '<a' . $class . ' href="'
				. getinfo('site_url')
				. $type
				. urlencode($tag)
				. '" rel="tag">'
				. htmlspecialchars($tag)
				. '</a>   ';
		} else
			$out .=  htmlspecialchars($tag) . '   ';
	}

	$out = trim($out);
	$out = str_replace('   ', $sep, $out);

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

// получение даты
function mso_page_date($date = 0, $format = 'Y-m-d H:i:s', $do = '', $posle = '', $echo = true)
{
	if (!$date) return '';

	if (is_array($format)) // формат в массиве, значит там и замены
	{
		if (isset($format['format']))
			$df = $format['format'];
		else
			$df = 'D, j F Y г.';

		if (isset($format['days']))
			$dd = $format['days'];
		else
			$dd = tf('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье');

		if (isset($format['month']))
			$dm = $format['month'];
		else
			$dm = tf('января февраля марта апреля мая июня июля августа сентября октября ноября декабря');
	} else {
		$df = $format;
		$dd = false;
		$dm = false;
	}

	// учитываем смещение времени time_zone
	$out = mso_date_convert($df, $date, true, $dd, $dm);

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

// функция формирует http-адрес страницы по slug и type
function mso_page_url($page_slug = '', $type = 'page')
{
	if (!$page_slug) return '';
	if ($type) $type .= '/';

	return getinfo('site_url') . $type . $page_slug;
}

// формирование титла или ссылки на страницу
function mso_page_title($page_slug = '', $page_title = 'no title', $do = '<h1>', $posle = '</h1>', $link = true, $echo = true, $type = 'page')
{
	if (!$page_slug) return '';

	if ($link) {
		if ($type) $type .= '/';

		$out = '<a href="' . getinfo('site_url') . $type . $page_slug . '" title="' . htmlspecialchars($page_title) . '">' . htmlspecialchars($page_title) . '</a>';
	} else
		$out = htmlspecialchars($page_title);

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

// формирование ссылки для rss страницы
function mso_page_feed($page_slug = '', $page_title = 'Подписаться', $do = '<p>', $posle = '</p>', $link = true, $echo = true, $type = 'page')
{
	if (!$page_slug) return '';

	if ($link) {
		if ($type) $type .= '/';
		$out = '<a href="' . getinfo('site_url') . $type . $page_slug . '/feed">' . tf($page_title) . '</a>';
	} else
		$out = $page_title;

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

// вывод текста
function mso_page_content($page_content = '', $use_password = true, $message = 'Данная запись защищена паролем.')
{
	global $page;

	mso_hook('content_start'); // хук на начало блока

	$page_password = $page['page_password'] ?? '';
	
	if ($use_password and $page_password) // есть пароль
	{
		$form = '';

		$_message = tf($message);
		$_pass = tf('Пароль:');
		$_id = $page['page_id'];
		$_sess = mso_form_session('f_session_id');

		// можно переписать форму - файл должен вернуть переменную $form 
		if ($fn = mso_find_ts_file('type/page/units/page-password-content-form.php')) {
			require $fn;
		} else {
			$form = '<h5>' . $_message . '</h5>';
			$form .= '<form class="mso-form" method="post">' . $_sess;
			$form .= '<input type="hidden" name="f_page_id" value="' . $_id . '">';
			$form .= '<div>' . $_pass . ' <input type="text" name="f_password" value="" required> ';
			$form .= '<button type="submit" name="f_submit">OK</button></div>';
			$form .= '</form>';
		}

		// возможно пароль уже был отправлен
		if ($post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_id', 'f_password'))) {
			mso_checkreferer();

			$f_page_id = (int) $post['f_page_id']; // номер записи
			$f_password = $post['f_password']; // пароль

			if ($f_page_id == $page['page_id'] and $f_password == $page['page_password']) {
				// верный пароль
				$page['page_password_ok'] = true;
				echo mso_hook('content_content', $page_content);
			} else {
				// ошибка в пароле
				echo '<p class="mso-message-alert">' . tf('<strong>Ошибочный пароль!</strong> Повторите ввод.') . '</p>' . $form;
			}
		} else {
			// нет post, выводим форму
			echo $form;
		}
	} else {
		// нет пароля
		echo mso_hook('content_content', $page_content);
	}
}

// некоторые плагины нужно выводить после всех хуков на content
function mso_page_content_end()
{
	mso_hook('content_end'); // хук на конец блока
}

// получение meta
function mso_page_meta($meta = '', $page_meta = [], $do = '', $posle = '', $razd = ', ', $echo = true)
{
	if ($out = mso_page_meta_value($meta, $page_meta, '', $razd)) {
		if ($echo)
			echo $do . $out . $posle;
		else
			return $do . $out . $posle;
	} else {
		return '';
	}
}

// получение значение meta
// если нет, то отдается $default
function mso_page_meta_value($meta = '', $page_meta = [], $default = '', $razd = ', ')
{
	if (!$meta or !$page_meta) return $default;

	if (isset($page_meta[$meta]) and $page_meta[$meta]) {
		$out = '';

		foreach ($page_meta[$meta] as $val) {
			$out .= $val . '     ';
		}

		$out = trim($out);

		if (!$out) {
			if ($out != 0) return $default;
		}

		return str_replace('     ', $razd, $out);
	} else {
		return $default;
	}
}

// формирование ссылки «обсудить» если разрешен комментарий
function mso_page_comments_link($page_comment_allow = true, $page_slug = '', $title = 'Обсудить', $do = '', $posle = '', $echo = true, $type = 'page')
{
	if ($type) $type .= '/';

	if (is_array($page_comment_allow)) {
		// первый элемент - массив, значит принимаем его значения - остальное игнорируем
		$def = [
			'page_comment_allow' => true, // разрешены комментарии?
			'page_slug' => '', // короткая ссылка страницы
			'title' => tf('Обсудить'), // титул, если есть ссылка
			'title_no_link' => tf('Посмотреть комментарии'), // титул если ссылки нет
			'title_no_comments' => tf('Обсудить'), // титул если еще нет комментариев
			'do' => '', // текст ДО
			'posle' => '', // текст ПОСЛЕ
			'echo' => true, // выводить?
			'page_count_comments' => 0 // колво комментов
		];

		$r = array_merge($def, $page_comment_allow); // объединяем дефолт с входящим

		if (!$r['page_slug']) return ''; // не указан slug - выходим

		if (isset($r['type'])) {
			// если тип передан в массиве
			if ($r['type'])
				$type = $r['type'] . '/';
			else
				$type = false;
		}

		$out = '';

		if (!$r['page_comment_allow']) {
			// коментирование запрещено
			if ($r['page_count_comments']) {
				// но если уже есть комментарии, то выводи строчку title_no_link
				$out = $r['do'] . '<a href="' . getinfo('site_url') . $type
					. $r['page_slug'] . '#comments">' . $r['title_no_link'] . '</a>' . $r['posle'];
			}
		} else {

			if (!$r['page_count_comments']) {
				// если нет комментариев, то выводим строчку title_no_link
				// если запрещены комментарии от всех, если их нет, не выводим ссылку ОБСУДИТЬ
				if (mso_get_option('allow_comment_comusers', 'general', '1') or mso_get_option('allow_comment_anonim', 'general', '1')) {
					$out = $r['do'] . '<a href="' . getinfo('site_url') . $type
						. $r['page_slug'] . '#comments">' . tf($r['title_no_comments']) . '</a>' . $r['posle'];
				}
			} else
				$out = $r['do'] . '<a href="' . getinfo('site_url') . $type
					. $r['page_slug'] . '#comments">' . tf($r['title']) . '</a>' . $r['posle'];
		}


		if ($r['echo'])
			echo $out;
		else
			return $out;
	} else {
		// обычные параметры
		if (!$page_slug) return '';
		if (!$page_comment_allow) return '';

		$out = $do . '<a href="' . getinfo('site_url') . $type . $page_slug . '#comments">' . tf($title) . '</a>' . $posle;

		if ($echo)
			echo $out;
		else
			return $out;
	}
}

// получить ссылкe на автора страницы
function mso_page_author_link($users_nik = '', $page_id_autor = '', $do = '', $posle = '', $echo = true, $type = 'author', $link = true)
{
	if (!$users_nik or !$page_id_autor) return '';

	$out = '';

	if ($link) {
		if ($type) $type .= '/';

		$out .=  '<a href="'
			. getinfo('site_url')
			. $type
			. $page_id_autor
			. '">'
			. $users_nik
			. '</a>';
	} else {
		$out .= $users_nik;
	}

	if ($echo)
		echo $do . $out . $posle;
	else
		return $do . $out . $posle;
}

# end of file
