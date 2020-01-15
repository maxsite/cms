<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// вывод списка страниц по паренту - навигация под страницами - все связанные
function mso_page_nav($page_id = 0, $page_id_parent = 0, $echo = false)
{
	$r = mso_page_map($page_id, $page_id_parent); // построение карты страниц
	$r = mso_create_list($r); // создание ul-списка

	if ($echo)
		echo $r;
	else
		return $r;
}

// вывод карты страниц по паренту - готовый массив с вложениями с childs=>...
// функция ресурсоемкая!
function mso_page_map($page_id = 0, $page_id_parent = 0)
{
	$cache_key = 'mso_page_map' . $page_id . '-' . $page_id_parent;
	$k = mso_get_cache($cache_key);

	if ($k) return $k; // да есть в кэше

	$CI = &get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug');

	if ($page_id) {
		$CI->db->where('page_id', $page_id);
		$CI->db->where('page_id_parent', '0');
		$CI->db->where('page_status', 'publish');
		$CI->db->where('page_date_publish < ', date('Y-m-d H:i:s'));
		$CI->db->or_where('page_id', $page_id_parent);
	}

	$CI->db->order_by('page_menu_order');

	$query = $CI->db->get('page');
	$result = $query->result_array(); // здесь все страницы

	foreach ($result as $key => $row) {
		$k = $row['page_id'];
		$r[$k] = $row;
		if ($k == $page_id) $r[$k]['current'] = 1;

		$ch = _mso_page_map_get_child($row['page_id'], $page_id);

		if ($ch) $r[$k]['childs'] = $ch;
	}

	if (!isset($r[$k]['childs'])) $r = []; // в итоге нет детей у первого элемента, все обнуляем

	mso_add_cache($cache_key, $r); // в кэш

	return $r;
}

// вспомогательная рекурсивная рубрика для получения всех потомков страницы
function _mso_page_map_get_child($page_id = 0, $cur_id = 0)
{
	$CI = &get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug');
	$CI->db->where('page_id_parent', $page_id);
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	$CI->db->order_by('page_menu_order');

	$query = $CI->db->get('page');
	$result = $query->result_array(); // здесь все рубрики

	if ($result) {
		$r0 = [];

		foreach ($result as $key => $row) {
			$k = $row['page_id'];
			$r0[$k] = $row;

			if ($k == $cur_id) $r0[$k]['current'] = 1;
		}

		$result = $r0;

		foreach ($result as $key => $row) {
			$r = _mso_page_map_get_child($row['page_id'], $cur_id);

			if ($r) $result[$key]['childs'] = $r;
		}
	}

	return $result;
}

// получить следующую и предыдущую запись после указанной
// возвращает масив $out['prev'] и $out['next'] с данными записей 
// Если записей нет, то ключи равны false
function mso_next_prev_page($r = [])
{
	// TODO: переделать: Если есть $next_id и $prev_id, то просто 
	// получаем записи, без прочих условий	
	// $par сделать единый вариант

	// TODO: разбраться с этой глобальной $page
	global $page;

	$out = [];

	$out['next'] = false;
	$out['prev'] = false;

	// если это page, то смотрим глобальную $page, где есть мета 
	// next_page_id и prev_page_id — id страниц на далее и ранее
	$next_id = (isset($page['page_meta']['next_page_id'][0]) and $page['page_meta']['next_page_id'][0]) ? (int) $page['page_meta']['next_page_id'][0] : 0;

	$prev_id = (isset($page['page_meta']['prev_page_id'][0]) and $page['page_meta']['prev_page_id'][0]) ? (int) $page['page_meta']['prev_page_id'][0] : 0;

	if (!isset($r['page_id']) or !isset($r['page_categories']) or !isset($r['page_date_publish'])) {
		return $out;
	}

	// передаем дату отдельно, она используется в function_add_custom_sql
	mso_set_val('_sql_next_prev_pages_page_date_publish', $r['page_date_publish']);

	// $r['use_category'] — если нужно учитывать рубрику
	if (!isset($r['use_category'])) $r['use_category'] = true;
	if (!isset($r['get_page_categories'])) $r['get_page_categories'] = false;
	if (!isset($r['get_page_meta_tags'])) $r['get_page_meta_tags'] = false;

	$cat = ''; // рубрика не учитывается

	if ($r['use_category']) {
		// рубрики
		$cat = $r['page_categories'];

		// если несколько рубрик, то ищем те, в которых
		// category_id_parent не равен 0 — это подрубрики
		// если такие есть, то по ним и делаем навигаци
		if (count($r['page_categories']) > 1) {
			$all_cat = mso_cat_array_single(); // все рубрики

			foreach ($r['page_categories'] as $id) {
				if ($all_cat[$id]['parents'] > 0) {
					$cat = array($id);
					break;
				}
			}
		}

		$cat = implode(',', $cat);
	}

	if (!isset($r['type'])) $r['type'] = 'blog'; // можно задать тип записей

	// next
	$par = [
		'content' => false,
		'cat_id' => $cat,
		'order_asc' => 'asc',
		'limit' => 1,
		'pagination' => false,
		'work_cut' => false,
		'custom_type' => 'home',
		'function_add_custom_sql' => '_sql_next_page',
		'get_page_categories' => $r['get_page_categories'],
		'get_page_meta_tags' => $r['get_page_meta_tags'],
		'get_page_count_comments' => false,
		'type' => $r['type'],
		'exclude_page_id' => $r['page_id']
	];

	if ($next_id) {
		unset($par['cat_id']);
		unset($par['exclude_page_id']);
		unset($par['function_add_custom_sql']);
		$par['page_id'] = $next_id;
	}

	if ($pages = mso_get_pages($par, $temp)) $out['next'] = $pages[0];

	// prev
	$par = [
		'content' => false,
		'cat_id' => $cat,
		'order_asc' => 'desc',
		'limit' => 1,
		'pagination' => false,
		'work_cut' => false,
		'custom_type' => 'home',
		'function_add_custom_sql' => '_sql_prev_page',
		'get_page_categories' => $r['get_page_categories'],
		'get_page_meta_tags' => $r['get_page_meta_tags'],
		'get_page_count_comments' => false,
		'type' => $r['type'],
		'exclude_page_id' => $r['page_id']
	];

	if ($prev_id) {
		unset($par['cat_id']);
		unset($par['exclude_page_id']);
		unset($par['function_add_custom_sql']);
		$par['page_id'] = $prev_id;
	}

	if ($pages = mso_get_pages($par, $temp)) $out['prev'] = $pages[0];

	// $r['reverse'] — меняем местами пункты
	if (isset($r['reverse']) and $r['reverse']) {
		$o = [];
		$o['next'] = $out['prev'];
		$o['prev'] = $out['next'];
		$out = $o;
	}

	return $out;
}

// вспомогательная функция для next mso_next_prev_page
function _sql_next_page()
{
	$CI = &get_instance();
	$CI->db->where('page_date_publish > ', mso_get_val('_sql_next_prev_pages_page_date_publish'));
}

// вспомогательная функция для prev mso_next_prev_page
function _sql_prev_page()
{
	$CI = &get_instance();
	$CI->db->where('page_date_publish < ', mso_get_val('_sql_next_prev_pages_page_date_publish'));
}

# end of file
