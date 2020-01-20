<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 * 
 */

// блок "Еще записи этой рубрики"
function mso_page_other_pages($page_id = 0, $page_categories = [])
{
	if ($bl_title = mso_get_option('page_other_pages', 'templates', tf('Еще записи по теме'))) {
		// алгоритм получения записей
		$algoritm = mso_get_option('page_other_pages_algoritm', 'templates', 'all');

		$type_page = mso_get_option('page_other_pages_type_page', 'templates', '');

		if (!$type_page) $type_page = false;

		$custom_type = 'category';

		if ($algoritm == 'lowlewel') {
			// только из подрубрик
			$all_cat = mso_cat_array_single(); // все рубрики

			$bl_page_categories = []; // обработаный массив id-level

			foreach ($page_categories as $cat_id) {
				$bl_page_categories[$cat_id] = $all_cat[$cat_id]['level'];
			}

			arsort($bl_page_categories); // сортируем в обратном порядке

			$bl_page_categories = array_keys($bl_page_categories); // оставляем только ключи (id)

			// если что-то есть, то оставляем только первую рубрику, иначе $page_categories
			if (isset($bl_page_categories[0]))
				$bl_page_categories = array($bl_page_categories[0]);
			else
				$bl_page_categories = $page_categories;
		} elseif ($algoritm == 'no-cat') {
			// не учитывать рубрики
			$bl_page_categories = [];
			$custom_type = 'home';
		} else {
			// обычный вывод по всем рубрикам 
			$bl_page_categories = $page_categories;
		}

		// своя функция sql-запроса для function_add_custom_sql
		// задается через mso_set_val()
		$fasc = mso_get_val('page_other_pages_function_add_custom_sql', false);
		
		// если отмечена опция Только с изображениями записи и нет своей sql-функции
		// подключаем функцию для поиска изображений записи
		if (!$fasc and mso_get_option('page_other_pages_is_image', 'templates', ''))
			$fasc = '_mso_page_other_pages_is_image';
		
		$bl_pages = mso_get_pages(
			[
				'type' => $type_page,
				'content' => false,
				'pagination' => false,
				'custom_type' => $custom_type,
				'categories' => $bl_page_categories,
				'exclude_page_id' => array($page_id),
				'limit' => mso_get_option('page_other_pages_limit', 'templates', 7),
				'order' => mso_get_option('page_other_pages_order', 'templates', 'page_date_publish'),
				'order_asc' => mso_get_option('page_other_pages_order_asc', 'templates', 'random'),
				'function_add_custom_sql' => $fasc,
			],
			$_temp
		);

		if ($bl_pages) {
			if ($f = mso_page_foreach('page-other-pages-out')) {
				// свой вывод
				require $f;
			} else {
				echo '<div class="mso-page-other-pages">' . mso_get_val('page_other_pages_start', '<h4>') . $bl_title . mso_get_val('page_other_pages_end', '</h4>') . '<ul>';

				foreach ($bl_pages as $bl_page) {
					mso_page_title($bl_page['page_slug'], $bl_page['page_title'], '<li>', '</li>', true);
				}

				echo '</ul></div>';
			}
		}
	}
}

function _mso_page_other_pages_is_image()
{
	// добавляем выборку по метаплю превьюшки
	$CI = &get_instance();
	$CI->db->where('meta.meta_table', 'page');
	$CI->db->where('meta.meta_key', 'image_for_page');
	$CI->db->join('meta', 'meta.meta_id_obj = page.page_id', 'left');
}

# end of file
