<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# подготовка данных
	$min_search_chars = 2; // минимальное кол-во симоволов при поиске
	
	$search = mso_segment(2);

	$search = mso_strip(strip_tags($search));
	$searh_to_text = mb_strtolower($search, 'UTF8');

	if ($f = mso_page_foreach('search-head-meta')) require($f);
	else
	{ 
		mso_head_meta('title', $search); //  meta title страницы
	}
	
	$search_len = true; // поисковая фраза более 2 символов
	
	// параметры для получения страниц
	if (!$search or ( $search_len = (strlen(mso_slug($search)) < $min_search_chars) ) ) // нет запроса или он короткий
	{
		$search = tf('Поиск');
		$pages = false; // нет страниц
		$categories = false; // нет рубрик
		$tags = false; // нет меток
	}
	else
	{
		$par = array( 'limit' => 7, 'cut'=>false, 'type'=>false ); 
		
		// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
		if ($f = mso_page_foreach('search-mso-get-pages')) require($f); 

		$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
		
		
		// рубрики
		$categories = array();
		
		// параметры ($type = 'page', $order = 'category_name', $asc = 'ASC', $type_page = 'blog', $cache = true)
		$all_categories = mso_cat_array_single(); // получаем все рубрики в один массив
		
		foreach($all_categories as $cat)
		{
			// сверяем названия рубрик с исходной фразой
			$category_name = mb_strtolower($cat['category_name'], 'UTF8');
			
			// если нужно искать по части вхождения: плагин -> плагины, плагинюшки, плагинячки 
			if ( strpos($category_name, $searh_to_text) !== false ) $categories[$cat['category_slug']] = $cat['category_name'];
			
		}
		
		
		// метки
		require_once( getinfo('common_dir') . 'meta.php' ); 
		
		$tags = array();
		$all_tags = mso_get_all_tags_page(); // получаем все метки
		
		foreach($all_tags as $key => $val)
		{
			// сверяем метки с исходной фразой
			$tag_name = mb_strtolower($key, 'UTF8');
			if ( strpos($tag_name, $searh_to_text) !== false ) $tags[] = $key;
		}
	}
	
if (!$pages and !$categories and !$tags and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_search">' . NR;

if ($f = mso_page_foreach('search-do')) require($f); // подключаем кастомный вывод
else 
{	
	if ($pages or $categories or $tags) // есть страницы рубрики или метки
	{
		echo '<div class="page_only"><div class="wrap">';
		
		echo '<h1>' . tf('Поиск') . '</h1>';
		
		echo '<div class="page_content">';
		
		echo '<p>' . tf('Результаты поиска по запросу') . ' <strong>«' . $search . '»</strong></p>';
	}
}


if ($categories) // есть рубрики
{
	echo '<h2>' . tf('Рубрики:') . '</h2><ul class="search-res">';
	foreach ($categories as $key => $val)
	{
		echo '<li><a href="' . getinfo('siteurl') . 'category/' . $key . '">' . $val . '</a></li>';
	}
	echo '</ul>';
}

if ($tags) // есть метки
{
	echo '<h2>' . tf('Метки:') . '</h2><ul class="search-res">';
	foreach ($tags as $tag)
	{
		echo '<li><a href="' . getinfo('siteurl') . 'tag/' . urlencode($tag) . '">' . $tag . '</a></li>';
	}
	echo '</ul>';
}


if ($pages) // есть страницы
{
	$max_word_count_do = 3; // колво слов до
	$max_word_count_posle = 7; // колво слов после
	
	echo '<h2>' . tf('Записи:') . '</h2>';
	
	// вывод найденных страниц
	echo '<ul class="search-res">';

	foreach ($pages as $page) : // выводим в цикле
		
		if ($f = mso_page_foreach('search')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		extract($page);
		
		mso_page_title($page_slug, $page_title, '<li><h3>', '</h3>', true);
		
		$page_content = strip_tags($page_content);
		
		// удалим переносы и табуляторы 
		$page_content = str_replace("\n", ' ', $page_content);
		$page_content = str_replace("\t", ' ', $page_content);
	
		// разобъем текст в массив по словам
		$arr = explode(' ', trim($page_content));
		
		// получим ключи всех вхождений
		$all_key = array(); 
		foreach ($arr as $key => $val)
		{
			if ( mb_stripos(mb_strtolower($val, 'UTF8'), $searh_to_text, 0, 'UTF8') !== false ) // есть вхождение
			{
				$all_key[] = $key;
			}
		}

		$out = ''; // результат
		
		// пройдемся по всем найденным
		// нужно сделать строки до вхождения и после на $max_word_count
		foreach ($all_key as $key) 
		{

			$arr[$key] = '<span style="color: red; background: yellow;">' 
						. str_replace($searh_to_text, '<strong>' . $searh_to_text . '</strong>', $arr[$key]) 
						. '</span>';
			
			$key_start = $key - $max_word_count_do;
			if ($key_start < 0) $key_start = 0;
			
			$a = array_slice($arr, $key_start, $max_word_count_posle + $max_word_count_do);
			
			// pr($a);
			$out .= ' &lt;...&gt; ' . implode(' ', $a);
		}
		
		$page_content = $out;
		$cou = count($all_key) + substr_count(mb_strtolower($page_title, 'UTF8'), $searh_to_text);
		
		// кол-во совпадений
		echo  '<p><em>' . tf('Совпадений') . ': ' . $cou . '</em></p>';
		echo '<p>' . $page_content . '</p>';

		echo '</li>';
	
	endforeach;
	
	echo '</ul>';
	
	echo '</div></div></div>';
	
	mso_hook('pagination', $pagination);
}

if (!$pages and !$categories and !$tags)
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<div class="page_only"><div class="wrap">';
		
		echo '<h1>'. tf('404. Ничего не найдено...'). '</h1>';
		
		echo '<div class="page_content">';
		
		if ($search_len) echo '<p>'. tf('Поисковая фраза должна быть не менее ' . $min_search_chars . ' символов.') . '</p>';
		
		echo '<p>'. tf('Попробуйте повторить поиск.') . '</p>';

		echo '
		<p><form name="f_search" method="get" onsubmit="location.href=\'' . getinfo('siteurl') . 'search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;">	<input type="text" class="text" name="s" size="20" onfocus="if (this.value == \''. tf('что искать?'). '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''. tf('что искать?'). '\';}" value="'. tf('что искать?'). '">&nbsp;<input type="submit" class="submit" name="Submit" value="  '. tf('Поиск'). '  "></form></p>';
		
		echo mso_hook('page_404');
		
		echo '</div></div></div>';
	}
	
} // endif $pages

echo NR . '</div><!-- class="type type_search" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>