<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

%%% - замените на имя плагина


# функция автоподключения плагина
function %%%_autoload()
{
	mso_hook_add( 'admin_init', '%%%_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function %%%_activate($args = array())
{	
	mso_create_allow('%%%_edit', t('Админ-доступ к настройкам %%%'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function %%%_uninstall($args = array())
{	
	mso_delete_option('plugin_%%%', 'plugins' ); // удалим созданные опции
	mso_remove_allow('%%%_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function %%%_admin_init($args = array()) 
{
	if ( !mso_check_allow('%%%_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = '%%%'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин %%%'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/%%%
	mso_admin_url_hook ($this_plugin_url, '%%%_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function %%%_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл

	if ( !mso_check_allow('%%%_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('%%%') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('%%%') . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . '%%%/admin.php');
}


# функции плагина
function %%%_custom($arg = array(), $num = 1)
{

	
}


# end file