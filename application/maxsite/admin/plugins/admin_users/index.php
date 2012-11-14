<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_users_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_users_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_users_admin_init($args = array()) 
{

	$this_plugin_url = 'users'; // url и hook
	
	if ( mso_check_allow('admin_users_users') ) 
		mso_admin_menu_add('users', $this_plugin_url, t('Авторы'), 3);

	mso_admin_url_hook ($this_plugin_url, 'admin_users_admin');
	
	if ( mso_check_allow('admin_users_group') ) 
	{
		$this_plugin_url = 'users_group'; // url и hook
		mso_admin_menu_add('users', $this_plugin_url, t('Разрешения'), 4);
		mso_admin_url_hook ($this_plugin_url, 'admin_users_group');	
	}

	$this_plugin_url = 'users_my_profile'; // url и hook
	mso_admin_menu_add('users', $this_plugin_url, t('Мой профиль'), 5);
	mso_admin_url_hook ($this_plugin_url, 'admin_users_my_profile');	
	
	
//	$this_plugin_url = 'users_edit'; // url и hook
//	mso_admin_menu_add('users', $this_plugin_url, 'Редактировать пользователя', 3);
//	mso_admin_url_hook ($this_plugin_url, 'admin_users_edit');	
	
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_users_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_users_users') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	# если идет вызов с номером юзера, то подключаем страницу для редактирования

	// Определим текущую страницу (на основе сегмента url)
	// http://localhost/codeigniter/admin/users/edit/1
	$seg = mso_segment(3); // третий - edit

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Пользователи') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Пользователи') . ' - " . $args; ' );

	// подключаем соответственно нужный файл
	if ($seg == '') require($MSO->config['admin_plugins_dir'] . 'admin_users/users.php');
		elseif ($seg == 'edit') require($MSO->config['admin_plugins_dir'] . 'admin_users/edit.php');
	
}


function admin_users_group($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_users_group') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройка групп пользователей') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройка групп пользователей') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_users/group.php');
}



function admin_users_my_profile($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройка своего профиля') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройка своего профиля') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_users/my_profile.php');
}


?>