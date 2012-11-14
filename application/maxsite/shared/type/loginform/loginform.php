<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
	
	echo NR . '<div class="type type_loginform">' . NR;
	
	
	if (is_login())
	{
		if (mso_segment(2) == 'error') mso_redirect('loginform');
		
		echo '<div class="loginform"><p class="ok user"><strong>'
				. tf('Привет,') . ' ' . getinfo('users_nik') . '!</strong><br>'
				. '<a href="' . getinfo('site_admin_url') . '">'. tf('Админ-панель') . '</a> | '
				. '<a href="' . getinfo('siteurl') . 'logout' . '">'. tf('выйти') . '</a>'
				. '</p></div>';
	}
	elseif ($comuser = is_login_comuser())
	{
		if (mso_segment(2) == 'error') mso_redirect('loginform');
		
		if (!$comuser['comusers_nik']) $cun = t('Привет!');
			else $cun = t('Привет,') . ' ' . $comuser['comusers_nik'] . '!';
		
		echo '<div class="loginform"><p class="ok comuser"><strong>' . $cun . '</strong><br>'
				. '<a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' 
				. t('своя страница') . '</a> | '
				.'<a href="' . getinfo('siteurl') . 'logout' . '">' . t('выйти') . '</a>'
				. '</p></div>';
	}
	else
	{
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl');

		if (mso_segment(2) == 'error')
			echo '<p class="message error small">' . tf('Неверный логин/пароль'). '</p>';

		echo '<div class="loginform"><p class="header">'. tf('Введите свой логин и пароль'). '</p>';
		
		// если разрешена регистрация, то выведем ссылку
		if (mso_get_option('allow_comment_comusers', 'general', '1') )
		{
			$reg = '<a href="' . getinfo('siteurl') . 'registration">' . tf('Регистрация') . '</a> | ';
		}
		else $reg = '';
		
		mso_login_form(array( 
			'login'=> tf('Логин'), 
			'password'=> tf('Пароль'), 
			'submit'=> '', 
			'submit_value'=> tf('Войти'), 
			'form_end'=>'<div class="form-end">' . $reg . '<a href="' . getinfo('siteurl') . 'password-recovery">'. tf('Забыли пароль?'). '</a> | '. '<a href="' . getinfo('siteurl') . '">'. tf('Вернуться к сайту'). '</a></div>'
			), 
			$redirect_url);
			
		echo '</div>';
	}

	echo NR . '</div><!-- class="type type_loginform" -->' . NR;

	if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
