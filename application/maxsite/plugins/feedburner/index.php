<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function feedburner_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'feedburner_admin_init'); # хук на админку
	mso_hook_add( 'init', 'feedburner_init'); # хук на init
}

# функция выполняется при активации (вкл) плагина
function feedburner_activate($args = array())
{	
	mso_create_allow('feedburner_edit', t('Админ-доступ к feedburner'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function feedburner_uninstall($args = array())
{	
	mso_delete_option('plugin_feedburner', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function feedburner_admin_init($args = array()) 
{
	if ( !mso_check_allow('feedburner_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_feedburner'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, 'FeedBurner');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'feedburner_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function feedburner_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('feedburner_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "FeedBurner"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "FeedBurner - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'feedburner/admin.php');
}


# функции плагина
function feedburner_init($args = array())
{

	if (!is_feed()) return $args;
	
	
	$options = mso_get_option('plugin_feedburner', 'plugins', array());
	if ( !isset($options['key']) ) return $args; 
	
	if ( !preg_match("!feedburner|feedvalidator!i", $_SERVER['HTTP_USER_AGENT']) ) // если это не feedburner, то делаем редирект на feedburner
	{
		if (mso_segment(1) == 'feed') // только для главной страницы
		{
			header("Location: http://feeds2.feedburner.com/" . trim($options['key']));
			header("HTTP/1.1 302 Temporary Redirect");
			exit;
		}
	}
	
	return $args;
}



# end file