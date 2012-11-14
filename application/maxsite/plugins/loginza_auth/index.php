<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Dminty (d51x)
 * (c) http://d51x.ru/
 */


# функция автоподключения плагина
function loginza_auth_autoload()
{
	// должна быть CURL
	if (function_exists('curl_init'))
	{
		$options = mso_get_option('plugin_loginza_auth', 'plugins', array());
		$widget_fcomments_priority = (isset($options['widget_fcomments_priority'])) ? $options['widget_fcomments_priority'] : 10; 
		$widget_flogin_priority = (isset($options['widget_flogin_priority'])) ? $options['widget_flogin_priority'] : 10; 
		
		mso_hook_add('init', 'loginza_auth_init');
		mso_hook_add('page-comment-form', 'loginza_auth_page_comment_form', $widget_fcomments_priority); # хук на форму комментов
		mso_hook_add('login_form_auth', 'loginza_auth_login_form_auth', $widget_flogin_priority); # хук на форму логина
		mso_hook_add('admin_init', 'loginza_auth_admin_init'); # хук на админку
		mso_hook_add( 'head', 'loginza_auth_head');
	}
	//mso_register_widget('loginza_auth_widget', t('Форма Loginza Auth')); 	
}

# функция выполняется при активации (вкл) плагина
function loginza_auth_activate($args = array())
{	
	mso_create_allow('loginza_auth_edit', t('Админ-доступ к настройкам Loginza'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function loginza_auth_uninstall($args = array())
{	
	mso_delete_option('plugin_loginza_auth', 'plugins' ); // удалим созданные опции
	mso_remove_allow('loginza_auth_edit'); // удалим созданные разрешения
	mso_delete_option_mask('loginza_auth_widget', 'plugins' ); // 
	return $args;
}

# подключим страницу опций, как отдельную ссылку
function loginza_auth_admin_init($args = array()) 
{
    
	if ( mso_check_allow('loginza_auth_edit') ) 
	{
		$this_plugin_url = 'plugin_options/loginza_auth'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Loginza Auth'));
		mso_admin_url_hook ($this_plugin_url, 'plugin_loginza_auth');
	}
	
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function loginza_auth_mso_options() 
{
	
	if ( !mso_check_allow('loginza_auth_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	$curl = (!function_exists('curl_init')) ? '<span style="color:red">' . t('Для работы плагина требуется наличие включенной PHP-библиотеки CURL!') . '</span><br><br>' : '';
	
	mso_admin_plugin_options('plugin_loginza_auth', 'plugins', 
		array(
				'widget_type' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы комментариев в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы комментариев в виде строки, либо в виде виджета <img src="') . getinfo('plugins_url'). '/loginza_auth/sign_in_button_gray.gif">',
						'values' => t('0||виджет # 1||текстовая строка'),
						'default' => '1'
					),	
				'auth_title' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите текст ссылки авторизации для формы комментариев. Работает html'),
						'default' => 'Loginza'
					),					
				'widget_fcomments_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'widget_type_flogin' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы логина в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы логина в виде строки, либо в виде виджета <img src="') . getinfo('plugins_url'). '/loginza_auth/sign_in_button_gray.gif"><br>' . 
						t(' либо в виде большого виджета <img src="') . getinfo('plugins_url'). '/loginza_auth/sign_in_big_buttons.png">',
						'values' => t('0||виджет # 1||текстовая строка # 2||Большой виджет'),
						'default' => '1'
					),	
					
				'auth_title_flogin' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы логина:'), 
						'description' => t('Укажите текст ссылки авторизации для формы логина. Работает html'),
						'default' => 'Loginza'
					),		
					
				'widget_flogin_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы логина:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'providers_set' => array(
						'type' => 'text', 
						'name' => t('Доступные провайдеры:'), 
						'description' => t('Укажите через запятую доступных провайдеров. Оставьте поле пустым, если желаете отображать всех доступных провайдеров. Вы можете использовать следующих провайдеров:<br>') .
						// оставим на будущее
						//google, yandex, mailruapi, mailru, vkontakte, facebook, twitter, loginza, myopenid, webmoney, rambler, flickr, lastfm, verisign, aol, steam, openid', 
						'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid', 
						'default' => ''
					),
					
			),
		t('Настройки плагина Loginza Auth'), // титул
		t('Авторизация на сайте через сервис <a href="http://loginza.ru">Loginza</a>')
		. $curl
		. t('<br><b>Авторизация будет работать только в том случае, если выбранный провайдер будет возвращать e-mail адрес!!!</b>')   // инфо
	);	
	
}


function loginza_auth_head($args = array())
{
	if (!is_login() and !is_login_comuser())
		echo '<script src="http://loginza.ru/js/widget.js"></script>';

	return $args;
}

