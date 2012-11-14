<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



# функция автоподключения плагина
function admin_plugins_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_plugins_admin_init');
}

# функция выполняется при указаном хуке admin_init
function admin_plugins_admin_init($args = array()) 
{

	if ( mso_check_allow('admin_plugins') ) 
	{
		$this_plugin_url = 'plugins'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# Четвертый - номер в меню
		
		
		mso_admin_menu_add('options', $this_plugin_url, t('Плагины'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/admin_plugins
		mso_admin_url_hook ($this_plugin_url, 'admin_plugins_admin');
	}
		
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_plugins_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_plugins') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройка плагинов') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройка плагинов') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_plugins/admin.php');
}

?>