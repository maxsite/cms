<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_comusers_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_comusers_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_comusers_admin_init($args = array()) 
{
	$this_plugin_url = 'comusers'; // url и hook
	
	if ( mso_check_allow('admin_comusers') ) 
		mso_admin_menu_add('users', $this_plugin_url, t('Комментаторы'), 2);

	mso_admin_url_hook ($this_plugin_url, 'admin_comusers_admin');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_comusers_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_comusers') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	# если идет вызов с номером юзера, то подключаем страницу для редактирования

	// Определим текущую страницу (на основе сегмента url)
	// http://localhost/codeigniter/admin/users/edit/1
	$seg = mso_segment(3); // третий - edit

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Комментаторы') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Комментаторы') . ' - " . $args; ' );

	// подключаем соответственно нужный файл
	if ($seg == 'edit') require($MSO->config['admin_plugins_dir'] . 'admin_comusers/edit.php');
		else require($MSO->config['admin_plugins_dir'] . 'admin_comusers/comusers.php');
	
}

?>