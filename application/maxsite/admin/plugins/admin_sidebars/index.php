<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_sidebars_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_sidebars_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_sidebars_admin_init($args = array()) 
{
	if ( mso_check_allow('admin_sidebars') ) 
	{
		$this_plugin_url = 'sidebars'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки
		
		mso_admin_menu_add('options', $this_plugin_url, t('Сайдбары и виджеты'), 3);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/admin_options
		mso_admin_url_hook ($this_plugin_url, 'admin_sidebars_admin');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_sidebars_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_sidebars') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Сайдбары и виджеты"); ' );
	mso_hook_add_dinamic( 'admin_title', ' return t("Сайдбары и виджеты") . " - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_sidebars/admin.php');
}


# end file