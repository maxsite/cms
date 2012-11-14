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
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# четвертый номер по порядку
		
		mso_admin_menu_add('page', $this_plugin_url, t('Список'), 2);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'admin_page_admin');
	}
	
	if ( mso_check_allow('admin_page_new') ) 
	{
		$this_plugin_url = 'page_edit'; // url и hook
		// mso_admin_menu_add('page', $this_plugin_url, 'Редактировать запись', 2);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_edit');
		
		
		$this_plugin_url = 'page_new'; // url и hook
		mso_admin_menu_add('page', $this_plugin_url, t('Создать'), 1);
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
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Список страниц') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Список страниц') . ' - " . $args; ' );
	
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
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редактирование страницы') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редактирование страницы') . ' - " . $args; ' );
	
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
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Создать страницу') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Создать страницу') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/new.php');
}

# скрываем блоки
# опции задаются в application/maxsit/admin/plugins/admin_options/editor.php
function admin_page_hide_blocks($arg = array())
{
	$options = mso_get_option('editor_options', 'admin', array());
	
	$css = '';
	if ( isset($options['page_status']) and !$options['page_status']) $css .= 'p.page_status {display: none !important;}' .NR ;
	if ( isset($options['page_files']) and !$options['page_files']) $css .= 'a.page_files {display: none !important;}' .NR ;
	
	if ( isset($options['page_meta']) and !$options['page_meta']) $css .= 'div.page_meta {display: none !important;}' .NR ;
	if ( isset($options['page_all_cat']) and !$options['page_all_cat']) $css .= 'div.page_all_cat {display: none !important;}' .NR ;
	if ( isset($options['page_tags']) and !$options['page_tags']) $css .= 'div.page_tags {display: none !important;}' .NR ;
	if ( isset($options['page_slug']) and !$options['page_slug']) $css .= 'div.page_slug {display: none !important;}' .NR ;
	if ( isset($options['page_discus']) and !$options['page_discus']) $css .= 'div.page_discus {display: none !important;}' .NR ;
	if ( isset($options['page_date']) and !$options['page_date']) $css .= 'div.page_date {display: none !important;}' .NR ;
	if ( isset($options['page_post_type']) and !$options['page_post_type']) $css .= 'div.page_post_type {display: none !important;}' .NR ;
	if ( isset($options['page_password']) and !$options['page_password']) $css .= 'div.page_password {display: none !important;}' .NR ;
	if ( isset($options['page_menu_order']) and !$options['page_menu_order']) $css .= 'div.page_menu_order {display: none !important;}' .NR ;
	if ( isset($options['page_all_parent']) and !$options['page_all_parent']) $css .= 'div.page_all_parent {display: none !important;}' .NR ;
	if ( isset($options['page_all_users']) and !$options['page_all_users']) $css .= 'div.page_all_users {display: none !important;}' .NR ;
	if ( isset($options['cat_height']) and $options['cat_height']) $css .= 'div.page_all_cat div.cat_page {max-height: ' . ((int) $options['cat_height']) . 'px!important; overflow: auto;}' .NR ;
	
	
	if ($css)
	{
		echo NR . '<style>' . NR . $css . '</style>' . NR;
	}
	
	// если второй сегмент page_edit, то в меню выделим пункт список
	if (mso_segment(2)== 'page_edit')
	{
		echo '
		<script>
			$(function(){
				$("li.admin-menu-page").addClass("admin-menu-selected");
			});
		</script>
		';
		
		//		$("li.admin-menu-page a").text("' . t('Список/правка') . '");
	}
}

# end file