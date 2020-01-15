<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

require_once getinfo('common_dir') . 'comments.php';

// обработка отправленных данных - возвращает результат
$res_post = mso_comuser_lost(array('password_recovery' => true));

if ($fn = mso_page_foreach('password-recovery-head-meta'))
	require $fn;
else
	mso_head_meta('title', tf('Восстановление пароля') . '. ' .  getinfo('title')); // meta title страницы

// начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require $fn;

echo '<div class="mso-type-password-recovery"><div class="mso-page-only"><div class="mso-page-content mso-type-loginform-content">';

echo $res_post;

if ($fn = mso_page_foreach('password-recovery')) {
	require $fn; // подключаем кастомный вывод
} else {
	if (is_login()) {
		eval(mso_tmpl_ts('type/loginform/units/loginform-user-tmpl.php'));
	} elseif ($comuser = is_login_comuser()) {
		if (mso_segment(2) == 'error') mso_redirect('loginform');

		if (!$comuser['comusers_nik'])
			$hello = t('Привет!');
		else
			$hello = t('Привет,') . ' ' . $comuser['comusers_nik'] . '!';

		eval(mso_tmpl_ts('type/loginform/units/loginform-comuser-tmpl.php'));
	} else {
		eval(mso_tmpl_ts('type/password-recovery/units/password-recovery-tmpl.php'));
	}
}

echo '</div></div></div><!-- class="mso-type-password-recovery" -->';

// конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require $fn;
	
# end of file
