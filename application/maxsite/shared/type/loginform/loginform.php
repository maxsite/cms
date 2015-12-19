<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

echo NR . '<div class="mso-type-loginform"><div class="mso-page-only"><div class="mso-page-content mso-type-loginform-content">';

if (is_login())
{
	if (mso_segment(2) == 'error') mso_redirect('loginform');
	
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
	$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl');
	
	if (mso_segment(2) == 'error')
	{
		eval(mso_tmpl_ts('type/loginform/units/loginform-error.php'));
	}	

	// если разрешена регистрация, то выведем ссылку
	if (mso_get_option('allow_comment_comusers', 'general', '1') )
	{
		$reg = '<a href="' . getinfo('siteurl') . 'registration">' . tf('Регистрация') . '</a> | ';
	}
	else $reg = '';
	
	$login_form = mso_login_form(
		array( 
			'login'=> tf('Логин'), 
			'password'=> tf('Пароль'), 
			'submit'=> '', 
			'submit_value'=> tf('Войти'), 
			'form_end'=>'<p class="mso-loginform-end">' . $reg . '<a href="' . getinfo('siteurl') . 'password-recovery">' . tf('Забыли пароль?') . '</a> | '. '<a href="' . getinfo('siteurl') . '">' . tf('Вернуться к сайту') . '</a></p>'
			),
		$redirect_url, false);
	
	
	eval(mso_tmpl_ts('type/loginform/units/loginform-tmpl.php'));
}

echo '</div></div></div><!-- class="mso-type-loginform" -->' . NR;

if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

# end file