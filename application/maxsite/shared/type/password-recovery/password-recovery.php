<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

require_once( getinfo('common_dir') . 'comments.php' );


$res_post = mso_comuser_lost(array('password_recovery' => true)); // обработка отправленных данных - возвращает результат


if ($f = mso_page_foreach('password-recovery-head-meta')) require($f);
else
{
	mso_head_meta('title', tf('Восстановление пароля') . '. '.  getinfo('title')); // meta title страницы
}


// теперь сам вывод
# начальная часть шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="mso-type-password-recovery"><div class="mso-page-only"><div class="mso-page-content mso-type-loginform-content">';

echo $res_post;
	
if ($f = mso_page_foreach('password-recovery')) 
{
	require($f); // подключаем кастомный вывод
}
else
{
	if (is_login())
	{
		eval(mso_tmpl_ts('type/loginform/units/loginform-user-tmpl.php'));
	}
	elseif ($comuser = is_login_comuser())
	{
		if (mso_segment(2) == 'error') mso_redirect('loginform');
		
		if (!$comuser['comusers_nik']) $hello = t('Привет!');
			else $hello = t('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
		
		eval(mso_tmpl_ts('type/loginform/units/loginform-comuser-tmpl.php'));
	}
	else
	{
		eval(mso_tmpl_ts('type/password-recovery/units/password-recovery-tmpl.php'));
	}
}

echo NR . '</div></div></div><!-- class="mso-type-password-recovery" -->' . NR;

# конечная часть шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	
# end file