<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// Функции которые выполняют роль подсчета количества прочтения записи
// первая функция, проверяет из куки значение массива с текущим url
// если номера не совпадают, то функция устанавливает значение прочтений больше на 1
// если совпадают, значит запись уже была прочитана с этого компа
// если нужно убрать уникальность и учитывать все хиты, то $unique = false
// начения хранятся в виде url1|url2|url2|url3
// url - второй сегмент
// время жизни 30 дней: 60 секунд * 60 минут * 24 часа * 30 дней = 2592000
function mso_page_view_count_first($unique = false, $name_cookies = 'maxsite-cms', $expire = 2592000)
{
	// global $_COOKIE, $_SESSION;

	if (!mso_get_option('page_view_enable', 'templates', '1') and !$unique) return true; //если нет такой опции или не пришло в функцию, то выходим
	if (!$unique) $unique = mso_get_option('page_view_enable', 'templates', '1');

	$slug = mso_segment(2);
	$all_slug = [];

	if ($unique == 0) {
		return false; // не вести подсчет
	} elseif ($unique == 1) {
		//с помощью куки
		if (isset($_COOKIE[$name_cookies]))	$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
		if (in_array($slug, $all_slug)) return false; // уже есть текущий урл - не увеличиваем счетчик
	} elseif ($unique == 2) {
		//с помощью сессии
		session_start();

		if (isset($_SESSION[$name_cookies]))
			$all_slug = explode('|', $_SESSION[$name_cookies]); // значения текущей сессии

		if (in_array($slug, $all_slug)) return false; // уже есть текущий урл - не увеличиваем счетчик
	}

	// нужно увеличить счетчик
	$all_slug[] = $slug; // добавляем текущий slug
	$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
	$all_slug = implode('|', $all_slug); // соединяем обратно в строку
	$expire = time() + $expire;

	if ($unique == 1) @setcookie($name_cookies, $all_slug, $expire); // записали в кук
	elseif ($unique == 2) $_SESSION[$name_cookies] = $all_slug; // записали в сессию

	// получим текущее значение page_view_count
	// и увеличиваем значение на 1
	$CI = &get_instance();
	$CI->db->select('page_view_count');

	if (is_numeric($slug)) // ссылка вида http://site.com/page/1 
		$CI->db->where('page_id', $slug);
	else
		$CI->db->where('page_slug', $slug);

	$CI->db->limit(1);
	$query = $CI->db->get('page');

	if ($query->num_rows() > 0) {
		$pages = $query->row_array();
		$page_view_count = $pages['page_view_count'] + 1;

		$CI->db->where('page_slug', $slug);
		$CI->db->update('page', array('page_view_count' => $page_view_count));
		$CI->db->cache_delete('page', $slug);

		return true;
	}
}

// вывод количества просмотров текущей записи
function mso_page_view_count($page_view_count = 0, $do = '<span>Прочтений:</span> ', $posle = '', $echo = true)
{
	if (!$page_view_count) return '';

	// если в опции включено не вести подсчет, то блок не выводим
	if (mso_get_option('page_view_enable', 'templates', 0) == 0) return '';

	if ($echo)
		echo tf($do) . $page_view_count . $posle;
	else
		return tf($do) . $page_view_count . $posle;
}

# end of file
