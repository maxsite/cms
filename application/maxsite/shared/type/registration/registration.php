<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

// проверяем запрет регистраций/комментирование комюзеров
if (!mso_get_option('allow_comment_comusers', 'general', '1') )
{
	echo '<div class="mso-message-alert">' . tf('На сайте запрещена регистрация комюзеров.') . '</div>';
	if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
	return;
}

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
	
	// для запоминания уже введенных полей
	$vreg_email = $vreg_password = $vreg_password_repeat = $vreg_nik = $vreg_url = '';
	
	if ( $post = mso_check_post(array('freg_session_id', 'freg_rules_ok', 'freg_email', 'freg_password', 'freg_password_repeat', 'freg_nik', 'freg_url', 'freg_submit')) )
	{
		mso_checkreferer();
		
		// обработка _post
		$post = mso_clean_post(array(
			'freg_email' => 'email',
			'freg_password' => 'base',
			'freg_password_repeat' => 'base',
			'freg_nik' => 'base|not_url',
			'freg_url' => 'base',
			'freg_redirect_url' => 'base'
			), $post);
		

		// подставим введенные поля
		$vreg_email = $post['freg_email'];
		$vreg_password = $post['freg_password'];
		$vreg_password_repeat = $post['freg_password_repeat'];
		$vreg_nik = $post['freg_nik'];
		$vreg_url = $post['freg_url'];
		
		
		// проверки введенных данных
		
		$error = '';
		
		if (!$post['freg_rules_ok'])
		{
			$error .= '<div class="mso-message-error">' . tf('Необходимо принять правила сайта') . '</div>';
		}
		
		if (!$post['freg_email'])
		{
			$error .= '<div class="mso-message-error">' . tf('Не указан email') . '</div>';
		}
		else
		{
			// email указан, проверим его корректность
			$email = mso_clean_str($post['freg_email'], 'email');
			
			if (!$email)
			{
				$error .= '<div class="mso-message-error">' . tf('Неверный email') . '</div>';
			}
		}
		
		if (!$post['freg_password'])
		{
			$error .= '<div class="mso-message-error">' . tf('Не указан пароль') . '</div>';
			
			$vreg_password = '';
			$vreg_password_repeat = '';
		}
		else
		{
			if ( strlen($post['freg_password']) < 6) 
				$error .= '<div class="mso-message-error">' . tf('Длина пароля должна быть более 6 символов') . '</div>';
		}
		
		if ($post['freg_password'] != $post['freg_password_repeat'])
		{
			$vreg_password = '';
			$vreg_password_repeat = '';
			
			$error .= '<div class="mso-message-error">' . tf('Пароль и его повтор не совпадают') . '</div>';
		}
		
		
		if ($error) 
		{
			echo $error;
		}
		else
		{
			// нет ошибок
			
			require_once(getinfo('common_dir') . 'comments.php');
			
			// подготавливаем данные для mso_comuser_auth
			
			$data = array();
			
			$data['allow_create_new_comuser'] = true; // явно разрешить регистрацию
			$data['die'] = false; // получаем результат в случае ошибки
			
			$data['password'] = $vreg_password;
			$data['comusers_nik'] = $vreg_nik;
			$data['comusers_url'] = $vreg_url;
			$data['email'] = $vreg_email;
			
			if (isset($post['freg_redirect_url'])) 
					$data['redirect'] = $post['freg_redirect_url'];
				else 
					$data['redirect'] = getinfo('siteurl') . 'registration';
			
			// функция сама средиректит куда нужно
			// из-за этого форма ниже не будет отображена в случае успеха
			$res = mso_comuser_auth($data);
			
			// если ошибка, то выводим сообщение
			echo '<div class="mso-message-alert">' . $res . '</div>';
		}
	}

	// форма регистрации
	
	$action = getinfo('siteurl') . 'registration';

	if ($rules = mso_get_option('rules_site', 'general', '')) 
		$rules = ' (<a href="' . $rules . '" target="_blank">' . tf('Правила сайта') . '</a>)';
	else
		$rules = '';
	
	eval(mso_tmpl_ts('type/registration/units/registration-tmpl.php'));
}

echo '</div></div></div><!-- class="mso-type-loginform" -->' . NR;

if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

# end file