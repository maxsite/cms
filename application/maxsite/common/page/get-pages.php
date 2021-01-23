<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// функция получения выборки страниц
function mso_get_pages($r = [], &$pag = [])
{
	global $MSO, $mso_page_current;

	$CI = &get_instance();

	if (!isset($r['limit']))
		$r['limit'] = 7; // сколько отдавать страниц
	else {
		// проверим входящий лимит - он должен быть числом
		$r['limit'] = (int) $r['limit'];
		$r['limit'] = abs($r['limit']);
		if (!$r['limit']) $r['limit'] = 7; // что-то не то, заменяем на дефолт=7
	}

	if (!isset($r['cut'])) $r['cut'] = tf('Далее'); // ссылка на [cut]
	if (!isset($r['xcut'])) $r['xcut'] = true; // для тех у кого нет cut, но есть xcut выводить после xcut

	if (!isset($r['show_cut'])) $r['show_cut'] = true; // отображать ссылку «далее» для [cut] ? 
	if (!isset($r['show_xcut'])) $r['show_xcut'] = true; // отображать ссылку «далее» для [xcut] ? 

	// приписка к ссылке на страницу полной записи
	if (!isset($r['a_link_cut'])) $r['a_link_cut'] = '#cut';

	// удалять ли [cut], если false, то cut не обрабатывается
	// если false, то $r['cut'] и $r['xcut'] уже не учитываются
	if (!isset($r['work_cut'])) $r['work_cut'] = true;

	// отдавать все поля из таблиц (только для типов home и page)
	// если false, то только то, что предопределено
	if (!isset($r['all_fields'])) $r['all_fields'] = false;

	if (!isset($r['cat_order'])) $r['cat_order'] = 'category_name'; // сортировка рубрик
	if (!isset($r['cat_order_asc'])) $r['cat_order_asc'] = 'asc'; // порядок рубрик

	if (!isset($r['meta_order'])) $r['meta_order'] = 'meta_value'; // сортировка meta
	if (!isset($r['meta_order_asc'])) $r['meta_order_asc'] = 'asc'; // порядок meta

	if (!isset($r['pagination'])) $r['pagination'] = true; // использовать пагинацию
	if (!isset($r['content'])) $r['content'] = true; // получать весь текст

	// если 0, значит все страницы - только для главной
	// можно указать номера страниц через запятую
	if (!isset($r['page_id'])) $r['page_id'] = 0;

	// можно указать номера рубрик через запятую
	if (!isset($r['cat_id'])) $r['cat_id'] = 0; // если 0, значит все рубрики - только для главной

	// исключить указанные в массиве рубрики
	if (!isset($r['exclude_cat_id'])) $r['exclude_cat_id'] = 0;

	if (!isset($r['type'])) $r['type'] = 'blog'; // если false - то все, иначе blog или static
	if ($r['page_id']) $r['type'] = false; // если указан номер, то тип страницы сбрасываем

	if (!isset($r['order'])) $r['order'] = 'page_date_publish'; // поле сортировки страниц
	if (!isset($r['order_asc'])) $r['order_asc'] = 'desc'; // поле сортировки страниц

	// если нужно вывести все данные, невзирая на limit, то no_limit=true - пагинация при этом отключается
	if (!isset($r['no_limit'])) $r['no_limit'] = false;

	// если указаны номера записей, то limit отключаем
	if ($r['page_id']) $r['no_limit'] = true;

	if ($r['no_limit']) $r['pagination'] = false;

	// custom_type - аналог is_type - анализ явного указания типа данных
	if (!isset($r['custom_type']))	$r['custom_type'] = false;

	// кастомная функция - вызывается вместо автоанализа по is_type
	// эта функция обязательно должна быть подобна _mso_sql_build_home($r, &$pag) и т.п.
	if (!isset($r['custom_func'])) $r['custom_func'] = false;

	// для функции mso_page_title - передаем тип ссылки для страниц
	if (!isset($r['link_page_type'])) $r['link_page_type'] = 'page';

	// для _mso_sql_build_category можно указать массив номеров рубрик
	// и получить все записи указанных рубрик
	if (!isset($r['categories'])) $r['categories'] = [];

	// исключить указанные в массиве записи
	if (!isset($r['exclude_page_id'])) $r['exclude_page_id'] = [];

	// произвольный slug - используется там, где вычисляется mso_segment(2)
	// страница, рубрика, метка, поиск
	if (!isset($r['slug']))			$r['slug'] = false;

	// если true, то публикуется только те, которые старше текущей даты
	// если false - то публикуются все
	// если юзер залогинен, то дата сбрасывается при выводе page
	if (!isset($r['date_now']))		$r['date_now'] = true;

	// смещение времени в формате ЧЧ:ММ
	// если нет, то берется из настроек 
	if (!isset($r['time_zone'])) {
		$time_zone = getinfo('time_zone');

		if ($time_zone < 10 and $time_zone > 0) {
			$time_zone = '0' . $time_zone;
		} elseif ($time_zone > -10 and $time_zone < 0) {
			$time_zone = '0' . $time_zone;
			$time_zone = str_replace('0-', '-0', $time_zone);
		} else {
			$time_zone = '00.00';
		}

		$time_zone = str_replace('.', ':', $time_zone);
		$r['time_zone'] = $time_zone;
	}

	// если указано учитывать время публикации, то выполняем запрос в котором получаем
	// все записи, которые будущие и которые следует исключить из выборки
	// сей алгоритм связан с оптимизацией запросов к MySQL и значительным (очень!) ускорением
	// сложного select без использования NOW()
	if ($r['date_now']) {
		$CI->db->select('`page_id`', false);
		$CI->db->where('page_date_publish > ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
		$query = $CI->db->get('page');

		if ($query and $query->num_rows() > 0) {
			$page_id_date_now = $query->result_array();
			$r['page_id_date_now'] = [];

			foreach ($page_id_date_now as $key => $val) {
				$r['page_id_date_now'][] = $val['page_id'];
			}
		} else {
			$r['page_id_date_now'] = false; // нет записей 
		}
	} else {
		$r['page_id_date_now'] = false; // не нужно учитывать время
	}

	// учитывать ли опцию публикация RSS в странице -
	// если true, то отдаются только те, которые отмечены с этой опцией, false - все
	if (!isset($r['only_feed'])) $r['only_feed'] = false;

	// стутус страниц - если false, то не учитывается
	if (!isset($r['page_status'])) {
		$r['page_status'] = 'publish';
		$r['page_status0'] = ''; // флаг что статус страницы не указан в исходном виде
	} else {
		$r['page_status0'] = $r['page_status'];
	}
	
	// можно указать номер автора - получим только его записи
	if (!isset($r['page_id_autor'])) $r['page_id_autor'] = false;

	// получать ли информацию о рубриках
	// если false, то возвращает пустые массивы page_categories и page_categories_detail
	if (!isset($r['get_page_categories'])) $r['get_page_categories'] = true;

	// получать ли информацию о метках и мета страницы
	// объединены, потому что это один sql-запрос
	// если false, то возвращает пустые массивы page_tags и page_meta
	// при этом перестанет работать парсер текста
	if (!isset($r['get_page_meta_tags'])) $r['get_page_meta_tags'] = true;

	// нужно ли получать данные по количеству комментариев к страницам
	if (!isset($r['get_page_count_comments'])) $r['get_page_count_comments'] = true;

	// после получения записей можно изменить порядрк записей на обратный
	// если true, то делаем реверс 
	if (!isset($r['pages_reverse'])) $r['pages_reverse'] = false;

	// можно указать key и table для получения произвольных выборок мета, например метки
	// используется только для _mso_sql_build_tag
	// в качестве meta_value используется $slug
	if (!isset($r['meta_key']))	$r['meta_key'] = 'tags';
	if (!isset($r['meta_table'])) $r['meta_table'] = 'page';

	// сегмент, признак пагинации
	if (!isset($r['pagination_next_url']))	$r['pagination_next_url'] = 'next';

	// функция со своим sql, в которой можно добавить свои условия
	if (!isset($r['function_add_custom_sql']))
		$r['function_add_custom_sql'] = false;
	else 
		if (!function_exists($r['function_add_custom_sql'])) $r['function_add_custom_sql'] = false;

	// хук, если нужно поменять параметры
	// $r_restore = $r; 
	$r = mso_hook('mso_get_pages', $r);

	// для каждого типа страниц строится свой sql-запрос
	// мы оформляем его в $CI, а здесь только выполняем $CI->db->get();

	// если указана кастомная функция, то выполняем r1
	if ($r['custom_func'] and function_exists($r['custom_func'])) {
		$r['custom_func']($r, $pag);
	} elseif ($r['custom_type']) {
		// указан какой-то свой тип данных - аналог is_type
		$custom_type = $r['custom_type'];

		if ($custom_type == 'home') _mso_sql_build_home($r, $pag);
		elseif ($custom_type == 'page') _mso_sql_build_page($r, $pag);
		elseif ($custom_type == 'category') _mso_sql_build_category($r, $pag);
		elseif ($custom_type == 'tag') _mso_sql_build_tag($r, $pag);
		elseif ($custom_type == 'archive') _mso_sql_build_archive($r, $pag);
		elseif ($custom_type == 'search') _mso_sql_build_search($r, $pag);
		elseif ($custom_type == 'author') _mso_sql_build_author($r, $pag);
		else return [];
	} elseif (is_type('home')) _mso_sql_build_home($r, $pag);
	elseif (is_type('page')) _mso_sql_build_page($r, $pag);
	elseif (is_type('category')) _mso_sql_build_category($r, $pag);
	elseif (is_type('tag')) _mso_sql_build_tag($r, $pag);
	elseif (is_type('archive')) _mso_sql_build_archive($r, $pag);
	elseif (is_type('search')) _mso_sql_build_search($r, $pag);
	elseif (is_type('author')) _mso_sql_build_author($r, $pag);
	else return [];

	// сам запрос и его обработка
	// $query = $CI->db->get();

	// нужно добавить SQL_BUFFER_RESULT
	// поскольку CodeIgniteryt не позволяет добавлять его явно, придется извращаться	
	// $query_sql = str_replace('SELECT ', 'SELECT SQL_BUFFER_RESULT ', $CI->db->_compile_select());

	// SQL_BUFFER_RESULT - убрал, неактуально на новых версиях mysql
	$query_sql = $CI->db->_compile_select();

	// экранирование CodeIgniter - используем свои костыли для запятых в запросе
	$query_sql = str_replace('_MSO_ZAP_', ',', $query_sql);

	// pr($query_sql);

	$query = $CI->db->query($query_sql);
	$CI->db->_reset_select();
	
	// восстанавливать после запроса???
	// $r = mso_hook('mso_get_pages_restore', $r_restore);
	
	if ($query and $query->num_rows() > 0) {
		$pages = $query->result_array();
				
		if ($r['pages_reverse']) $pages = array_reverse($pages);

		$MSO->data['pages_is'] = true; // ставим признак, что записи получены

		// если явно указано какое-то условие статуса, то отдаем как есть
		if (is_type('page') and !$r['page_status0']) {
			// проверяем статус публикации - если page_status <> publish то смотрим автора и сравниваем с текущим юзером
			$page_status = $pages[0]['page_status']; // в page - всегда одна запись

			if ($page_status != 'publish') {
				// не опубликовано
				if (isset($MSO->data['session']['users_id'])) {
					// залогинен
					if ($pages[0]['page_id_autor'] <> $MSO->data['session']['users_id'] && !mso_check_allow('admin_page_edit_other')) return [];
					else {
						if ($page_status == 'draft') $pages[0]['page_title'] .= ' ' . tf('(черновик)');
						// else $pages[0]['page_title'] .= ' (личное)';
					}
				} else {
					return []; // не залогинен
				}
			}			
		}

		// массив всех page_id
		$all_page_id = [];

		foreach ($pages as $key => $page) {
			$all_page_id[] = $page['page_id'];

			$content = $page['page_content'];
			$content = mso_hook('content_init', $content);
			$content = str_replace('<!-- pagebreak -->', '[cut]', $content); // совместимость с TinyMCE

			// если после [cut] пробелы до конца строки, то удалим их 
			$content = preg_replace('|\[cut\]\s*<br|', '[cut]<br', $content);
			$content = preg_replace('|\[cut\](\&nbsp;)*<br|', '[cut]<br', $content);
			$content = preg_replace('|\[cut\](\&nbsp;)*(\s)*<br|', '[cut]<br', $content);

			$pages[$key]['page_slug'] = $page['page_slug'] = mso_slug($page['page_slug']);

			if ($r['work_cut']) {
				// обрабатывать cut
				if ($r['xcut']) // можно использовать [xcut]
					$content = str_replace('[xcut', '[mso_xcut][cut', $content);
				else
					$content = str_replace('[xcut', '[cut', $content);

				if (preg_match('/\[cut(.*?)?\]/', $content, $matches)) {
					$content = explode($matches[0], $content, 2);
					$cut = $matches[1];
				} else {
					$content = array($content);
					$cut = '';
				}

				$output = $content[0];

				if (count($content) > 1) {
					// ссылка на «далее...»
					if ($r['cut'] !== false) {
						if ($cut) {
							if (isset($content[1])) {
								if (strpos($cut, '%wordcount%') !== false)
									$cut = str_replace('%wordcount%', mso_wordcount($content[1]), $cut);
							}
						} else $cut = $r['cut'];

						// отображать ссылку?
						if ($r['show_cut'])
							$output .= mso_page_title(
								$page['page_slug'] . $r['a_link_cut'],
								$cut,
								'<span class="mso-cut">',
								'</span>',
								true,
								false,
								$r['link_page_type']
							);
					} else {
						$output .= '<a id="cut"></a>' .  $content[1];
					}
				}

				if ($r['xcut']) {
					if (strpos($output, '[mso_xcut]') !== false) {
						$xcontent = explode('[mso_xcut]', $output);

						if ($r['cut'] and $cut) {
							if ($r['show_xcut'])
								$cut = mso_page_title(
									$page['page_slug'] . $r['a_link_cut'],
									$cut,
									'<span class="mso-cut">',
									'</span>',
									true,
									false,
									$r['link_page_type']
								);
							else
								$cut = '';

							$output = $xcontent[0] . $cut;
						} else {
							$output = $xcontent[1];
						}
					}
				}
			} else {
				$output = $content; // отдаем как есть
			}

			// $mso_page_current глобальная переменная, где хранится текущая обрабатываемая page
			$mso_page_current = $page;

			$output = mso_hook('content_in', $output);

			// замена [pi] и [page_images] на http://сайт/uploads/_pages/IDзаписи/ 
			$output = str_replace(array('[pi]', '[page_images]'), getinfo('uploads_url') . '_pages/' . $page['page_id'] . '/', $output);

			$output = mso_hook('content', $output);

			$pages[$key]['page_content'] = $output;
			$pages[$key]['page_categories'] = [];
			$pages[$key]['page_categories_detail'] = [];
			$pages[$key]['page_tags'] = [];
			$pages[$key]['page_meta'] = [];
			
			$mso_page_current = $pages[$key];
		}

		if ($r['get_page_categories']) {
			// теперь одним запросом получим все рубрики каждой записи

			$CI->db->select('page_id, category.category_id, category.category_name, category.category_slug, category.category_desc, category.category_id_parent');
			$CI->db->where_in('page_id', $all_page_id);
			$CI->db->order_by('category.' . $r['cat_order'], $r['cat_order_asc']); // сортировка рубрик
			$CI->db->from('cat2obj');
			$CI->db->join('category', 'cat2obj.category_id = category.category_id');

			if ($query = $CI->db->get())
				$cat = $query->result_array();
			else
				$cat = [];


			// переместим все в массив page_id[] = category_id
			$page_cat = [];
			$page_cat_detail = [];

			foreach ($cat as $key => $val) {
				$page_cat[$val['page_id']][] = $val['category_id'];

				$page_cat_detail[$val['page_id']][$val['category_id']] = [
					'category_name' => $val['category_name'],
					'category_slug' => $val['category_slug'],
					'category_desc' => $val['category_desc'],
					'category_id_parent' => $val['category_id_parent'],
					'category_id' => $val['category_id']
				];
			}
		}

		if ($r['get_page_meta_tags']) {
			// по этому же принципу получаем все метки
			$CI->db->select('`meta_id_obj`, `meta_key`, `meta_value`', false);
			$CI->db->where(array('meta_table' => 'page'));
			$CI->db->where_in('meta_id_obj', $all_page_id);
			$CI->db->order_by($r['meta_order'], $r['meta_order_asc']); // сортировка мета

			if ($query = $CI->db->get('meta'))
				$meta = $query->result_array();
			else
				$meta = [];

			// переместим все в массив page_id[] = category_id
			$page_meta = [];

			foreach ($meta as $key => $val) {
				$page_meta[$val['meta_id_obj']][$val['meta_key']][] = $val['meta_value'];
			}
		}

		// нужно получить колво комментариев к записям
		if ($r['get_page_count_comments']) {
			$CI->db->select('`comments_page_id`, COUNT(`comments_id`) AS `page_count_comments`', false);
			$CI->db->where_in('comments_page_id', $all_page_id);
			$CI->db->where('comments_approved', '1');
			$CI->db->group_by('comments_page_id');
			$CI->db->from('comments');

			$query = $CI->db->get();
			$count_comments = $query->result_array();

			// переместим все в массив page_count_comments
			$page_count_comments = [];

			foreach ($count_comments as $key => $val) {
				$page_count_comments[$val['comments_page_id']] = $val['page_count_comments'];
			}
		}

		// получим данные о всех парсерах
		$parser_all = mso_hook('parser_register', []); // все зарегистрированные парсеры

		// добавим в массив pages полученную информацию по меткам и рубрикам
		foreach ($pages as $key => $val) {
			// рубрики
			if ($r['get_page_categories'] and isset($page_cat[$val['page_id']]) and $page_cat[$val['page_id']]) {
				$pages[$key]['page_categories'] = $page_cat[$val['page_id']];
				$pages[$key]['page_categories_detail'] = $page_cat_detail[$val['page_id']];
			}

			// метки отдельно как page_tags
			if ($r['get_page_meta_tags'] and isset($page_meta[$val['page_id']]['tags']) and $page_meta[$val['page_id']]['tags'])
				$pages[$key]['page_tags'] = $page_meta[$val['page_id']]['tags'];

			// остальные мета отдельно в page_meta
			if ($r['get_page_meta_tags'] and isset($page_meta[$val['page_id']]) and $page_meta[$val['page_id']])
				$pages[$key]['page_meta'] = $page_meta[$val['page_id']];


			// колво комментариев
			if ($r['get_page_count_comments']) {
				if (isset($page_count_comments[$val['page_id']]))
					$pages[$key]['page_count_comments'] = $page_count_comments[$val['page_id']];
				else
					$pages[$key]['page_count_comments'] = 0; // нет комментариев
			} else {
				$pages[$key]['page_count_comments'] = 0; // ставим, что нет комментариев
			}
			// обработка контента хуками
			$output = $pages[$key]['page_content'];

			// обработка парсером
			if (isset($pages[$key]['page_meta']['parser_content'])) {
				if ($pages[$key]['page_meta']['parser_content'][0] !== 'none') {
					$p = $pages[$key]['page_meta']['parser_content'][0];

					if (isset($parser_all[$p]['content'])) {
						$func = $parser_all[$p]['content']; // функция, которую нужно выполнить
						if (function_exists($func)) $output = $func($output);
					}
				}
			} else {
				// парсер не указан, используем дефолтный
				// проверим его наличие по функции parser_default_content()
				// иначе нужно включить плагин
				if (!function_exists('parser_default_content')) {
					require_once(getinfo('plugins_dir') . 'parser_default/index.php');
				}

				$output = parser_default_content($output);
			}

			$output = mso_hook('content_complete', $output);
			$pages[$key]['page_content'] = $output;
			$pages[$key] = mso_hook('page_complete', $pages[$key]);
			
			$mso_page_current = $pages[$key];
		}
	} else {
		$pages = [];
		$MSO->data['pages_is'] = false; // ставим признак, что записей нет
	}

	return $pages;
}

// главная страница - home
function _mso_sql_build_home($r, &$pag)
{
	$CI = &get_instance();
	$offset = 0;
	$cat_id = $r['cat_id'] ? mso_explode($r['cat_id']) : false;
	$exclude_cat_id = $r['exclude_cat_id'] ? mso_explode($r['exclude_cat_id']) : false;
	$r['page_id'] = mso_explode($r['page_id']);

	// еслу указан массив номеров рубрик, значит выводим только его
	$categories = $r['categories'] ? true : false;

	// если указаны номера записей, котоыре следует исключить
	$exclude_page_id = $r['exclude_page_id'] ? true : false;

	if ($r['pagination']) {
		// пагинация
		// для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		// сама пагинация выводится отдельным плагином
		// запрос один в один, кроме limit и юзеров
		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page.page_status', $r['page_status']);

		// if ($r['date_now']) 
		//	$CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

		if ($r['type']) {
			if (is_array($r['type']))
				$CI->db->where_in('page_type.page_type_name', $r['type']);
			else
				$CI->db->where('page_type.page_type_name', $r['type']);
		}

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');

		if ($cat_id or $categories or $exclude_cat_id) {
			$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
			$CI->db->join('category', 'cat2obj.category_id = category.category_id', 'left');
		}

		if ($r['page_id'])
			$CI->db->where_in('page.page_id', $r['page_id']);

		if ($r['page_id_autor'])
			$CI->db->where('page.page_id_autor', $r['page_id_autor']);

		if ($cat_id)
			$CI->db->where_in('category.category_id', $cat_id);

		if ($categories)
			$CI->db->where_in('category.category_id', $r['categories']);

		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

		if ($exclude_cat_id)
			$CI->db->where_not_in('category.category_id', $exclude_cat_id);

		if ($r['order'])
			$CI->db->order_by($r['order'], $r['order_asc']);

		$CI->db->group_by('page.page_id');

		if ($function_add_custom_sql = $r['function_add_custom_sql'])
			$function_add_custom_sql();

		$query = $CI->db->get();

		$pag_row = $query->num_rows();

		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);

			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	// теперь сами страницы
	if (!$r['all_fields']) {
		if ($r['content']) {
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
		} else {
			// такие селекты теперь нужно вызывать с false в конце...
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login', false);
		}
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');
	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');

	if ($cat_id or $categories or $exclude_cat_id) {
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id', 'left');
	}

	if ($r['page_id']) $CI->db->where_in('page.page_id', $r['page_id']);
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	// $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now'])
		$CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

	if ($r['only_feed'])
		$CI->db->where('page_feed_allow', '1');

	if ($r['page_id_autor'])
		$CI->db->where('page.page_id_autor', $r['page_id_autor']);

	if ($cat_id)
		$CI->db->where_in('category.category_id', $cat_id);

	if ($categories)
		$CI->db->where_in('category.category_id', $r['categories']);

	if ($exclude_page_id)
		$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

	if ($exclude_cat_id)
		$CI->db->where_not_in('category.category_id', $exclude_cat_id);

	// экранирование CodeIgniter! Приходится делать свои замены! 
	if ($r['page_id']) {
		$CI->db->order_by('FIELD(`' . $CI->db->dbprefix . 'page`.`page_id`_MSO_ZAP_' . implode('_MSO_ZAP_', $r['page_id']) . ')');
	} else {
		if ($r['order'])
			$CI->db->order_by($r['order'], $r['order_asc']);
	}

	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}
	// pr(_sql($query));
	if ($function_add_custom_sql = $r['function_add_custom_sql'])
		$function_add_custom_sql();
}

// одиночная страница
function _mso_sql_build_page($r, &$pag)
{
	$CI = &get_instance();

	$pag = false; // здесь нет пагинации

	if ($r['slug'])
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);

	if (!$r['all_fields']) {
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_last_modified, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	if (!is_login()) {
		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	}

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	if ($r['page_status0']) $CI->db->where('page_status', $r['page_status0']);
		
	$CI->db->where(array('page_slug' => $slug));
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->limit(1);

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// рубрики
function _mso_sql_build_category($r, &$pag)
{
	$CI = &get_instance();

	if ($r['slug'])
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);

	// если указан массив номеров рубрик, значит выводим только его
	if ($r['categories'])
		$categories = true;
	else
		$categories = false;

	// если указаны номера записей, котоыре следует исключить
	if ($r['exclude_page_id'])
		$exclude_page_id = true;
	else
		$exclude_page_id = false;

	$offset = 0;

	if ($r['pagination']) {
		// пагинация
		// для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		// сама пагинация выводится отдельным плагином
		// запрос один в один, кроме limit и юзеров
		//$CI->db->select('page.page_id');

		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['type']) {
			if (is_array($r['type']))
				$CI->db->where_in('page_type_name', $r['type']);
			else
				$CI->db->where('page_type_name', $r['type']);
		}

		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');

		if ($categories) {
			$CI->db->where_in('category.category_id', $r['categories']);
		} else {
			$CI->db->where('category.category_slug', $slug);
		}

		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();

		$pag_row = $query->num_rows();

		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	if (!$r['all_fields']) {
		// теперь сами страницы
		if ($r['content'])
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
		else
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login', false);
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');

	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
	// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

	if ($r['only_feed']) $CI->db->where('page.page_feed_allow', '1');
	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	if ($r['type']) {
		if (is_array($r['type'])) $CI->db->where_in('page_type_name', $r['type']);
		else $CI->db->where('page_type_name', $r['type']);
	}

	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
	$CI->db->join('category', 'cat2obj.category_id = category.category_id');

	if ($categories) {
		$CI->db->where_in('category.category_id', $r['categories']);
	} else {
		$CI->db->where('category.category_slug', $slug);
	}

	if ($exclude_page_id)
		$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// страница меток
function _mso_sql_build_tag($r, &$pag)
{
	$CI = &get_instance();

	if ($r['slug'])
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);

	// если метка не указана, то иммитируем несуществующую метку, чтобы была 404-страница
	if (!$slug and !isset($r['no_meta_value']) and !isset($r['meta_value'])) {
		$slug = md5(time());
		$r['pagination'] = false;
	}

	// если no_meta_value = true, то делаем $slug = false
	// чтобы можно было делать запросы без этого поля
	// если указан meta_value, то $slug = meta_value
	if (isset($r['no_meta_value']) and $r['no_meta_value'] === true) $slug = false;
	elseif (isset($r['meta_value']) and $r['meta_value']) $slug = $r['meta_value'];

	if ($r['exclude_page_id'])
		$exclude_page_id = true;
	else
		$exclude_page_id = false;

	$offset = 0;

	if ($r['pagination']) {
		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

		if ($r['type']) {
			if (is_array($r['type'])) $CI->db->where_in('page_type_name', $r['type']);
			else $CI->db->where('page_type_name', $r['type']);
		}

		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
		$CI->db->where('meta_key', $r['meta_key']);
		$CI->db->where('meta_table', $r['meta_table']);

		if ($slug) $CI->db->where('meta_value', $slug);
		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();

		$pag_row = $query->num_rows();

		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	if (!$r['all_fields']) {
		// теперь сами страницы
		if ($r['content'])
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, meta.meta_value AS tag_name, page.page_id_autor, users_description, users_login');
		else
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, meta.meta_value AS tag_name, page.page_id_autor, users_description, users_login', false);
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');

	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	//if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');

	if ($exclude_page_id)
		$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

	$CI->db->where('meta_key', $r['meta_key']);
	$CI->db->where('meta_table', $r['meta_table']);

	if ($slug) $CI->db->where('meta_value', $slug);

	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// архивы по датам
function _mso_sql_build_archive($r, &$pag)
{
	global $MSO;

	$CI = &get_instance();

	// [1]=>archive [2]=>Y [3]=>M [4]=>D [5]=>next
	$seg = $MSO->data['uri_segment'];

	// если есть пагинация, то отсекаем её
	$in_next = array_search($r['pagination_next_url'], $seg);

	if ($in_next !== false) {
		// есть вхождение - отсекаем
		// эти операции, чтобы начать счёт в массиве с 1, как в $MSO->data['uri_segment'])
		$seg = array_merge(array(0), array_slice($seg, 0, $in_next - 1));
		unset($seg[0]);
	}

	$count_segment = count($seg) - 1;
	$year = (int) mso_segment(2, true, $seg); // используем свой массив сегментов

	if ($year > date('Y', time()) or $year < 2006) $year = date('Y', time());

	$month = (int) mso_segment(3, true, $seg);
	$day = (int) mso_segment(4, true, $seg);

	if ($count_segment >= 3) {
		// [2]=>Y [3]=>M [4]=>D
		// указаны год-месяц-дата

		if ($month > 12 or $month < 1) $month = 1;

		$dmax = get_total_days($month, $year);

		if ($day == 0) $day = 1;
		if ($day > $dmax) $day = $dmax;

		$date_in = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month . '-' . $day . ' 00:00:00', false);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month . '-' . $day . ' 23:59:59', false);
	} elseif ($count_segment == 2) {
		// [2]=>Y [3]=>M
		// указано год-месяц

		if ($month > 12 or $month < 1) $month = 1;

		$dmax = get_total_days($month, $year);
		$date_in = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month . '-1 00:00:00', false);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month . '-' . $dmax . ' 23:59:59', false);
	} elseif ($count_segment == 1) {
		// [2]=>Y
		// указан только год
		$date_in = mso_date_convert('Y-m-d H:i:s', $year . '-01-01 00:00:00', false);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-12-31 23:59:59', false);
	} else {
		// ничего не указано - выводим архив за все время
		$year = date('Y', time());
		$date_in = mso_date_convert('Y-m-d H:i:s', '2006-01-01 00:00:00', false);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-12-31 23:59:59', false);
	}

	$offset = 0;

	if ($r['pagination']) {
		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

		if ($r['type']) {
			if (is_array($r['type']))
				$CI->db->where_in('page_type_name', $r['type']);
			else
				$CI->db->where('page_type_name', $r['type']);
		}

		$CI->db->where('page_date_publish >= ', $date_in);
		$CI->db->where('page_date_publish <= ', $date_in_59);

		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->order_by($r['order'], $r['order_asc']);

		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();

		$pag_row = $query->num_rows();
		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);

			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	// теперь сами страницы
	if (!$r['all_fields']) {
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');

	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	$CI->db->where('page_date_publish >= ', $date_in);
	$CI->db->where('page_date_publish <= ', $date_in_59);

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// страница поиска
function _mso_sql_build_search($r, &$pag)
{
	$CI = &get_instance();

	if ($r['slug'])
		$search = $r['slug'];
	else
		$search = mso_segment(2);

	// $search = mso_segment(2);
	$search = mso_strip(strip_tags($search));
	$offset = 0;

	if ($r['pagination']) {
		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');
		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['type']) {
			if (is_array($r['type']))
				$CI->db->where_in('page_type_name', $r['type']);
			else
				$CI->db->where('page_type_name', $r['type']);
		}

		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

		$CI->db->where(
			'(`page_content` LIKE \'%' . $CI->db->escape_str($search) . '%\' OR `page_title` LIKE \'%' . $CI->db->escape_str($search) . '%\')',
			'',
			false
		);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->order_by($r['order'], $r['order_asc']);

		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();
		$pag_row = $query->num_rows();

		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	// теперь сами страницы
	if (!$r['all_fields']) {
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');

	// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	// like делаем свой
	$CI->db->where(
		'(`page_content` LIKE \'%' . $CI->db->escape_str($search) . '%\' OR `page_title` LIKE \'%' . $CI->db->escape_str($search) . '%\')',
		'',
		false
	);

	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// страницы автора
function _mso_sql_build_author($r, &$pag)
{
	$CI = &get_instance();

	if ($r['slug'])
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);

	// если slug есть число, то выполняем поиск по id
	if (!is_numeric($slug))
		$id = 0; // slug не число
	else
		$id = (int) $slug;

	$offset = 0;

	if ($r['pagination']) {
		$CI->db->select($CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['type']) {
			if (is_array($r['type']))
				$CI->db->where_in('page_type_name', $r['type']);
			else
				$CI->db->where('page_type_name', $r['type']);
		}

		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->where('page.page_id_autor', $id);
		$CI->db->group_by('page.page_id');
		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();
		$pag_row = $query->num_rows();

		if ($pag_row > 0) {
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу
			$pag['rows'] = $pag_row; // всего записей

			$current_paged = mso_current_paged($r['pagination_next_url']);

			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		} else {
			$pag = false;
		}
	} else {
		$pag = false;
	}

	// теперь сами страницы
	if (!$r['all_fields']) {
		if ($r['content'])
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login');
		else
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, page_menu_order, users_avatar_url, page.page_id_autor, users_description, users_login', false);
	} else {
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
	if ($r['only_feed']) $CI->db->where('page.page_feed_allow', '1');

	if ($r['type']) {
		if (is_array($r['type']))
			$CI->db->where_in('page_type_name', $r['type']);
		else
			$CI->db->where('page_type_name', $r['type']);
	}

	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->where('page.page_id_autor', $id);
	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit']) {
		if ($pag and $offset)
			$CI->db->limit($r['limit'], $offset);
		else
			$CI->db->limit($r['limit']);
	}

	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();
}

// вспомогательная
// функция из Calendar.php
if (!function_exists('get_total_days')) {
	function get_total_days($month, $year)
	{
		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

		if ($month < 1 or $month > 12) return 0;
		if ($month == 2) {
			if ($year % 400 == 0 or ($year % 4 == 0 and $year % 100 != 0))	return 29;
		}

		return $days_in_month[$month - 1];
	}
}

# end of file
