<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

$full_posts = mso_get_option('author_full_text', 'templates', true); // полные или короткие записи

// параметры для получения страниц
$par = [
	'limit' => mso_get_option('limit_post', 'templates', '7'),
	'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
	'cat_order' => 'category_name',
	'cat_order_asc' => 'asc',
	// 'type' => false,
	'content' => $full_posts
];

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($fn = mso_page_foreach('def-mso-get-pages')) require $fn;
if ($fn = mso_page_foreach('author-mso-get-pages')) require $fn;

$pages = mso_get_pages($par, $pagination);

mso_set_val('mso_pages', $pages); // сохраняем массив для глобального доступа

// meta title страницы
if ($fn = mso_find_ts_file('type/author/units/author-head-meta.php')) require $fn;
elseif ($fn = mso_page_foreach('author-head-meta')) require $fn;
else {
	$t = $d = '';

	if (function_exists('ushka')) {
		// по идее здесь только число пусть будет обработка
		$t1 = $d1 = htmlspecialchars(mso_segment(2));

		$t = trim(htmlspecialchars(ushka('author/' . mb_strtolower($t1) . '/title', '', '')));
		$d = trim(htmlspecialchars(ushka('author/' . mb_strtolower($d1) . '/descr', '', '')));
	}

	if ($t)
		mso_head_meta('title', $t);
	else
		mso_head_meta('title', $pages, '%users_nik%||%title%', ' » '); //  meta title страницы

	if ($d)
		mso_head_meta('description', $d);
	else
		mso_head_meta('description', $pages, '%users_nik%'); // meta description страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-author"><section>';

if ($f = mso_page_foreach('author-do')) require $f;

if ($pages) {
	// есть страницы
	if ($f = mso_find_ts_file('type/author/units/author-default.php')) require $f;
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

if ($f = mso_page_foreach('author-posle')) require $f;

echo '<section></div><!-- class="mso-type-author" -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file
