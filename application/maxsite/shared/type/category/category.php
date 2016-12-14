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
	// category_unit шаблон вывода для определенной рубрики
	// slug рубрики = юнит-файл
	// news = 2col.php
	// ford = 3col.php
	// если в этой опции рубрика не задана, то используется стандартный вариант вывода
	// юнит файл располагается в шаблоне в type/category/units/
	// дефолтный юнит: type/category/units/category-default.php
	
	$use_unit = false; // флаг использования юнита
	
	if ($category_unit = mso_get_option('category_unit', getinfo('template'), ''))
	{
		$current_url = mso_segment(2); // текущая рубрика определяется по slug
		$category_unit = explode("\n", $category_unit);
		
		foreach ($category_unit as $elem)
		{
			$elem = explode("=", trim($elem));
			
			if (count($elem) == 2) // должно быть два элемента
			{
				$m1 = trim($elem[0]); // slug
				$m2 = trim($elem[1]); // файл
				
				if ($m1 === $current_url)
				{
					// есть совпадение
					if ($fn = mso_fe('type/category/units/' . $m2)) 
					{	
						$use_unit = $fn; // выставляем путь к файлу
					}
					
					break; // в любом случае рубим цикл
				}
			}
		}
	}
	
	if ($use_unit) 
		require($use_unit);
	else 
		// стандартный вывод
		if ($fn = mso_find_ts_file('type/category/units/category-default.php')) require($fn);
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