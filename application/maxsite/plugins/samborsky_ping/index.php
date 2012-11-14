<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) MaxSite CMS
 * http://maxsite.org/
 * 
 * (с) Евгений Самборский
 * http://www.samborsky.com/
 * 
 * Начало работы: 03.06.2009
 * 
 **/
 
	# функция автоподключения плагина
	function samborsky_ping_autoload($args = array()){
		
		// Ставим хук на публикацию. При mso_new_page и mso_edit_page
		mso_hook_add('new_page_publish','samborsky_ping_do');
		mso_hook_add('edit_page_publish','samborsky_ping_do');
		
		if( is_type('admin') ){
			// хук на админку
			mso_hook_add('admin_init','samborsky_ping_init');
		}
	}
	
	# функция выполняется при активации (вкл) плагина
	function samborsky_ping_activate($args = array()){

		// Пинг-сервисы по умолчанию
		mso_add_option('samborsky_ping_list',array(
			'http://rpc.technorati.com/rpc/ping',
			'http://blogsearch.google.com/ping/RPC2',
			'http://ping.blogs.yandex.ru/RPC2',
			'http://ping.feedburner.com',
			'http://rpc.pingomatic.com/'			
		),'plugins');		
		
		return $args;
	}
	
	# функция выполняется при указаном хуке admin_init
	function samborsky_ping_init($args = array()){
		
		mso_admin_menu_add('plugins','samborsky_ping','Пинги');
		mso_admin_url_hook('samborsky_ping','samborsky_ping_admin_page');
		
		return $args;
	}
	
	# функция вызываемая при хуке, указанном в mso_admin_url_hook
	function samborsky_ping_admin_page($args = array()){
		
		mso_hook_add_dinamic('mso_admin_header',' return $args . "' . t('samborsky_ping') . '"; ' );
		mso_hook_add_dinamic('admin_title',' return "' . t('samborsky_ping') . ' - " . $args; ' );
		
		require(getinfo('plugins_dir') . 'samborsky_ping/admin.php');
	}	
	
	# Калбек-функция для хука
	function samborsky_ping_do($result = null){
	
		if( !is_array($list = mso_get_option('samborsky_ping_list','plugins')) ){
			$list = array();
		}
		
		$CI = &get_instance();
		$CI->load->library('xmlrpc');
		
		$CI->xmlrpc->method('weblogUpdates.ping');
		$CI->xmlrpc->request(array(
			mso_get_option('name_site', 'plugins' ),
			getinfo('site_url'),
			getinfo('site_url') . 'feed'
		));		
		
		foreach( $list as $key => $value ){
			
			if( !empty($value) ){
				$CI->xmlrpc->server($value,80);
				$CI->xmlrpc->send_request();
			}
		}
		
		// Удадалим кеш
		mso_flush_cache();
		
		return $result;
	}

?>