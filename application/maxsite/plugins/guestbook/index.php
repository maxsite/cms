<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function guestbook_autoload($args = array())
{
	mso_hook_add('admin_init', 'guestbook_admin_init'); # хук на админку
	mso_hook_add('custom_page_404', 'guestbook_custom_page_404'); # хук для подключения к шаблону
}

# функция выполняется при активации (вкл) плагина
function guestbook_activate($args = array())
{	
	mso_create_allow('guestbook_edit', t('Админ-доступ к гостевой книге'));
	
	$CI = & get_instance();	

	if ( !$CI->db->table_exists('guestbook')) // нет таблицы guestbook
	{
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "guestbook (
		guestbook_id bigint(20) NOT NULL auto_increment,
		guestbook_ip varchar(255) NOT NULL default '',
		guestbook_browser varchar(255) NOT NULL default '',
		guestbook_date datetime default NULL,
		guestbook_approved bigint(20) NOT NULL default '0',
		guestbook_name varchar(255) NOT NULL default '',
		guestbook_text longtext,
		guestbook_title varchar(255) NOT NULL default '',
		guestbook_email varchar(255) NOT NULL default '',
		guestbook_icq varchar(255) NOT NULL default '',
		guestbook_site varchar(255) NOT NULL default '',
		guestbook_phone varchar(255) NOT NULL default '',
		guestbook_custom1 varchar(255) NOT NULL default '',
		guestbook_custom2 varchar(255) NOT NULL default '',
		guestbook_custom3 varchar(255) NOT NULL default '',
		guestbook_custom4 varchar(255) NOT NULL default '',
		guestbook_custom5 varchar(255) NOT NULL default '',
		PRIMARY KEY (guestbook_id)
		)" . $charset_collate;
		
		$CI->db->query($sql);
	}
		
	return $args;
}


# функция выполняется при деинстяляции плагина
function guestbook_uninstall($args = array())
{	
	mso_delete_option('plugin_guestbook', 'plugins' ); // удалим созданные опции
	mso_remove_allow('guestbook_edit'); // удалим созданные разрешения
	
	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('guestbook');

	return $args;
}

# функция выполняется при указаном хуке admin_init
function guestbook_admin_init($args = array()) 
{
	if ( !mso_check_allow('guestbook_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'guestbook'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Гостевая книга'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/guestbook
	mso_admin_url_hook ($this_plugin_url, 'guestbook_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function guestbook_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('guestbook_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Гостевая книга') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Гостевая книга') . ' - " . $args; ' );
	
	if ( mso_segment(3) == 'edit') require(getinfo('plugins_dir') . 'guestbook/edit.php');
	elseif ( mso_segment(3) == 'editone') require(getinfo('plugins_dir') . 'guestbook/editone.php');
	else require(getinfo('plugins_dir') . 'guestbook/admin.php');
}

# подключаем свой файл к шаблону
function guestbook_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_guestbook', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'guestbook'; 
	
	if ( mso_segment(1)==$options['slug'] ) 
	{
		require( getinfo('plugins_dir') . 'guestbook/guestbook.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}

?>