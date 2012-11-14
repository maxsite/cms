<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function fbauth_autoload()
{
	$options = mso_get_option('plugin_fbauth', 'plugins', array());
	if (isset($options['app_id']) and $options['app_id'] and isset($options['app_secret']) and $options['app_secret'])
	{
		mso_hook_add('init', 'fbauth_init');
		mso_hook_add('page-comment-form', 'fbauth_page_comment_form');
		mso_hook_add('login_form_auth', 'fbauth_login_form_auth'); # хук на форму логина
	}
	
	mso_hook_add('admin_init', 'fbauth_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function fbauth_activate($args = array())
{	
	mso_create_allow('fbauth_edit', t('Админ-доступ к настройкам Facebook Auth'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function fbauth_uninstall($args = array())
{	
	mso_delete_option('plugin_fbauth', 'plugins' ); // удалим созданные опции
	mso_remove_allow('fbauth_edit'); // удалим созданные разрешения
	return $args;
}

# подключим страницу опций, как отдельную ссылку
function fbauth_admin_init($args = array()) 
{
	if ( mso_check_allow('fbauth_edit') ) 
	{
		$this_plugin_url = 'plugin_options/fbauth'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Facebook Auth'));
		mso_admin_url_hook ($this_plugin_url, 'plugin_fbauth');
	}
	
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function fbauth_mso_options() 
{
	
	if ( !mso_check_allow('fbauth_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
		
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_fbauth', 'plugins', 
		array(
			'app_id' => array(
							'type' => 'text', 
							'name' => t('ID приложения'), 
							'description' => '', 
							'default' => ''
						),
						
			'app_secret' => array(
							'type' => 'text', 
							'name' => t('Секрет приложения'), 
							'description' => '', 
							'default' => ''
						),

	
			),
		t('Настройки плагина Facebook Auth'), // титул
		'С помощью этого плагина вы можете разрешить авторизацию/регистрацию комментаторов на своём сайте с помощью facebook.com. Перед началом работы вам необходимо получить «ID приложения» и «Секрет приложения» на facebook.com. Для этого следует с <a href="http://www.facebook.com/developers/createapp.php?version=new">cоздать новое приложение</a>. 
		
		<img src="' . getinfo('plugins_url') . '/fbauth/images/step1.png">
		
		<br>Название приложения можно задать произвольное.
		<br>После этого у вас появится страница настроек, где нужно переключиться на вкладку «Вебсайт».
		<img src="' . getinfo('plugins_url') . '/fbauth/images/step2.png">
		<br>В поле «URL сайта» укажите адрес своего сайта. В поле «Домен сайта» укажите домен своего сайта, как это приведено в примере. Сохраните изменения.
		<br>После этого скопируйте «ID приложения» и «Секрет приложения» в соответствующие поля на этой странице.
		'   // инфо
	);
}

# сообщение в форме комментариев
function fbauth_page_comment_form($args = array()) 
{
	echo ' <span><a href="' . getinfo('siteurl') . 'maxsite-fbauth">Facebook</a>.</span> ';
	
	return $args;
}

# хук на форму логина
function fbauth_login_form_auth($text = '') 
{
	$text .= '<a class="login-form-auth fbauth" title="' . t('Вход с помощью Facebook.com') . '" href="' . getinfo('siteurl') . 'maxsite-fbauth">Facebook</a>[end]';
	return $text;
}

# запросы через curl
function fbauth_request($url, $callbackurl = '')
{
	$ch = curl_init();
 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $callbackurl);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
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
function fbauth_init($arg = array())
{
	if (mso_segment(1) == 'maxsite-fbauth') 
	{
		if (!function_exists('curl_init')) die('Не найдено PHP-расширение CURL');
		if (!function_exists('json_decode')) die('Не найдено PHP-расширение JSON');
		
		$options = mso_get_option('plugin_fbauth', 'plugins', array());
		if (!isset($options['app_id']) or !$options['app_id']) die(t('Не задан app_id')); 
		if (!isset($options['app_secret']) or !$options['app_secret']) die(t('Не задан app_secret'));
			
		$app_id = $options['app_id'];
		$app_secret = $options['app_secret'];
		
		$my_url = getinfo('site_url') . 'maxsite-fbauth';

		$code = isset($_REQUEST["code"]) ? $_REQUEST["code"] : null;

		if(!isset($code)) 
		{
			$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
					. $app_id 
					. "&redirect_uri=" 
					. urlencode($my_url) 
					. '&scope=email,user_website';
			mso_redirect($dialog_url, true);
		}
		
		$token_url = "https://graph.facebook.com/oauth/access_token?client_id="
			. $app_id 
			. "&redirect_uri=" . urlencode($my_url) 
			. "&client_secret="
			. $app_secret 
			. "&code=" . $code;

		$access_token = fbauth_request($token_url);
		
		$graph_url = "https://graph.facebook.com/me?" . $access_token;
		
		$user0 = fbauth_request($graph_url);

		if (strpos($user0, '400 Bad Request') !== false)
		{
			die(t('Ошибка авторизации (400 Bad Request)'));
		}
		else
		{
			$user = json_decode($user0);
			
			if (isset($user->email) and mso_valid_email($user->email)) 
			{
				require_once(getinfo('common_dir') . 'comments.php');
				mso_comuser_auth(array('email'=>$user->email, 'comusers_nik'=>$user->name));
				
				// echo("Hello " . $user->name);
			}
			else
			{
				// ошибочный или отстутсвующий email
				die(t('Не удалось авторизоваться с помощью Facebook. Возможно это связано с тем, что в ответ на запрос сервис не возвратил ваш e-mail'));
			}
			
			die;
		}
		
	}
	
	return $arg;
}

# end file