<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

require_once(getinfo('common_dir') . 'comments.php');

// обработка отправленных данных - возвращает результат
$res_post = mso_comuser_edit();

// получим всю информацию о комюзере из сессии или url
$comuser_info = mso_get_comuser();

// отображение формы залогирования
$login_form = !is_login_comuser();

// если нет данных юзера, то выводим форму
if (!$comuser_info) $login_form = true;

if ($fn = mso_page_foreach('users-form-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Форма редактирования комментатора') . '. ' . getinfo('title')); // meta title страницы

if (!$comuser_info and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

// теперь сам вывод
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-users-form">';
echo $res_post;

if ($comuser_info) {
	extract($comuser_info[0]);

	echo '<div class="mso-page-only">';

	// pr($comuser_info[0]);
	if ($fn = mso_page_foreach('users-form')) {
		require $fn; // подключаем кастомный вывод
	} else {
		if ($comusers_nik)
			echo '<h1>' . $comusers_nik . '</h1>';
		else
			echo '<h1>' . tf('Комментатор') . ' ' . $comusers_id . '</h1>';

		echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">' . tf('Персональная страница') . '</a>';

		if (!$login_form) {
			// echo ' | <a href="' . getinfo('siteurl') . 'password-recovery">' . tf('Сменить пароль') . '</a>';
			echo ' | <a href="' . getinfo('siteurl') . 'logout">' . tf('Выход') . '</a>';
		}

		echo '</p>';

		$session_id = mso_form_session('f_session_id');

		// если активация не завершена, то вначале требуем её завершить
		if ($comusers_activate_string != $comusers_activate_key) {
			// нет активации
			$admin_email = mso_get_option('admin_email', 'general', '-');

			eval(mso_tmpl_ts('type/users/units/users-lost-no-activate-tmpl.php'));
		} else {
			// активация завершена - можно вывести поля для редактирования
			if ($login_form) {
				// нужно отобразить форму
				$login_redirect = getinfo('siteurl') . 'users/' . $comusers_id . '/edit';
				$lost_link = getinfo('siteurl') . 'users/' . $comusers_id . '/lost';

				eval(mso_tmpl_ts('type/users/units/users-loginform-tmpl.php'));
			} else {
				$comusers_description = htmlspecialchars(strip_tags($comusers_description));

				// чекбоксы
				$check_subscribe_my_comments = (isset($comusers_meta['subscribe_my_comments']) and $comusers_meta['subscribe_my_comments'] == '1') ? ' checked="checked"' : '';

				$check_subscribe_other_comments = (isset($comusers_meta['subscribe_other_comments']) and $comusers_meta['subscribe_other_comments'] == '1') ? ' checked="checked"' : '';

				$check_subscribe_new_pages = (isset($comusers_meta['subscribe_new_pages']) and $comusers_meta['subscribe_new_pages'] == '1') ? ' checked="checked"' : '';

				$check_subscribe_admin = (isset($comusers_meta['subscribe_admin']) and $comusers_meta['subscribe_admin'] == '1') ? ' checked="checked"' : '';

				// в файле можно выполнить инициализацию переменных, которую можно использовать
				// в форме в type_foreach-файлах users-form-edit-tmpl1 и users-form-edit-tmpl2
				if ($fn = mso_page_foreach('users-form-edit-tmpl0')) require $fn;

				eval(mso_tmpl_ts('type/users/units/users-form-edit-tmpl.php'));
			}
		}
	} // mso_page_foreach

	echo '</div>';
} else {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn;
	} else {
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

echo '</div><!-- mso-type-users-form -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
