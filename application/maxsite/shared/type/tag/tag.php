<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

$full_posts = mso_get_option('tag_full_text', 'templates', true); // полные или короткие записи

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
if ($f = mso_page_foreach('def-mso-get-pages')) require($f);
if ($f = mso_page_foreach('tag-mso-get-pages')) require($f); 
		
$pages = mso_get_pages($par, $pagination);

// meta title страницы
if ($f = mso_find_ts_file('type/tag/units/tag-head-meta.php')) 
	require($f);
elseif ($f = mso_page_foreach('tag-head-meta')) 
	require($f);
else
{
	$t = $d = htmlspecialchars(mso_segment(2));
	
	if (function_exists('ushka'))
	{
		$t = trim(htmlspecialchars(ushka('tag/' . mb_strtolower($t) . '/title', '', $t)));
		$d = trim(htmlspecialchars(ushka('tag/' . mb_strtolower($d) . '/descr', '', $d)));
	}
	
	mso_head_meta('title', $t);
	mso_head_meta('description', $d);
}


if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) 
	header('HTTP/1.0 404 Not Found'); 

if ($f = mso_find_ts_file('main/main-start.php')) require($f);

echo '<div class="mso-type-tag"><section>';

if ($f = mso_page_foreach('tag-do')) require($f);

if ($pages) // есть страницы
{
	if ($f = mso_find_ts_file('type/tag/units/tag-default.php')) require($f);
}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f);
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

if ($f = mso_page_foreach('tag-posle')) require($f);


echo '</section></div><!-- class="mso-type-tag" -->';

// конечная часть шаблона
if ($f = mso_find_ts_file('main/main-end.php')) require($f);

# end of file