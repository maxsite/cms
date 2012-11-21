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

echo NR . '<div class="type type_category"><section>' . NR;

if ($f = mso_page_foreach('category-do')) require($f);

if ($pages) // есть страницы
{ 	
	echo NR2 . '<header>';
	
		if ($f = mso_page_foreach('category-header')) 
		{
			require($f);
		}
		else
		{
			echo '<h1 class="category">'
				. htmlspecialchars(mso_get_cat_key('category_name')) 
				. '</h1>';
		}

		if ( mso_get_option('category_show_rss_text', 'templates', 1) )
		{
			if ($f = mso_page_foreach('category-show-rss-text'))
			{
				require($f);
			}
			else
			{
				echo 
					mso_get_val('show_rss_text_start', '<p class="show_rss_text">') 
					. '<a href="' . getinfo('siteurl') . mso_segment(1) 
					. '/' . mso_segment(2) 
					. '/feed">'
					. tf('Подписаться на эту рубрику по RSS'). '</a>' 
					.  mso_get_val('show_rss_text_end', '</p>');
			}
		}
		
		if ($f = mso_page_foreach('category-show-desc'))
		{
			require($f); // подключаем кастомный вывод
		}
		else
		{
			// описание рубрики
			if ($category_desc = mso_get_cat_key('category_desc'))
				echo '<div class="category_desc">' . $category_desc . '</div>';
		}
	
	echo '</header>' . NR2;
	
	
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
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
	}
} // endif $pages

if ($f = mso_page_foreach('category-posle')) require($f); // подключаем кастомный вывод
	
echo NR . '</section></div><!-- /div.type type_category -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file