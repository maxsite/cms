<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

$full_posts = mso_get_option('archive_full_text', 'templates', true); // полные или короткие записи

// параметры для получения страниц
$par = array(
		'limit' => mso_get_option('limit_post', 'templates', '7'),
		'cut' => mso_get_option('more', 'templates', tf('Читать полностью »')),
		'cat_order' => 'category_name', 
		'cat_order_asc' => 'asc', 
		'type' => false,
		'content' => $full_posts
	); 


// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('archive-mso-get-pages')) require($f); 
		
$pages = mso_get_pages($par, $pagination);

if ($f = mso_page_foreach('archive-head-meta')) 
{
	require($f);
}
else
{
	mso_head_meta('title', tf('Архивы') . '. ' . getinfo('title') ); //  meta title страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) 
	header('HTTP/1.0 404 Not Found'); 


if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="type type_archive">' . NR;

if ($f = mso_page_foreach('archive-do')) 
		require($f);
	else 
		echo '<h1 class="category archive">' . tf('Архивы') . '</h1>';

if ($pages) // есть страницы
{
	// цикл вывода в отдельных юнитах

	if ($full_posts) // полные записи
	{
		if ($fn = mso_find_ts_file('type/archive/units/archive-full.php')) require($fn);
	}
	else // вывод в виде списка
	{
		if ($fn = mso_find_ts_file('type/archive/units/archive-list.php')) require($fn);
	}
	
	if ($f = mso_page_foreach('archive-posle-pages')) require($f);
	
	mso_hook('pagination', $pagination);
}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f);
	}
	else // стандартный вывод
	{
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
	}
} // endif $pages

if ($f = mso_page_foreach('archive-posle')) require($f);


echo NR . '</div><!-- class="type type_archive" -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file