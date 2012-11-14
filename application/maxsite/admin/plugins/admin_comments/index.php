<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

### каркас для плагина
### вместо _null укажите свой плагин


# функция автоподключения плагина
function admin_comments_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_comments_admin_init');
}

# функция выполняется при указаном хуке admin_init
function admin_comments_admin_init($args = array()) 
{

	if ( mso_check_allow('admin_comments') ) 
	{
		$this_plugin_url = 'comments'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# четвертый номер по порядку
		
		mso_admin_menu_add('users', $this_plugin_url, t('Комментарии'), 1);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'admin_comments_admin');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_comments_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_comments') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	$seg = mso_segment(3);
	
	if ($seg == 'edit')
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Редактирование комментария"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Редактирование комментария") . " - " . $args; ' );
		require($MSO->config['admin_plugins_dir'] . 'admin_comments/edit.php');
	} 
	else
	{
		mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Комментарии"); ' );
		mso_hook_add_dinamic( 'admin_title', ' return t("Комментарии") . " - " . $args; ' );
		require($MSO->config['admin_plugins_dir'] . 'admin_comments/admin.php');
	}
}

# end file