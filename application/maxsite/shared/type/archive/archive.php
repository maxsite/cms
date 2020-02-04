<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$full_posts = mso_get_option('archive_full_text', 'templates', true); // полные или короткие записи

// параметры для получения страниц
$par = [
	'limit' => mso_get_option('limit_post', 'templates', '7'),
	'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
	'cat_order' => 'category_name',
	'cat_order_asc' => 'asc',
	'type' => false,
	'content' => $full_posts
];

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($fn = mso_page_foreach('def-mso-get-pages')) require $fn;
if ($fn = mso_page_foreach('archive-mso-get-pages')) require $fn;

$pages = mso_get_pages($par, $pagination);

mso_set_val('mso_pages', $pages); // сохраняем массив для глобального доступа

if ($f = mso_page_foreach('archive-head-meta')) {
	require $fn;
} else {
	mso_head_meta('title', tf('Архивы') . '. ' . getinfo('title')); //  meta title страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-archive">';

if ($f = mso_page_foreach('archive-header'))
	require $f;
else
	echo '<h1 class="mso-archive">' . tf('Архивы') . '</h1>';

if ($pages) {
	// есть страницы

	if ($fn = mso_find_ts_file('type/archive/units/archive-do-pages.php')) require $fn;
	if (function_exists('ushka')) echo ushka('archive-do-pages');

	// цикл вывода в отдельных юнитах
	if ($full_posts) {
		// полные записи
		if ($fn = mso_find_ts_file('type/archive/units/archive-full.php')) require $fn;
	} else {
		// вывод в виде списка
		if ($fn = mso_find_ts_file('type/archive/units/archive-list.php')) require $fn;
	}

	if ($fn = mso_page_foreach('archive-posle-pages')) require $fn;
	if (function_exists('ushka')) echo ushka('archive-posle-pages');

	mso_hook('pagination', $pagination);
} else {
	if ($f = mso_page_foreach('pages-not-found')) {
		require $f;
	} else {
		// стандартный вывод
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

if ($f = mso_page_foreach('archive-posle')) require $f;

echo '</div><!-- mso-type-archive -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
