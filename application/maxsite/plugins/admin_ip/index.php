<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_ip_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'admin_ip_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function admin_ip_activate($args = array())
{	
	mso_create_allow('admin_ip_edit', t('Админ-доступ к редактированию разрешенных IP'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function admin_ip_uninstall($args = array())
{	
	mso_delete_option('plugin_admin_ip', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function admin_ip_admin_init($args = array()) 
{
	// проверяем сегменты URL
	// получаем из опций секретный сегмент
	// если это секретный, то сбрасываем ip
	// получаем список разрешенных IP из опций
	// получаем текущий IP юзера
	// если его нет в разрешенных, то die('no');
	
	global $MSO;
	
	$options_key = 'plugin_admin_ip';
	
	$options = mso_get_option($options_key, 'plugins', array());
	
	if ( !isset($options['secret']) ) $options['secret'] = '';
	if ( !isset($options['ip']) ) $options['ip'] = '';
	
	if ( $options['secret'] and (mso_segment(3) == $options['secret']) )
	{	
		// сброс ip
		// http://localhost/codeigniter/admin/plugin_admin_ip/secret_to_reset - secret_to_reset
		
		$options['ip'] = '';
		mso_add_option($options_key, $options, 'plugins' );
		mso_redirect('admin/plugin_admin_ip'); // редирект на страницу плагина
	}
	
	if ($options['ip'])
	{
		// указаны IP
		
		$ips = explode("\n", $options['ip']);
		$curr_ip = $MSO->data['session']['ip_address'];
		
		$ok = false; // признак, что доступ разрешен
		foreach ($ips as $ip)
		{
			if ( trim($ip) == $curr_ip)
			{
				$ok = true;
				break;
			}
		}
		
		if (!$ok) die('Access denied'); // рубим 
	}
	
	if ( !mso_check_allow('admin_ip_admin_page') ) // разрешение для юзеров
	{
		return $args; // 'Доступ запрещен';
	}
	
	$this_plugin_url = 'plugin_admin_ip'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, 'Admin IP');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'admin_ip_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_ip_admin_page($args = array()) 
{
	if ( !mso_check_allow('admin_ip_admin_page') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Admin IP"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Admin IP - " . $args; ' );

	require(getinfo('plugins_dir') . 'admin_ip/admin.php');
}


?>