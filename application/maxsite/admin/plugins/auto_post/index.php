<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function auto_post_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'auto_post_admin_init');
}

# функция выполняется при указаном хуке admin_init
function auto_post_admin_init($args = array()) 
{

	if ( mso_check_allow('auto_post') ) 
	{
		$this_plugin_url = 'auto_post'; // url и hook
		//mso_admin_menu_add('page', $this_plugin_url, t('AutoPost'));
		mso_admin_url_hook ($this_plugin_url, 'auto_post_admin');
		
	}
		
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function auto_post_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('auto_post') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('AutoPost') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('AutoPost') . ' - " . $args; ' );
	mso_hook_add( 'admin_head_css', 'auto_post_admin_head_css');
	
	require($MSO->config['admin_plugins_dir'] . 'auto_post/admin.php');
}

function auto_post_admin_head_css($args = array()) 
{
	echo '<link rel="stylesheet" href="'. getinfo('admin_url') . 'plugins/auto_post/style.css">';
		
	return $args;
}

# end of file