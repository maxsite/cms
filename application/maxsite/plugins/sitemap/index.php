<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function sitemap_autoload($args = array())
{
	mso_hook_add('content', 'sitemap_content'); # хук на обработку текста [sitemap]
	mso_hook_add('page_404', 'sitemap404'); # хук на 404-страницу
}


# оюработка текста на предмет в нем [sitemap]
function sitemap_content($text = '')
{
	if (strpos($text, '[sitemap]') === false) // нет в тексте
	{
		return $text;
	} else {
		return str_replace('[sitemap]', sitemap(), $text);
	}
}


# оюработка текста на предмет в нем [sitemap]
function sitemap404($text = '')
{
	return  '<h2 class="sitemap">' . tf('Воспользуйтесь картой сайта') . '</h2>' . sitemap();
}

# явный вызов функции - отдается карта сайта
function sitemap($arg = '')
{
	global $MSO;
	
	mso_head_meta('title', t('Карта сайта') ); // meta title страницы

	if (mso_segment(2) == 'cat') return sitemap_cat($arg);
	if (mso_segment(2) == 'cat-list') return sitemap_cat_list($arg);

	// кэш строим по url, потому что у он меняется от пагинации
	$cache_key = 'sitemap' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$options = mso_get_option('plugin_sitemap', 'plugins', array()); // получаем опции
	if (!isset($options['limit'])) $options['limit'] = 30;
	else $options['limit'] = (int) $options['limit'];

	if ($options['limit'] < 2) $options['limit'] = 30;


	$out = '';
	// параметры для получения страниц
	$par = array(
		//'no_limit' => true,
		'limit' => $options['limit'],
		// 'type'=> false, 
		'custom_type' => 'home',
		'content' => false,
		'cat_order' => 'category_id_parent',
		'cat_order_asc' => 'asc',
		//'order_asc'=> 'desc',
	);
	if ($f = mso_page_foreach('sitemap-mso-get-pages')) require($f);
	$pages = mso_get_pages($par, $pagination); // получим все

	if ($pages) {
		$out .= '<div class="page_content"><div class="sitemap sitemap_list">' . mso_hook('sitemap_do');

		$out .= sitemap_bread('date');

		$first = true;
		foreach ($pages as $page) {
			$date = mso_date_convert('m/Y', $page['page_date_publish']);

			if ($first) {
				$out .= '<h3>' . $date . '</h3><ul>';
				$first = false;
			} elseif ($date1 != $date) {
				$out .= '</ul><h3>' . $date . '</h3><ul>';
			}

			$slug = mso_slug($page['page_slug']);

			$out .= '<li>' . mso_date_convert('d', $page['page_date_publish']) . ': <a href="' . getinfo('siteurl')
				. 'page/' . $slug . '" title="' . htmlspecialchars($page['page_title']) . '">'
				. htmlspecialchars($page['page_title']) . '</a>';

			if ($page['page_categories'])
				$out .=  ' <span>('
					. mso_page_cat_link($page['page_categories'], ' &rarr; ', '', '', false)
					. ')</span>';

			$out .=  '</li>';

			$date1 = $date;
		}

		$out .= '</ul>' . mso_hook('sitemap_posle') . '</div></div>';
	}


	$pagination['type'] = '';
	ob_start();
	mso_hook('pagination', $pagination);
	$out .=  ob_get_contents();
	ob_end_clean();

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}


