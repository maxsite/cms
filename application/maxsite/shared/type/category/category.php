<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# подготовка данных

$full_posts = mso_get_option('category_full_text', 'templates', true); // полные или короткие записи
	
// параметры для получения страниц
$par = array( 
		'limit' => mso_get_option('limit_post', 'templates', '7'),
		'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
		'cat_order' => 'category_id_parent',
		'cat_order_asc' => 'asc',
		'type' => false,
		'content' => $full_posts
	); 

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('category-mso-get-pages')) require($f); 

$pages = mso_get_pages($par, $pagination);

if ($f = mso_page_foreach('category-head-meta')) 
{
	require($f);
}
else
{ 
	mso_head_meta('title', $pages, '%category_name%'); //  meta title страницы
	mso_head_meta('description', $pages, '%category_desc%'); // meta description страницы
	mso_head_meta('keywords', $pages, '%category_name%'); // meta keywords страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1))
		header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="mso-type-category"><section>' . NR;

if ($f = mso_page_foreach('category-do')) require($f);

if ($pages) // есть страницы
{
	if ($fn = mso_find_ts_file('type/category/units/category-header.php')) require($fn);

	if ($f = mso_page_foreach('category-do-pages')) require($f); // подключаем кастомный вывод
	if (function_exists('ushka')) echo ushka('category-do-pages');
	
	if ($fn = mso_find_ts_file('type/category/units/category-do-pages.php')) require($fn);
	
	// цикл вывода в отдельных юнитах
	
	if ($full_posts) // полные записи
	{
		if ($fn = mso_find_ts_file('type/category/units/category-full.php')) require($fn);
	}
	else // вывод в виде списка
	{
		if ($fn = mso_find_ts_file('type/category/units/category-list.php')) require($fn);
	}
	
	if ($f = mso_page_foreach('category-posle-pages')) require($f); // подключаем кастомный вывод
	if (function_exists('ushka')) echo ushka('category-posle-pages');
	
	mso_hook('pagination', $pagination);

}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
} // endif $pages

if ($f = mso_page_foreach('category-posle')) require($f); // подключаем кастомный вывод
	
echo NR . '</section></div><!-- /div.mso-type-category -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file