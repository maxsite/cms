<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// подготовка данных
$min_search_chars = 2; // минимальное кол-во симоволов при поиске

$search = mso_segment(2);

$search = mso_strip(strip_tags($search));
$searh_to_text = mb_strtolower($search, 'UTF8');

if ($fn = mso_page_foreach('search-head-meta'))
	require $fn;
else
	mso_head_meta('title', $search);

$search_len = true; // поисковая фраза более 2 символов

// параметры для получения страниц
if (!$search or ($search_len = (strlen(mso_slug($search)) < $min_search_chars))) // нет запроса или он короткий
{
	$search = tf('Поиск');
	$pages = false; // нет страниц
	$categories = false; // нет рубрик
	$tags = false; // нет меток
} else {
	$par = ['limit' => 7, 'cut' => false, 'type' => false];

	// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
	if ($fn = mso_page_foreach('search-mso-get-pages')) require $fn;

	$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	
	mso_set_val('mso_pages', $pages); // сохраняем массив для глобального доступа
	
	// рубрики
	$categories = [];

	// параметры ($type = 'page', $order = 'category_name', $asc = 'ASC', $type_page = 'blog', $cache = true)
	$all_categories = mso_cat_array_single(); // получаем все рубрики в один массив

	foreach ($all_categories as $cat) {
		// сверяем названия рубрик с исходной фразой
		$category_name = mb_strtolower($cat['category_name'], 'UTF8');

		// если нужно искать по части вхождения: плагин -> плагины, плагинюшки, плагинячки 
		if (strpos($category_name, $searh_to_text) !== false) $categories[$cat['category_slug']] = $cat['category_name'];
	}

	$tags = [];
	$all_tags = mso_get_all_tags_page(); // получаем все метки

	foreach ($all_tags as $key => $val) {
		// сверяем метки с исходной фразой
		$tag_name = mb_strtolower($key, 'UTF8');
		if (strpos($tag_name, $searh_to_text) !== false) $tags[] = $key;
	}
}

if (!$pages and !$categories and !$tags and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-search">';

if ($fn = mso_page_foreach('search-do')) {
	require $fn; // подключаем кастомный вывод 
} else {
	if ($pages or $categories or $tags) // есть страницы рубрики или метки
	{
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-search-content">'
			. '<h1 class="mso-type-search">' . tf('Поиск') . '</h1>'
			. '<div class="mso-page-content">'
			. '<p>' . tf('Результаты поиска по запросу')
			. ' <strong>«' . $search . '»</strong></p>';
	}
}

if ($categories) // есть рубрики
{
	echo '<h2>' . tf('Рубрики:') . '</h2><ul class="mso-search-res">';

	foreach ($categories as $key => $val) {
		echo '<li><a href="' . getinfo('siteurl') . 'category/' . $key . '">' . $val . '</a></li>';
	}

	echo '</ul>';
}

if ($tags) {
	// есть метки
	echo '<h2>' . tf('Метки:') . '</h2><ul class="mso-search-res">';

	foreach ($tags as $tag) {
		echo '<li><a href="' . getinfo('siteurl') . 'tag/' . urlencode($tag) . '">' . $tag . '</a></li>';
	}

	echo '</ul>';
}

if ($pages) {
	// есть страницы
	$max_word_count_do = 3; // колво слов до
	$max_word_count_posle = 7; // колво слов после

	echo '<h2>' . tf('Записи:') . ' ' . $pagination['rows'] . '</h2>';
	echo '<ul class="mso-search-res">';

	foreach ($pages as $page) // выводим в цикле
	{
		if ($fn = mso_page_foreach('search')) {
			require $fn; // подключаем кастомный вывод
			continue; // следующая итерация
		}
        
		extract($page);
       
        $ptitle = htmlspecialchars(str_replace(mb_strtoupper($searh_to_text), '_START_' . mb_strtoupper($searh_to_text) . '_END_', mb_strtoupper($page_title)));
        
        $ptitle = str_replace(['_START_', '_END_'], ['<span style="color: Teal; background: Aquamarine;">', '</span>'], $ptitle);
        
        echo '<li><h3><a href="' . getinfo('site_url') . 'page/' . $page_slug . '">' . $ptitle . '</a></h3>';
        
		// mso_page_title($page_slug, $ptitle, '<li><h3>', '</h3>', true);

		$page_content = strip_tags($page_content);

		// удалим переносы и табуляторы 
		$page_content = str_replace("\n", ' ', $page_content);
		$page_content = str_replace("\t", ' ', $page_content);

		// разобъем текст в массив по словам
		$arr = explode(' ', trim($page_content));

		// делаем срез до 500 элементов, чтобы не нагружать сервер
		$arr = array_slice($arr, 0, 500);

		// получим ключи всех вхождений
		$all_key = [];

		foreach ($arr as $key => $val) {
			if (mb_stripos(mb_strtolower($val, 'UTF8'), $searh_to_text, 0, 'UTF8') !== false) {
				// есть вхождение
				$all_key[] = $key;
			}
		}

		$out = ''; // результат

		// пройдемся по всем найденным
		// нужно сделать строки до вхождения и после на $max_word_count
		foreach ($all_key as $key) {
			$arr[$key] = '<span style="color: Tomato; background: Khaki;">'
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

		// кол-во совпадений — поскольку это срез части текста, то совпадений может не быть
		// просто ничего не выводим
		if ($cou > 0) {
			echo  '<p><em>' . tf('Совпадений') . ': ' . $cou . '</em></p>';
			echo '<p>' . $page_content . '</p>';
		}

		echo '</li>';
	} // end foreach

	echo '</ul>';

	if ($fn = mso_page_foreach('search-posle-pages')) require $fn; // подключаем кастомный вывод
	if (function_exists('ushka')) echo ushka('search-posle-pages');

	mso_hook('pagination', $pagination);
}

if ($pages or $categories or $tags) echo '</div></div></div>';

if (!$pages and !$categories and !$tags) {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn; // подключаем кастомный вывод
	} else {
		// стандартный вывод
		eval(mso_tmpl_ts('type/search/units/form-tmpl.php'));
	}
}

echo '</div><!-- class="mso-type-search" -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file