function sitemap_mso_options()
{
	mso_admin_plugin_options(
		'plugin_sitemap',
		'plugins',
		array(
			'limit' => array(
				'type' => 'text',
				'name' => t('Количество записей на одной странице'),
				'description' => '',
				'default' => '30'
			),
			'cat_descr' => array(
				'type' => 'checkbox',
				'name' => t('Выводить описание рубрик'),
				'description' => '',
				'default' => 1
			),
		),
		t('Настройки плагины Sitemap - архив записей'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

function sitemap_cat($arg = '')
{
	$cache_key = 'sitemap_cat';
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$options = mso_get_option('plugin_sitemap', 'plugins', array()); // получаем опции

	$cat_descr = isset($options['cat_descr']) ? $options['cat_descr'] : true;

	if ($cat_descr)
		$cat_descr = '<p class="sitemap_descr">[DESCR]</p>';
	else
		$cat_descr = '';

	$out = '';

	$out .= '<div class="page_content sitemap sitemap_cat">' . mso_hook('sitemap_do');

	$out .= sitemap_bread('cat');

	$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_name', 'asc', array(), array(), 0, 0, true);

	$out .= mso_create_list($all, array('function' => '_sitemap_cat_elem', 'childs' => 'childs', 'format' => '<h3>[TITLE_HTML]</h3>' . $cat_descr .'[FUNCTION]', 'format_current' => '', 'class_ul' => '', 'title' => 'category_name', 'link' => 'category_slug', 'current_id' => false, 'prefix' => 'category/', 'count' => 'pages_count', 'slug' => 'category_slug', 'id' => 'category_id', 'menu_order' => 'category_menu_order', 'id_parent' => 'category_id_parent'));

	$out .= mso_hook('sitemap_posle') . '</div>';

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}


function _sitemap_cat_elem($elem)
{
	static $all_cat = false;
	static $all_page = array();

	$out = '';

	if ($all_cat === false) $all_cat = mso_cat_array_single();

	foreach ($elem['pages'] as $page) {
		// page_id page_title page_date_publish page_status page_slug 

		if ($page['page_status'] == 'publish') {
			// все рубрики этой записи
			if (isset($all_page[$page['page_id']])) {
				$cur_cats = $all_page[$page['page_id']];
			} else {
				$cur_cats = mso_get_cat_page($page['page_id']);
				$all_page[$page['page_id']] = $cur_cats;
			}

			if ($cur_cats) {
				$max_level = 0;
				$cat_vybr = 0;
				foreach ($cur_cats as $cat) {
					$level = $all_cat[$cat]['level'];

					if ($level > $max_level) {
						$max_level = $level;
						$cat_vybr = $cat;
					}
				}

				if ($cat_vybr == $elem['category_id'] or $cat_vybr == 0)
					$out .= '<li><a href="' . getinfo('siteurl') . 'page/' . $page['page_slug'] . '">' . htmlspecialchars($page['page_title']) . '</a> <span>&rarr; ' . mso_page_date($page['page_date_publish'], array('format' => 'j F Y г. H:i'), '', '', false) . '</span></li>';
			} else
				$out .= '<li><a href="' . getinfo('siteurl') . 'page/' . $page['page_slug'] . '">' . htmlspecialchars($page['page_title']) . '</a> <span>&rarr; ' . mso_page_date($page['page_date_publish'], array('format' => 'j F Y г. H:i'), '', '', false) . '</span></li>';
		}
	}

	if ($out) $out = '<ul>' . $out . '</ul>';

	return $out;
}

function sitemap_cat_list($arg = '')
{
	$cache_key = 'sitemap_cat_list';
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$options = mso_get_option('plugin_sitemap', 'plugins', array()); // получаем опции

	$cat_descr = isset($options['cat_descr']) ? $options['cat_descr'] : true;

	if ($cat_descr)
		$cat_descr = '<p class="sitemap_descr">[DESCR]</p>';
	else
		$cat_descr = '';

	$out = '';

	$out .= '<div class="page_content sitemap sitemap_cat_list">' . mso_hook('sitemap_do');

	$out .= sitemap_bread('cat-list');

	$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_name', 'asc', array(), array(), 0, 0, true);

	$out .= mso_create_list($all, array('childs' => 'childs', 'format' => '<h3>[LINK][TITLE_HTML][/LINK] <sup>[COUNT]</sup></h3>' . $cat_descr, 'format_current' => '', 'class_ul' => '', 'title' => 'category_name', 'link' => 'category_slug', 'current_id' => false, 'prefix' => 'category/', 'count' => 'pages_count', 'slug' => 'category_slug', 'id' => 'category_id', 'menu_order' => 'category_menu_order', 'id_parent' => 'category_id_parent'));

	$out .= mso_hook('sitemap_posle') . '</div>';

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}

// вспомогательная для хлебных крошек
function sitemap_bread($r)
{
	$out = '<div class="sitemap-link">';

	$link_base = getinfo('siteurl') . 'sitemap';

	$s1 = '<a href="' . $link_base . '">' . tf('Группировка по датам') . '</a>';
	$s1_s = '<span class="sitemap-current">' . tf('Группировка по датам') . '</span>';

	$s2 = '<a href="' . $link_base . '/cat">' . tf('Группировка по рубрикам') . '</a>';
	$s2_s = '<span class="sitemap-current">' . tf('Группировка по рубрикам') . '</span>';

	$s3 = '<a href="' . $link_base . '/cat-list">' . tf('Только рубрики') . '</a>';
	$s3_s = '<span class="sitemap-current">' . tf('Только рубрики') . '</span>';

	$sep = '<span class="sitemap-sep"> / </span>';

	if ($r == 'date') {
		$out .= $s1_s . $sep . $s2 . $sep . $s3;
	} elseif ($r == 'cat') {
		$out .= $s1 . $sep . $s2_s . $sep . $s3;
	} elseif ($r == 'cat-list') {
		$out .= $s1 . $sep . $s2 . $sep . $s3_s;
	}

	$out .= '</div>';

	return $out;
}

# end of file
