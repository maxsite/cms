<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if ($fn = mso_find_ts_file('type/author/units/author-out.php')) {
	require $fn;
} else {
	if ($fn = mso_page_foreach('author-header-all')) {
		require $fn;
	} else
		if ($fn = mso_find_ts_file('type/author/units/author-header.php')) require $fn;

	if ($fn = mso_find_ts_file('type/author/units/author-do-pages.php')) require $fn;
	if (function_exists('ushka')) echo ushka('author-do-pages');

	// цикл вывода в отдельных юнитах
	if ($full_posts) {
		// полные записи
		if ($fn = mso_find_ts_file('type/author/units/author-full.php')) require $fn;
	} else {
		// вывод в виде списка
		if ($fn = mso_find_ts_file('type/author/units/author-list.php')) require $fn;
	}

	if ($fn = mso_page_foreach('author-posle-pages')) require $fn; // подключаем кастомный вывод
	if (function_exists('ushka')) echo ushka('author-posle-pages');

	mso_hook('pagination', $pagination);
}

# end of file
