<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function mail_send_autoload()
{
	mso_hook_add( 'admin_init', 'mail_send_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function mail_send_activate($args = array())
{	
	mso_create_allow('mail_send_edit', t('Админ-доступ к плагину Mail Send'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function mail_send_uninstall($args = array())
{	
	mso_delete_option('mail_send', 'plugins' ); // удалим созданные опции
	mso_remove_allow('mail_send_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function mail_send_admin_init($args = array()) 
{
	if ( !mso_check_allow('mail_send_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'mail_send'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Mail Send'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/mail_send
	mso_admin_url_hook ($this_plugin_url, 'mail_send_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function mail_send_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл

	if ( !mso_check_allow('mail_send_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Mail Send') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Mail Send') . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'mail_send/admin.php');
}


# end file