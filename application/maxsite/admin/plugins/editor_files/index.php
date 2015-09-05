<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function editor_files_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'editor_files_admin_init');
}

# функция выполняется при указаном хуке admin_init
function editor_files_admin_init($args = array()) 
{

	if ( mso_check_allow('editor_files') ) 
	{
		$this_plugin_url = 'editor_files'; // url и hook
		mso_admin_menu_add('options', $this_plugin_url, t('Файлы'));
		mso_admin_url_hook ($this_plugin_url, 'editor_files_admin');
	}
		
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function editor_files_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('editor_files') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Файлы для редактирования') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Файлы для редактирования') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'editor_files/admin.php');
}

# end of file