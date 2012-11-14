<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

 
function feedburner_count_activate($args){
	feedburner_count_set_default();
	return $args;
}
 
function feedburner_count_set_default(){

	$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());

	$options['feed_name'] = '';
	$options['update_interval'] = '1440';
	$options['last_update'] = '0';
	$options['count'] = 'n/a';
	$options['template'] = base64_encode('<span class="feedburner_count">%COUNT%</span>');
	
	mso_add_option('samborsky_feedburner_count',$options,'plugins');
}

# функция автоподключения плагина
function feedburner_count_autoload($args = array()){
	mso_hook_add( 'admin_init', 'feedburner_count_admin_init'); # хук на админку
}

# функция выполняется при деинсталяции плагина
function feedburner_count_uninstall($args = array()){	
	mso_delete_option('samborsky_feedburner_count', 'plugins' );
	return $args;
}

# функция выполняется при указаном хуке admin_init
function feedburner_count_admin_init($args = array()){
	if( mso_check_allow('feedburner_count') ){
		$this_plugin_url = 'plugin_feedburner_count'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'FeedBurner count');
		mso_admin_url_hook ($this_plugin_url, 'feedburner_count_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function feedburner_count_admin_page($args = array()){

	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', 'return $args . "Настройка FeedBurner Count от samborsky.com"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка FeedBurner Count от samborsky.com - " . $args; ' );
	require(getinfo('plugins_dir') . 'feedburner_count/admin.php');
}


# подключаем функции сапы
function feedburner_count_update(){

	$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());
	
	if( empty($options['last_update']) ) $options['last_update'] = 0;
	
	if( feedburner_count_need_update($options['last_update'],$options['update_interval']*60) ){
		require(getinfo('plugins_dir') . 'feedburner_count/download.php');
		
		if( $content = get_download('https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=' . $options['feed_name']) ){

			preg_match('/circulation="(\d+)"/',$content,$match);
			
			$options['count'] = (string) $match[1];
			$options['last_update'] = time();
		}
		else{
			$options['last_update'] = time() + 5*60;
			$options['count'] = 'Ошибка!';
		}
		
		mso_add_option('samborsky_feedburner_count',$options,'plugins');
	}
}

function feedburner_count(){

	feedburner_count_update();
	$options = mso_get_option('samborsky_feedburner_count', 'plugins', array());
	echo str_replace(base64_decode($options['template']),'%COUNT%',$options['count']);
}

function feedburner_count_need_update($lastupdate, $updateinterval) {
	return (time() - $lastupdate > $updateinterval);
}


# end file