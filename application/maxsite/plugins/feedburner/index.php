<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function feedburner_autoload($args = array())
{
	mso_hook_add( 'init', 'feedburner_init'); # хук на init
}

# функция выполняется при активации (вкл) плагина
function feedburner_activate($args = array())
{	
	mso_create_allow('feedburner_edit', t('Админ-доступ к опциям feedburner'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function feedburner_uninstall($args = array())
{	
	mso_delete_option('plugin_feedburner', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function feedburner_mso_options() 
{
	if ( !mso_check_allow('feedburner_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_feedburner', 'plugins', 
		array(
			'key' => array(
						'type' => 'text', 
						'name' => t('Адрес вашего фида в FeedBurner.com:'), 
						'description' => 'http://feeds.feedburner.com/<b>[адрес вашего фида]</b>',
						'default' => ''
					),
			),
		t('Плагин FeedBurner'), // титул
		t('Плагин выполняет перенаправление вашего основного rss на сервис feedburner.com.')   // инфо
	);

}

# функции плагина
function feedburner_init($args = array())
{
	if (!is_feed()) return $args;
	
	$options = mso_get_option('plugin_feedburner', 'plugins', array());
	if ( !isset($options['key']) && !$options['key'] ) return $args; 
	
	if ( !preg_match("!feedburner|feedvalidator!i", $_SERVER['HTTP_USER_AGENT']) ) // если это не feedburner, то делаем редирект на feedburner
	{
		if (mso_segment(1) == 'feed') // только для главной страницы
		{
			header("Location: http://feeds.feedburner.com/" . trim($options['key']));
			header("HTTP/1.1 302 Temporary Redirect");
			exit;
		}
	}
	
	return $args;
}

# end file