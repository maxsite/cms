<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$full_posts = mso_get_option('category_full_text', 'templates', true); // полные или короткие записи

// параметры для получения страниц
$par = [
	'limit' => mso_get_option('limit_post', 'templates', '7'),
	'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
	'cat_order' => 'category_id_parent',
	'cat_order_asc' => 'asc',
	'type' => false,
	'content' => $full_posts
];

// сортировка рубрик может быть задана в опции category_sort шаблона
// news = page_date_publish asc
// book = page_menu_order desc
if ($category_sort = mso_get_option('category_sort', getinfo('template'), '')) {
	if ($sort = mso_text_find_key($category_sort, mso_segment(2))) {
		$oa = mso_explode($sort, false);

		// order ставим как есть, не проверяя, поскольку их слишком много
		if (isset($oa[0])) $par['order'] = $oa[0];

		// asc проверяем на корректность
		if (isset($oa[1]) and ($oa[1] == 'asc' or $oa[1] == 'desc' or $oa[1] == 'random'))
			$par['order_asc'] = $oa[1];
	}
}

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($fn = mso_page_foreach('def-mso-get-pages')) require $fn;
if ($fn = mso_page_foreach('category-mso-get-pages')) require $fn;

$pages = mso_get_pages($par, $pagination);

mso_set_val('mso_pages', $pages); // сохраняем массив для глобального доступа

if ($fn = mso_find_ts_file('type/category/units/category-head-meta.php')) require $fn;
elseif ($fn = mso_page_foreach('category-head-meta')) require $fn;
else {
	mso_head_meta('title', $pages, '%category_name%'); //  meta title страницы
	mso_head_meta('description', $pages, '%category_desc%'); // meta description страницы
	mso_head_meta('keywords', $pages, '%category_name%'); // meta keywords страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-category"><section>';

if ($fn = mso_page_foreach('category-do')) require $fn;

if ($pages) {
	// category_unit шаблон вывода для определенной рубрики
	// slug рубрики = юнит-файл
	// news = 2col.php
	// ford = 3col.php
	// если в этой опции рубрика не задана, то используется стандартный вариант вывода
	// юнит файл располагается в шаблоне в type/category/units/
	// дефолтный юнит: type/category/units/category-default.php

	$use_unit = false; // флаг использования юнита

	if ($category_unit = mso_get_option('category_unit', getinfo('template'), '')) {
		if ($u = mso_text_find_key($category_unit, mso_segment(2))) {
			if ($fn = mso_fe('type/category/units/' . $u)) {
				$use_unit = $fn; // выставляем путь к файлу
			}
		}
	}

	// если нет юнита, возможно указан дефолтный
	if (!$use_unit and ($category_unit_default = mso_get_option('category_unit_default', getinfo('template'), ''))) {
		if ($fn = mso_fe('type/category/units/' . $category_unit_default)) {
			$use_unit = $fn; // выставляем путь к файлу
		}
	}

	if ($use_unit)
		require $use_unit;
	else
		// стандартный вывод
		if ($fn = mso_find_ts_file('type/category/units/category-default.php')) require $fn;
} else {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn; // подключаем кастомный вывод
	} else {
		// стандартный вывод
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

if ($fn = mso_page_foreach('category-posle')) require $fn; // подключаем кастомный вывод

echo '</section></div><!-- /div.mso-type-category -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