# хук на форму логина
function loginza_auth_login_form_auth($text = '') 
{
	$text .= '';
	
	$options = mso_get_option('plugin_loginza_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type_flogin'])) $options['widget_type_flogin'] = 1; 
    $widget_type =  $options['widget_type_flogin'];
	 
	if (!isset($options['auth_title_flogin']) or empty($options['auth_title_flogin'])) $options['auth_title_flogin'] = 'Loginza';  
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid';
	$providers_set = $options['providers_set'];
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-loginza-auth?' . $curpage;
	
	$auth_url = "https://loginza.ru/api/widget?token_url=" .  urlencode( $current_url );
	if ( !empty($providers_set) ) {
		$providers_set = str_replace(' ', '', $providers_set);
		$auth_url .= '&amp;providers_set=' . $providers_set;
	} else {
		// пока что так
		$auth_url .= '&amp;providers_set=' . 'google,yandex,facebook,twitter,loginza,myopenid,webmoney,openid';
	}	
	
	if ( $widget_type == 0) 
	{
		$text .= '<a rel="nofollow" href="' .  $auth_url . '" class="loginza loginza_auth">';
		$text .= '<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/></a>';
		
	} else if ($widget_type == 1) {
	    //$text .= '<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>';
		$text .= '<a rel="nofollow" href="' .  $auth_url . '" class="loginza_auth">' . $options['auth_title_flogin'] . '</a>';
	} else if ($widget_type ==2 ) {
		$auth_url .= '&overlay=loginza';
		$text .= '<iframe src="' . $auth_url . '" style="width:359px; height:300px;" scrolling="no" frameborder="no"></iframe>';

	}
	$text .= '[end]';
	return $text;
}

# сообщение в форме комментариев
function loginza_auth_page_comment_form($args = array()) 
{
	$options = mso_get_option('plugin_loginza_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type'])) $options['widget_type'] = 1; 
    $widget_type =  $options['widget_type'];
	
	if (!isset($options['auth_title']) or empty($options['auth_title'])) $options['auth_title'] = 'Loginza';  
	$auth_title = $options['auth_title'];
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid';
	$providers_set = $options['providers_set'];
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-loginza-auth?' . $curpage;
	
	$auth_url = "https://loginza.ru/api/widget?token_url=" .  urlencode( $current_url . '#comments') ;
	if ( !empty($providers_set) ) {
		$providers_set = str_replace(' ', '', $providers_set);
		$auth_url .= '&amp;providers_set=' . $providers_set;
	} else {
		// пока что так
		$auth_url .= '&amp;providers_set=' . 'google,yandex,facebook,twitter,loginza,myopenid,webmoney,openid';
	}	
	
	if ( $widget_type == 0) 
	{
		echo '<span><a rel="nofollow" href="' .  $auth_url . '" class="loginza loginza_auth">';
		echo '<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/></a></span>';
	} else {
	    echo '<script src="http://s1.loginza.ru/js/widget.js"></script>';
		echo '<span><a rel="nofollow" href="' .  $auth_url . '" class="loginza_auth">' . $auth_title . '</a></span>';
	}
	return $args;
}

# запросы через curl
function loginza_auth_request($url, $callbackurl = '')
{
	$ch = curl_init();
 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $callbackurl);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded;charset=UTF-8")); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
 
	curl_setopt($ch, CURLOPT_URL, $url);
 
	$result = curl_exec($ch);
	if (empty($result)) 
	{
		die(curl_error($ch));
		curl_close($ch);
	}
 
	curl_close($ch);
	return $result;
}


# тут всё и происходит...
function loginza_auth_init($arg = array())
{
	if (mso_segment(1) == 'maxsite-loginza-auth') 
	{
		// тут придет token
		if( !empty($_POST['token']) )
		{
			// token пришел? делаем редрект на страницу авторизации
			$auth_url = "http://loginza.ru/api/authinfo?token=" . $_POST['token'];
			$profile = loginza_auth_request($auth_url);
			$profile = json_decode($profile);
			if (!is_object($profile) || !empty($profile->error_message) || !empty($profile->error_type)) {
				$res_profile = (array) $profile['error_type'];
				die ( $res_profile['error_type'] );
			}
			$curpage = mso_url_get();
			if ( $curpage == getinfo('site_url') ) $curpage = false;
			$email = (isset($profile->email) and mso_valid_email($profile->email)) ? $profile->email : null;
			$nick = (isset($profile->name->full_name) ) ? $profile->name->full_name : null;
		
			if (isset($profile->email) and mso_valid_email($profile->email))
			{
				require_once(getinfo('common_dir') . 'comments.php');
				mso_comuser_auth(array('email'=> $email,
  				                       'comusers_nik'=> $nick,
									   'redirect' => $curpage 
									   )
								);
								
				mso_redirect( getinfo('site_url') , true, 301 );
			} else {
				// ссылка на главную или на предыдущую
				// pr( $profile );
				$txt = t('Не удалось авторизоваться с помощью выбранного сервиса.<br>Возможно это связано с тем, что в ответ на запрос 
				     сервис не возратил Ваш e-mail') . '<br>';
				$txt .= t('Вернуться на') . ' <a href="' . getinfo('site_url') . $curpage. '">' . t('предыдущую страницу') . '</a><br>'; 	 
				$txt .= t('Вернуться на') . ' <a href="' . getinfo('site_url') . '">' . t('главную страницу') . '</a><br>';
				die( $txt );
			}			
			die();
		} 
	}	

	return $arg;
}


# end file
