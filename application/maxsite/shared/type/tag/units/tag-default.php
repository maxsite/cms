<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

if ($fn = mso_find_ts_file('type/tag/units/tag-out.php')) {
	require $fn;
} else {
	// стандартный вывод метки

	if ($fn = mso_page_foreach('tag-header-all'))
		require $fn;
	else
		if ($fn = mso_find_ts_file('type/tag/units/tag-header.php')) require $fn;

	if ($fn = mso_find_ts_file('type/tag/units/tag-do-pages.php')) require $fn;
	if (function_exists('ushka')) echo ushka('tag-do-pages');

	// цикл вывода в отдельных юнитах
	if ($full_posts) {
		if ($fn = mso_find_ts_file('type/tag/units/tag-full.php')) require $fn; // полные записи
	} else {
		if ($fn = mso_find_ts_file('type/tag/units/tag-list.php')) require $fn; // вывод в виде списка
	}

	if ($fn = mso_page_foreach('tag-posle-pages')) require $fn; // подключаем кастомный вывод
	if (function_exists('ushka')) echo ushka('tag-posle-pages');

	mso_hook('pagination', $pagination);
}

# end of file
