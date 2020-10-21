<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function global_cache_autoload($args = array())
{
	// исключаем из кэширования адреса по первому сегменту
	$no_cache = array('admin', 'page_404', 'contact', 'logout', 'login', 'registration', 'password-recovery', 'loginform', 'require-maxsite', 'ajax', 'remote', 'dc', 'feed', 'search');
	
	if (in_array(mso_segment(1), $no_cache)) return $args;
	
	$options = mso_get_option('plugin_global_cache', 'plugins', array());
	
	// кэш включен?
	if (isset($options['on']) and $options['on'])
	{
		# сброс кэша если была отправка POST
		if ( isset($_POST) and $_POST ) global_cache_all_flush();
		
		# дополнительные хуки, которые позволяют сбросить кэш - использовать в плагинах и т.п.
		mso_hook_add('global_cache_key_flush', 'global_cache_key_flush'); // сброс кэша текущей страницы
		mso_hook_add('global_cache_all_flush', 'global_cache_all_flush'); // сброс всего html-кэша
		
		// кэшировать только главную
		if (isset($options['home-only']) and $options['home-only'])
		{
			if (!is_type('home')) return $args;
		}
		
		// если залогиненность и есть опция выключения, то отключаем кэш
		if ( (isset($options['onlogin']) and $options['onlogin']) and (is_login() or is_login_comuser()))
		{
			return $args;
		}
		else
		{
			// ставим хуки на кэширование
			mso_hook_add('global_cache_start', 'global_cache_start');
			mso_hook_add('global_cache_end', 'global_cache_end');
		}
	}
}

# функция выполняется при деактивации (выкл) плагина
function global_cache_deactivate($args = array())
{	
	// очистить html-кэш
	global_cache_all_flush();
	return $args;
}

# функция выполняется при деинстяляции плагина
function global_cache_uninstall($args = array())
{	
	// очистить html-кэш
	global_cache_all_flush();
	return $args;
}

# текущий ключ
# если $dir = true, то добавляем каталог html
function global_cache_key($dir = true)
{
	$cache_key =  mso_clean_str($_SERVER['REQUEST_URI']); //$_SERVER['REQUEST_URI'];
	$cache_key = str_replace('/', '-', $cache_key);
	$cache_key = mso_slug(' ' . $cache_key);
	
	if (!$cache_key) $cache_key = 'home'; // главная
	
	if ($dir) $cache_key = 'html/' . $cache_key . '.html';
		else $cache_key = $cache_key . '.html';
	
	return $cache_key;
}

# старт кэширования
function global_cache_start($arg = array())
{
	if ( $k = mso_get_cache(global_cache_key(), true) ) 
	{
		// да есть в кэше
		$CI = & get_instance();	
		$mq = $CI->db->query_count; // колво sql-запросов
		$k = str_replace('<!--global_cache_footer-->', ' | Cache ON (' . $mq . 'sql)', $k);
		echo $k; 
		return true;
	}
	
	ob_start();
	return false;
}

# стоп кэширования - отдача результата
function global_cache_end($arg = array())
{
	# сброс кэша для страниц, которые отправлены как POST
	if ( isset($_POST) and $_POST ) global_cache_key_flush();
	else 
	{
		$options = mso_get_option('plugin_global_cache', 'plugins', array());
		
		if (!isset($options['time'])) $options['time'] = 15;
		
		mso_add_cache(global_cache_key(), ob_get_flush(), $options['time'] * 60, true);
	}
}

# сброс кэша текущей страницы
function global_cache_key_flush($arg = array())
{
	mso_flush_cache(false, 'html', global_cache_key(false));
	return $arg;
}

# сброс всего кэша html
function global_cache_all_flush($arg = array())
{
	mso_flush_cache(false, 'html');
	return $arg;
}

function global_cache_mso_options() 
{
	if ( !mso_check_allow('global_cache_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}

	// сброс всего кэша
	if ( $post = mso_check_post(array('f_submit_clear_global_cache', 'f_session_id')) )
	{
		global_cache_all_flush();
		echo '<div class="update">'. t('Кэш очищен') . '</div>';
	}

	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_global_cache', 'plugins', 
		array(
		
			'on' => array(
							'type' => 'checkbox', 
							'name' => t('Включить глобальное кэширование'), 
							'description' => '', 
							'default' => 1
						),
						
			'home-only' => array(
							'type' => 'checkbox', 
							'name' => t('Кэшировать только главную страницу'), 
							'description' => '', 
							'default' => 0
						),
			
			'onlogin' => array(
							'type' => 'checkbox', 
							'name' => t('Выключить глобальное кэширование для авторов/админов'), 
							'description' => '', 
							'default' => 1
						),
			
			'time' => array(
							'type' => 'text', 
							'name' => t('Время жизни кэша (минут)'), 
							'description' => t('Укажите время, через которое кэш устареет и будет создан заново'), 
							'default' => '15'
						),
			),
		t('Настройки глобального кэширования'), // титул
		t('Кэширует страницы целиком. В кэш будет добавляться полностью сгенерированные страницы, что ускоряет работу сайта. Рекомендуется для сайтов с большой посещаемостью. Данный кэш может занимать много места на диске.')   // инфо
	);
	
	echo '<form method="post">' . mso_form_session('f_session_id');
	echo '<p><button class="button i-stack-overflow" type="submit" name="f_submit_clear_global_cache">'. t('Сбросить кэш') . '</button></p>';
	echo '</form>';
	
}

# end of file