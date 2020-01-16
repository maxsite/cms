<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

require_once getinfo('common_dir') . 'comments.php';

//$res_post = mso_comuser_lost(); // обработка отправленных данных - возвращает результат
$res_post = mso_comuser_edit();

$comuser_info = mso_get_comuser(mso_segment(2)); // получим всю информацию о комюзере

if ($fn = mso_page_foreach('users-form-lost-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Восстановление пароля') . '. ' .  getinfo('title')); // meta title страницы

if (!$comuser_info and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-users-form-lost">';
echo $res_post;

if ($comuser_info) {
	extract($comuser_info[0]);

	if ($fn = mso_page_foreach('users-form-lost')) {
		require $fn; // подключаем кастомный вывод
	} else {
		$session_id = mso_form_session('f_session_id');

		if ($comusers_nik)
			echo '<h1>' . $comusers_nik . '</h1>';
		else
			echo '<h1>' . tf('Комментатор') . ' ' . $comusers_id . '</h1>';

		echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">' . tf('Персональная страница') . '</a></p>';

		// если активация не завершена, то вначале требуем её завершить
		if ($comusers_activate_string != $comusers_activate_key) {
			// нет активации
			$admin_email = mso_get_option('admin_email', 'general', '-');

			eval(mso_tmpl_ts('type/users/units/users-lost-no-activate-tmpl.php'));
		} else {
			// активация завершена - можно вывести поля для восстановления пароля
			eval(mso_tmpl_ts('type/users/units/users-replace-password-tmpl.php'));
		}
	} // mso_page_foreach
} else {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn; // подключаем кастомный вывод
	} else // стандартный вывод
	{
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

echo '</div><!-- class="mso-type-users-form-lost" -->';

if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file
