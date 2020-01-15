<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

require_once(getinfo('common_dir') . 'comments.php');

$comusers = mso_get_comusers_all(); // получим всех комюзеров

if ($fn = mso_page_foreach('users-all-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Комментаторы') . '. ' . getinfo('title')); // meta title страницы

if (!$comusers and mso_get_option('page_404_http_not_found', 'templates', 1)) header('HTTP/1.0 404 Not Found');

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-users-all"><div class="mso-page-only"><div class="mso-page-content mso-type-users-all">';

if ($comusers) {
	if ($fn = mso_page_foreach('users-all-do'))
		require $fn; // подключаем кастомный вывод
	else
		echo '<h1 class="mso-type-users-all">' . tf('Комментаторы') . '</h1><p>' . tf('Забыли кто вы?') . ' <a href="' . getinfo('siteurl') . 'password-recovery">' . tf('Можно восстановить пароль.') . '</a></p><ul class="mso-users-all">';

	// pr($comusers);
	foreach ($comusers as $comuser) {
		if ($fn = mso_page_foreach('users-all')) {
			require $fn; // подключаем кастомный вывод
			continue; // следующая итерация
		}

		if (!$comuser['comusers_nik'])
			$comuser['comusers_nik'] = tf('Комментатор') . ' ' . $comuser['comusers_id'];

		echo '<li><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . $comuser['comusers_nik'] . '</a></li>';
	}

	if ($fn = mso_page_foreach('users-all-posle'))
		require $fn;
	else
		echo '</ul>';
} else {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn; // подключаем кастомный вывод
	} else {
		// стандартный вывод
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

echo '</div></div></div><!-- class="mso-type-users-all" -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file
