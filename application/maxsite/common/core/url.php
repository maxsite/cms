<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// получаем название указанного сегменту текущей страницы
// http://localhost/admin/users/edit/1
// mso_segment(3) -> edit
// номер считается от home-сайта
// если в сегменте находится XSS и $die = true, то рубим все
// можно проверить сегмент в своём массиве $my_segments
function mso_segment($segment = 2, $die = true, $my_segments = false)
{
	global $MSO;

	$CI = &get_instance();

	if ($my_segments === false) $my_segments = $MSO->data['uri_segment'];

	if (count($my_segments) > ($segment - 1))
		$seg = $my_segments[$segment];
	else
		$seg = '';

	$seg = urldecode($seg);

	// $url = $CI->input->xss_clean($seg); // ci < 2
	$url = $CI->security->xss_clean($seg, false);

	if ($url != $seg and $die) die('<b><font color="red">Achtung! XSS attack!</font></b>');

	return $url;
}

// получение текущих сегментов url в массив
// в отличие от CodeIgniter - происходит анализ get и отсекание до «?»
// если «?» нет, то возвращает стандартное $this->uri->segment_array();
function mso_segment_array()
{
	$CI = &get_instance();

	if (isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI']) {
		// http://localhost/page/privet?get=hello
		$url = getinfo('site_protocol');
		$url .= $_SERVER['HTTP_HOST'] . mso_clean_str($_SERVER['REQUEST_URI']);
		$url = str_replace($CI->config->config['base_url'], '', $url); // page/privet?get=hello

		if (strpos($url, '?') !== FALSE) {
			// есть «?»
			$url = explode('?', $url); // разделим в массив
			$url = $url[0]; // сегменты - это только первая часть
			$url = explode('/', $url); // разделим в массив по /

			// нужно изменить нумерацию - начало с 1
			$out = [];
			$i = 1;
			
			foreach ($url as $val) {
				if ($val) {
					$out[$i] = $val;
					$i++;
				}
			}

			return $out;
		} else {
			return $CI->uri->segment_array();
		}
	} else {
		return $CI->uri->segment_array();
	}
}

// получение get-строки из текущего адреса
function mso_url_get()
{
	$CI = &get_instance();
	if (isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] and (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE)) {
		$url = getinfo('site_protocol') . $_SERVER['HTTP_HOST'] . mso_clean_str($_SERVER['REQUEST_URI']);
		$url = str_replace($CI->config->config['base_url'], "", $url);
		$url = explode('?', $url);

		return $url[1];
	} else {
		return '';
	}
}

// функция преобразования get-строки в массив
// разделитель элементов массива & или &amp;
// значение через стандартную parse_str
function mso_parse_url_get($s = '')
{
	if ($s) {
		$s = str_replace('&amp;', '&', $s);
		$s = explode('&', $s);
		$uri_get_array = [];

		foreach ($s as $val) {
			parse_str($val, $arr);

			foreach ($arr as $key1 => $val1) {
				$uri_get_array[$key1] = $val1;
			}
		}

		return $uri_get_array;
	} else {
		return [];
	}
}

// получить текущую страницу пагинации
// next - признак сегмент после которого указывается номер страницы
function mso_current_paged($next = 'next')
{
	// global $MSO;

	$uri = mso_current_url(false, true);

	// это чтобы нумерация совпадала с $MSO->data['uri_segment'] с 1
	array_unshift($uri, '');
	unset($uri[0]);

	if ($n = mso_array_get_key_value($uri, $next)) {
		if (isset($uri[$n + 1]))
			$n = (int) $uri[$n + 1];
		else
			$n = 1;

		if ($n > 0)
			$current_paged = $n;
		else
			$current_paged = 1;
	} else {
		$current_paged = 1;
	}

	return $current_paged;
}

