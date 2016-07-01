<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_page_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_page_admin_init');
	
	if ( mso_segment(1) == 'admin' and (mso_segment(2) == 'page_new' or mso_segment(2) == 'page_edit') ) 
	{
		mso_hook_add('admin_head', 'admin_page_hide_blocks');	
	}
}

# функция выполняется при указаном хуке admin_init
function admin_page_admin_init($args = array()) 
{

	if ( mso_check_allow('admin_page') ) 
	{
		$this_plugin_url = 'page'; // url и hook
		
		mso_admin_menu_add('page', $this_plugin_url, t('Все записи'), 2);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_admin');
	}
	
	if ( mso_check_allow('admin_page_new') ) 
	{
		$this_plugin_url = 'page_edit'; // url и hook
		// mso_admin_menu_add('page', $this_plugin_url, 'Редактировать запись', 2);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_edit');
		
		
		$this_plugin_url = 'page_new'; // url и hook
		mso_admin_menu_add('page', $this_plugin_url, t('Создать запись'), 1);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_new');	
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_page') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Список всех записей') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Список всех записей') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/admin.php');
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_edit($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_page_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редактирование записи') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редактирование записи') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/edit.php');
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_new($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_page_new') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Создать новую запись') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Создать новую запись') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/new.php');
}

# скрываем блоки
# опции задаются в application/maxsit/admin/plugins/admin_options/editor.php
function admin_page_hide_blocks($arg = array())
{
	$options = mso_get_option('editor_options', 'admin', array());
	
	$css = '';
	
	if ( isset($options['page_status']) and !$options['page_status']) 
		$css .= 'p.page_status {display: none !important;}' .NR ;
		
	if ( isset($options['page_files']) and !$options['page_files']) 
		$css .= 'a.page_files {display: none !important;}' .NR ;
	
	if ( isset($options['page_all_parent']) and !$options['page_all_parent']) 
		$css .= 'p.page_all_parent {display: none !important;}' .NR ;
	
	if ( isset($options['cat_height']) and $options['cat_height']) 
		$css .= 'div.tabs-box.all-cat div.page_cat {max-height: ' . ((int) $options['cat_height']) . 'px!important; overflow: auto;}' .NR ;
	
	if ($css)
		echo NR . '<style>' . $css . '</style>' . NR;
	
	// если второй сегмент page_edit, то в меню выделим пункт список
	if (mso_segment(2)== 'page_edit')
	{
		echo '<script> $(function(){ $("li.admin-menu-page").addClass("admin-menu-selected"); }); </script>';
	}
}


# end of file