<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function wpconvert_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'wpconvert_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function wpconvert_activate($args = array())
{	
	mso_create_allow('wpconvert_edit', t('Админ-доступ к wpconvert'));
	return $args;
}

# функция выполняется при указаном хуке admin_init
function wpconvert_admin_init($args = array()) 
{
	if ( !mso_check_allow('wpconvert_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_wpconvert'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, 'WordPress convert');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'wpconvert_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function wpconvert_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('wpconvert_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "WordPress convert "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "WordPress convert - " . $args; ' );

	require(getinfo('plugins_dir') . 'wpconvert/admin.php');
}


# end file