// увеличение или уменьшение ссылки next на указанную величину $inc
// http://site.com/home/next/2 -> +1 -> http://site.com/home/next/3
// $url - исходный адрес (относительно сайта). Если адрес = пустой строке, 
//	то берем mso_current_url Если первый сегмент пустой, то это home
// $inc - величина изменения
// $max - максимум - если $inc + текущий > $max, то ставится $max. Если $max = false, то он не учитывается
// $min - минимальное значение
// $next - признак сегмент после которого указывается номер страницы
// $empty_no_range = true - отдает пустую строчку, если текущая paged будет равна конечной
// если $empty_no_range = false, то отдаем ссылку как обычно
function mso_url_paged_inc($max = false, $inc = 1, $empty_no_range = true, $url = '', $min = 1, $next = 'next')
{
	if (!$url) $url = mso_current_url();

	$current_paged = mso_current_paged($next);
	$result_paged = $current_paged + $inc;

	if ($max) if ($result_paged > $max) $result_paged = $max;
	if ($result_paged < $min) $result_paged = $min;
	if ($empty_no_range and $result_paged == $current_paged) return '';

	// если нет $url, то это главная
	if (!$url) $url = 'home/' . $next . '/' . $current_paged;

	if (strpos($url, $next . '/') === false) // нет вхождения next/ - нужно добавить
		$url .= '/' . $next . '/' . $current_paged;

	// если $result_paged = , то $min, то	$next не пишем
	if ($result_paged == $min)
		$url = str_replace($next . '/' . $current_paged, '', $url);
	else
		$url = str_replace($next . '/' . $current_paged, $next . '/' . $result_paged, $url);

	// удалим последние слэши
	$url = trim(str_replace('/', ' ', $url));
	$url = str_replace(' ', '/', $url);

	if ($url == 'home') $url = '';

	return getinfo('siteurl') . $url;
}

// получить пермалинк рубрики по указанному слагу
function mso_get_permalink_cat_slug($slug = '', $prefix = 'category/')
{
	if (!$slug) return '';

	return  getinfo('siteurl') . $prefix . $slug;
}

// получить пермалинк страницы по её id через запрос БД
function mso_get_permalink_page($id = 0, $prefix = 'page/')
{
	global $MSO;

	$id = (int) $id;
	if (!$id) return '';

	$CI = &get_instance();
	$CI->db->select('page_slug, page_id');
	$CI->db->where(array('page_id' => $id));

	$query = $CI->db->get('page');

	if ($query->num_rows() > 0) {
		foreach ($query->result_array() as $row) {
			$slug = $row['page_slug'];
		}

		return  $MSO->config['site_url'] . $prefix . $slug;
	} else {
		return '';
	}
}

// получение текущего url относительно сайта
// ведущий и конечные слэши удаляем
// если $absolute = true, то возвращается текущий урл как есть
function mso_current_url($absolute = false, $explode = false, $delete_request = false)
{
	$url = getinfo('site_protocol') . $_SERVER['HTTP_HOST'] . mso_clean_str($_SERVER['REQUEST_URI']);
	
	//pr($url);

	if ($delete_request) {
		// отделим по «?»
		$url = explode('?', $url);
		$url = $url[0];
	}

	if ($absolute) return $url;

	$url = str_replace(getinfo('site_url'), '', $url);
	$url = trim(str_replace('/', ' ', $url));
	$url = str_replace(' ', '/', $url);
	$url = urldecode($url);

	if ($explode) $url = explode('/', $url);

	return $url;
}

// редирект на страницу сайта. путь указывать относительно сайта
// если $absolute = true - переход по указаному пути
// $header - 301 или 302 редирект
function mso_redirect($url = '', $absolute = false, $header = false)
{
	$url = strip_tags($url);
	$url = str_replace(array('%0d', '%0a'), '', $url);

	$url = mso_xss_clean($url);

	if ($header == 301) header('HTTP/1.1 301 Moved Permanently');
	elseif ($header == 302) header('HTTP/1.1 302 Found');

	if ($absolute) {
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	} else {
		$url = getinfo('site_url') . $url;
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	}
	
	exit();
}

# end of file
