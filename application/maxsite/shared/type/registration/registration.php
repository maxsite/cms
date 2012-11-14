<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);
	
	// проверяем запрет регистраций/комментирование комюзеров
	if (!mso_get_option('allow_comment_comusers', 'general', '1') )
	{
		echo '<div class="message alert small ">' . tf('На сайте запрещена регистрация комюзеров.') . '</div>';
		
		require(getinfo('template_dir') . 'main-end.php');
		
		return;
	}
	
	
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
			
			
			// pr($post);
			
			// подставил введенные поля
			$vreg_email = $post['freg_email'];
			$vreg_password = $post['freg_password'];
			$vreg_password_repeat = $post['freg_password_repeat'];
			$vreg_nik = $post['freg_nik'];
			$vreg_url = $post['freg_url'];
			
			
			// проверки введенных данных
			
			$error = '';
			
			if (!$post['freg_rules_ok'])
			{
				$error .= '<div class="message error small">' . tf('Необходимо принять правила сайта') . '</div>';
			}
			
			if (!$post['freg_email'])
			{
				$error .= '<div class="message error small">' . tf('Не указан email') . '</div>';
			}
			else
			{
				// email указан, проверим его корректность
				$email = mso_clean_str($post['freg_email'], 'email');
				
				if (!$email)
				{
					$error .= '<div class="message error small">' . tf('Неверный email') . '</div>';
				}
			}
			
			if (!$post['freg_password'])
			{
				$error .= '<div class="message error small">' . tf('Не указан пароль') . '</div>';
				
				$vreg_password = '';
				$vreg_password_repeat = '';
			}
			else
			{
				if ( strlen($post['freg_password']) < 6) 
					$error .= '<div class="message error small">' . tf('Длина пароля должна быть более 6 символов') . '</div>';
			}
			
			if ($post['freg_password'] != $post['freg_password_repeat'])
			{
				$vreg_password = '';
				$vreg_password_repeat = '';
				
				$error .= '<div class="message error small">' . tf('Пароль и его повтор не совпадают') . '</div>';
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
				echo '<div class="message alert small">' . $res . '</div>';
			}
		}

		
		echo '<div class="loginform"><p class="header">'. tf('Укажите данные для регистрации на сайте'). '</p>';

		// форма регистрации
		
		$action = getinfo('siteurl') . 'registration';
		$session_id = $MSO->data['session']['session_id'];
		$email = tf('Email (используется как логин)');
		$password = tf('Пароль (английские буквы и цифры, без пробелов, минимум 6 символов)');
		$password_repeat = tf('Повторите пароль');
		
		$submit_value = tf('Зарегистрироваться');
		$nik = tf('Имя');
		$url = tf('Адрес сайта (если есть)');
		
		$rules_ok = tf('Обязуюсь соблюдать правила сайта');
		
		if ($rules = mso_get_option('rules_site', 'general', '')) 
		{
			$rules = ' (<a href="' . $rules . '" target="_blank">' . tf('Правила сайта') . '</a>)';
		}
		else
		{
			$rules = '';
		}
		
	
		$form = <<<EOF
	<form method="post" action="{$action}" name="freg" class="freg fform">
		<input type="hidden" value="{$session_id}" name="freg_session_id">
		<input type="hidden" value="0" name="freg_rules_ok">
		<input type="hidden" value="{$redirect_url}" name="freg_redirect_url">
		
		<p>
			<label><span class="nocell ftitle">{$email}</span>
			<input type="email" name="freg_email" value="{$vreg_email}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password}</span>
			<input type="password" name="freg_password" value="{$vreg_password}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$password_repeat}</span>
			<input type="password" name="freg_password_repeat" value="{$vreg_password_repeat}">
			</label>
		</p>		
		
		<p>
			<label><span class="nocell ftitle">{$nik}</span>
			<input type="text" name="freg_nik" value="{$vreg_nik}">
			</label>
		</p>
		
		<p>
			<label><span class="nocell ftitle">{$url}</span>
			<input type="text" name="freg_url" value="{$vreg_url}">
			</label>
		</p>
		
		<p>
			<label><input type="checkbox" name="freg_rules_ok"> {$rules_ok} {$rules}</label>
		</p>
		
		<hr>
		
		<p>
			<span><button type="submit" name="freg_submit">{$submit_value}</button></span>
		</p>
	</form>
EOF;
		
		echo $form;
		
		echo '</div>';
	}

	echo NR . '</div><!-- class="type type_loginform" -->' . NR;

	if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);
