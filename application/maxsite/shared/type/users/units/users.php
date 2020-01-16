<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once(getinfo('common_dir') . 'comments.php');

// получим всю информацию о комюзере - номер в сегменте url
$comuser_info = mso_get_comuser(mso_segment(2));

if ($fn = mso_page_foreach('users-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Комментаторы') . '. ' . getinfo('title')); // meta title страницы

if (!$comuser_info and mso_get_option('page_404_http_not_found', 'templates', 1))
	header('HTTP/1.0 404 Not Found');

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-users"><div class="mso-page-only"><div class="mso-page-content mso-type-users-content">';

if ($comuser_info) {
	extract($comuser_info[0]);

	if ($f = mso_page_foreach('users')) {
		require $f;
	} else {
		$avatar_info = $comuser_info[0];
		$avatar_info['users_avatar_url'] = $avatar_info['users_email'] = '';
		$avatar = mso_avatar($avatar_info, '', false,  false, true); // только адрес граватарки

		if (!$comusers_nik) $comusers_nik = tf('Комментатор') . ' ' . $comusers_id;

		// нет активации
		if ($comusers_activate_string != $comusers_activate_key)
			$no_activation_link = getinfo('siteurl') . 'users/' . $comusers_id . '/edit';
		else
			$no_activation_link = '';

		if ($comusers_description) {
			$comusers_description = mso_clean_str($comusers_description);
			$comusers_description = str_replace("\n", '<br>', $comusers_description);
			$comusers_description = str_replace('<br><br>', '<br>', $comusers_description);
		}

		// скрыть дату рождения для всех
		// if (!$comusers_date_birth 
		// 	or $comusers_date_birth == '1970-01-01 00:00:00' 
		// 	or $comusers_date_birth == '0000-00-00 00:00:00') 
		// 	$comusers_date_birth = '';
		$comusers_date_birth = '';

		if (getinfo('comusers_id') == $comusers_id)
			$edit_link = getinfo('siteurl') . 'users/' . $comusers_id . '/edit';
		else
			$edit_link = '';

		eval(mso_tmpl_ts('type/users/units/users-tmpl.php'));

		// хук, по которому можно вывести дополнительные данные
		mso_hook('users_add_out', $comuser_info[0]);

		if ($fn = mso_page_foreach('users-add')) require $fn;

		// есть комментарии
		if ($comments)
			eval(mso_tmpl_ts('type/users/units/users-comments-tmpl.php'));
	} // mso_page_foreach

	if ($fn = mso_page_foreach('users-posle')) require $fn;
} else {
	if ($fn = mso_page_foreach('pages-not-found')) {
		require $fn;
	} else {
		// стандартный вывод
		echo '<div class="mso-page-only"><div class="mso-page-content mso-type-page_404">';
		echo '<h1>' . tf('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . tf('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		echo '</div></div>';
	}
}

echo '</div></div></div><!-- class="mso-type-users" -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;

# end of file
