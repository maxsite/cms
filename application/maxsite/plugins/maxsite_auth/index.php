<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function maxsite_auth_autoload()
{
	mso_hook_add('init', 'maxsite_auth_custom');
	mso_hook_add('page-comment-form', 'maxsite_auth_page_comment_form');
	mso_hook_add('admin_init', 'maxsite_auth_admin_init'); # хук на админку
	mso_hook_add('login_form_auth', 'maxsite_auth_login_form_auth'); # хук на форму логина
}

# функция выполняется при активации (вкл) плагина
function maxsite_auth_activate($args = array())
{	
	mso_create_allow('maxsite_auth_edit', t('Админ-доступ к настройкам Maxsite Auth') . ' ' . t('maxsite_auth'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function maxsite_auth_uninstall($args = array())
{	
	mso_delete_option('plugin_maxsite_auth', 'plugins' ); // удалим созданные опции
	mso_remove_allow('maxsite_auth_edit'); // удалим созданные разрешения
	return $args;
}

# подключим страницу опций, как отдельную ссылку
function maxsite_auth_admin_init($args = array()) 
{
	if ( mso_check_allow('maxsite_auth_edit') ) 
	{
		$this_plugin_url = 'plugin_options/maxsite_auth'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Maxsite Auth'));
		mso_admin_url_hook ($this_plugin_url, 'plugin_maxsite_auth');
	}
	
	return $args;
}

# сообщение в форме комментариев
function maxsite_auth_page_comment_form($args = array()) 
{
	echo '<span><a href="' . getinfo('siteurl') . 'maxsite-auth-form">MaxSiteAuth</a>.</span> ';
	
	return $args;
}

# хук на форму логина
function maxsite_auth_login_form_auth($text = '') 
{
	$text .= '<a class="login-form-auth maxsite_auth" title="' . t('Если у вас сайт на MaxSite CMS, то вы можете войти с его помощью') . '" href="' . getinfo('siteurl') . 'maxsite-auth-form">MaxSiteAuth</a>[end]';
	return $text;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function maxsite_auth_mso_options() 
{
	
	if ( !mso_check_allow('maxsite_auth_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_maxsite_auth', 'plugins', 
		array(
			'email' => array(
							'type' => 'text', 
							'name' => t('Email'), 
							'description' => t('Укажите рабочий email, который будет использоваться для регистрации и входа на других сайтах.'), 
							'default' => ''
						),
			'password' => array(
							'type' => 'text', 
							'name' => t('Пароль'), 
							'description' => t('Укажите пароль, который будет использоваться для регистрации и входа на других сайтах. Не указывайте здесь пароль от своего сайта!'), 
							'default' => ''
						),
			'unique' => array(
							'type' => 'checkbox', 
							'name' => t('Делать уникальные пароли для каждого сайта'), 
							'description' => t('В этом случае для каждого сайта будет создан уникальный пароль на основе его адреса и вашего пароля. Для «прямого» входа на чужом сайте вы можете сгенерировать полученный пароль через приведенную ниже форму. Вы можете использовать его также для восстановления на чужом сайте.'), 
							'default' => '0'
						),			
						
			),
		t('Настройка Maxsite Auth'), // титул
		t('С помощью Maxsite Auth вы можете осуществлять авторизацию на других сайтах с помощью своего. Достаточно лишь предварительно задать email и пароль, которые будут передаваться на исходный сайт, где вы автоматически будете зарегистрированы или авторизованы как комюзер (комментатор).')   // инфо
	);
	
	
	echo '<br>';
	
	if ( $post = mso_check_post(array('f_url_submit', 'f_url')) )
	{
		$url = mb_strtolower($post['f_url']);
		
		$options = mso_get_option('plugin_maxsite_auth', 'plugins', array());
		
		if (!$url)
		{
			echo '<div class="error">' . t('Нужно указать адрес сайта') . '</div>';
		}
		elseif (!isset($options['password']) or !$options['password'])
		{
			echo '<div class="error">' . t('Следует указать свой пароль') . '</div>';
		}
		elseif (!isset($options['unique']) or !$options['unique'])
		{
			echo '<div class="error">' . t('Вы не отметили создание уникального пароля для каждого сайта') . '</div>';
		}
		else
		{
			$pas = $url;
			$pas = convert_uuencode(mso_md5($options['password'] . $pas));
			$pas = mb_strtolower($pas);
			$pas = mso_slug($pas);
			$pas = substr($pas, 1, 20);
			
			echo '<div class="update">' . t('Пароль для ') . $url . ' — <input type="text" value="' . $pas . '"></div>';
		}
	}
	
	echo '<form method="post" class="fform">' . mso_form_session('f_session_id') . '
		<p class="hr head"><label class="fheader" for="f_url">' . t('Укажите адрес сайта (с http://), для которого необходимо узнать свой пароль') . '</label></p>
		<p><span><input type="text" name="f_url" id="f_url"></span></p>
		<p><span><button type="submit" name="f_url_submit" class="i execute">' . t('Узнать пароль для сайта') . '</button></span></p>
		</form>';
}



# функции плагина
function maxsite_auth_custom($args = array())
{
	if (mso_segment(1) == 'maxsite-auth-form')
	{	
		// здесь формируется форма для отправки запроса
		// данные отправляются POST
		// посетитель должен указать только адрес своего сайта
		// в hidden указываем нужные данные
		
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? mso_clean_str($_SERVER['HTTP_REFERER'], 'xss') : getinfo('siteurl');
		
		echo '<!DOCTYPE html><html><head>
<meta charset="UTF-8">
<title>Авторизация</title>
</head><body>
	<form method="post" action="' . getinfo('site_url'). 'maxsite-auth-form-post">
		<input type="hidden" name="redirect_url" value="' . urlencode($redirect_url) . '">
		Укажите адрес своего сайта (с http://): <input type="text" name="url" value="" size="80">
		<button type="submit">' . tf('Перейти к сайту') . '</button>
	</form>
</body></html>';
		
		die(); // Форма ОК
	}
	elseif (mso_segment(1) == 'maxsite-auth-form-post') 
	{
		// здесь происходит приём указанного адреса сайта и редирект на него с нужными данными
		
		if ( $post = mso_check_post(array('redirect_url', 'url')) )
		{
			$url = mb_strtolower($post['url']);
			$url = trim(str_replace('/', ' ', $url));
			$url = trim(str_replace('  ', ' ', $url));
			$url = trim(str_replace(' ', '/', $url));
			$url = str_replace('http:/', 'http://', $url);
			
			$url = $url . '/maxsite-auth-receive/' 
					. base64_encode((getinfo('siteurl') . '##'. urldecode($post['redirect_url']) . '##' . substr(mso_md5(getinfo('siteurl')), 1, 5)));
			
			mso_redirect($url, true);
		}
		else 
			mso_redirect('maxsite-auth-form'); // ошибочная форма - возвращаемся
	}
	elseif (mso_segment(1) == 'maxsite-auth-receive') 
	{
		// принимаем входящие данные от другого сайта
		// здесь запрос на авторизацию
		// нужно проверить все входящие данные
		// проверить is_login
		// и сформировать форму с отправкой на входящий_сайт/maxsite-auth-reply
		
		if (!is_login()) // нет логина - нужно вывести форму логина
		{
			echo '<!DOCTYPE html><html><head>
<meta charset="UTF-8">
<title>Авторизация</title>
</head><body>
	<div class="loginform">' . tf('Для авторизации необходимо войти на сайт') . '<br>';
			
			mso_login_form(array( 'login'=>tf('Логин:') . ' ', 'password'=>tf('Пароль:') . ' ', 'submit'=>''), getinfo('siteurl') . mso_current_url());
			
			echo '</div></body></html>';
			
			die(); // выходим ОК
		}
		else // вход есть
		{
			//проверяем разрешения группы
			if ( !mso_check_allow('maxsite_auth_edit') ) die(tf('Доступ к авторизации запрещен'));
			
			$options = mso_get_option('plugin_maxsite_auth', 'plugins', array());
			if (!isset($options['email']) or !$options['email']) die(tf('Не задан ответный email')); 
			if (!isset($options['password']) or !$options['password']) die(tf('Не задан ответный пароль'));
			if (!isset($options['unique'])) $options['unique'] = 0; // делать уникальный пароль
			
			// смотрятся входные get-данные (расшифровка из base64) адрес-сайт1
			$data64 = mso_segment(2);
			if (!$data64) die(tf('Нет данных'));
			
			// отладка
			//	echo (getinfo('siteurl') . '##'. 'page/about' . '##' . substr(mso_md5(getinfo('siteurl')), 1, 5));
			//	echo '<br>'. base64_encode((getinfo('siteurl') . '##'. 'page/about' . '##' . substr(mso_md5(getinfo('siteurl')), 1, 5)));
			//	echo '<br>';
			
			// распаковываем данные
			$data = @base64_decode($data64);
			if (!$data) die(tf('Ошибочные данные'));
			
			
			//	адрес-сайт1##адрес текущей страницы1##открытый ключ
			$data = explode('##', $data);
			
			// обработаем предварительно массив
			$data_1 = array();
			foreach($data as $element)
			{
				if ($d = trim($element)) $data_1[] = $d;
			}
			
			// должно быть 3 элемента
			if (count($data_1) != 3) die(tf('Неверное количество данных'));
			
			// pr($data_1);
			
			$data_siteurl = $data_1[0];
			$data_redirect = $data_1[1];
			$data_key = $data_1[2];
			
			// все проверки пройдены
			// выводим форму с кнопкой Разрешить
			
			// данные для ответа
			//	- адрес исходный
			//	- адрес ответ - текущий
			//	- адрес текущей страницы1 - редирект
			//	- открытый ключ сайта2
			//	- зашифрованный «email##пароль» на основе открытых ключей сайт1 и сайт2
			
			$CI = & get_instance();
			$CI->load->library('encrypt'); // подключим библиотеку для шифрования
			
			// ключ строится по этому алгоритму
			// он должен быть фиксированным для одного сайта
			$my_key = substr(mso_md5(getinfo('siteurl')), 1, 5);
			
			$pas = $data_siteurl;
			
			// делать пароль уникальным
			if ($options['unique'])
			{
				//pr($options['password']); // указанный пароль
				// pr($data_siteurl); // для какого сайта
				
				$pas = mb_strtolower($pas);
				$pas = convert_uuencode(mso_md5($options['password'] . $pas));
				$pas = mb_strtolower($pas);
				$pas = mso_slug($pas);
				$pas = substr($pas, 1, 20);

				// _pr($pas);
			}
			
			// шифруем на основе двух ключей
			$my_email_pass = $CI->encrypt->encode($options['email'] . '##' . $pas, $data_key . $my_key);

			$data = getinfo('siteurl') . '##'
					. $data_siteurl . '##'
					. $data_redirect . '##'
					. $my_key . '##'
					. $my_email_pass;
			
			// pr($data);
			// pr($CI->encrypt->decode($my_email_pass, $data_key . $my_key));
			
			echo '<!DOCTYPE html><html><head>
<meta charset="UTF-8">
<title>Авторизация</title>
</head><body>
<form method="post" action="' . $data_siteurl . 'maxsite-auth-reply">
	<input type="hidden" name="data" value="' . base64_encode($data) . '">
	<button type="submit">' . tf('Подтвердить авторизацию для') . ' ' . $data_siteurl . '</button>
</form>
</body></html>';

			die(); // выход ОК
		}
	}
	elseif (mso_segment(1) == 'maxsite-auth-reply')
	{
		// принимаем данные для авторизации от другого сайта
		// должен быть email и пароль
		// проверяем входящие данные
		// проверяем существаание такого комюзера
		// если его нет, то выполняем авторизацию
		// после выполняем автологин
		// и редиректимся на указанную страницу
		// pr($_POST);
		
		if ( $post = mso_check_post(array('data')) )
		{
			// проверим referer
			if (!isset($_SERVER['HTTP_REFERER'])) die(t('Ошибочный referer-сайт'));
			
			$data = @base64_decode($post['data']);
			if (!$data) die(t('Ошибочные данные')); // ошибка распаковки
			
			// pr($data);
			
			$data = explode('##', $data);
			
			// обработаем предварительно массив
			$data_1 = array();
			foreach($data as $element)
			{
				if ($d = trim($element)) $data_1[] = $d;
			}
			
			// должно быть 5 элементов
			if (count($data_1) != 5) die(t('Неверное количество данных'));
			
			/*
			$data = getinfo('siteurl') . '##'
					. $data_siteurl . '##'
					. $data_redirect . '##'
					. $my_key . '##'
					. $my_email_pass;
			
			
			*/
			
			//pr($data_1);
			
			$data_siteurl = $data_1[0]; // сайт где была сделана авторизация = реферер
			$my_siteurl = $data_1[1]; // сайт с которого был отправлен запрос на авторизацию - должен быть равен текущему
			$data_redirect = $data_1[2]; // конечная страница
			$data_key = $data_1[3]; // открытый ключ сайта-авторизатора
			$data_email_pass = $data_1[4]; // email и пароль
			
			
			if (strpos($_SERVER['HTTP_REFERER'], $data_siteurl) === false) die(t('Ошибочный referer-сайт'));
			
			if ($my_siteurl != getinfo('siteurl')) die(t('Ошибочный исходный сайт'));
			
			$CI = & get_instance();
			$CI->load->library('encrypt'); // подключим библиотеку для шифрования
			
			// ключ строится по этому алгоритму
			// он должен быть фиксированным для одного сайта
			$my_key = substr(mso_md5(getinfo('siteurl')), 1, 5);
			
			// шифруем на основе двух ключей
			$my_email_pass = $CI->encrypt->decode($data_email_pass, $my_key . $data_key);
			
			//_pr($my_email_pass);
			
			$email_pass = explode('##', $my_email_pass);
			
			
			if (count($email_pass) != 2) die(tf('Неверные данные email-пароль'));
			
			$email = $email_pass[0]; // email
			$pass = $email_pass[1]; // пароль
			
			if (!mso_valid_email($email)) die(t('Неверный email'));
			if (strlen($pass) < 6) die(tf('Короткий пароль')); 
			
			// pr($email . ' ' . $pass);
			
			
			require_once(getinfo('common_dir') . 'comments.php');
			
			mso_comuser_auth(array('email'=>$email, 'password'=>$pass));
			
			die(); // выход ОК
		}
		else die('Ошибочные данные'); // нет POST
		
	}
	else return $args;
}

# end file