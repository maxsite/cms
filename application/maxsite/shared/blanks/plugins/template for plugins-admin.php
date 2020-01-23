<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// MY - замените на имя плагина

// функция автоподключения плагина
function MY_autoload()
{
	mso_hook_add('admin_init', 'MY_admin_init'); // хук на админку
}

// функция выполняется при активации (вкл) плагина
function MY_activate()
{
	mso_create_allow('MY_edit', t('Админ-доступ к настройкам MY'));
}

// функция выполняется при деинсталяции плагина
function MY_uninstall()
{
	mso_delete_option('plugin_MY', 'plugins'); // удалим созданные опции
	mso_remove_allow('MY_edit'); // удалим созданные разрешения
}

// функция выполняется при указаном хуке admin_init
function MY_admin_init($args = [])
{
	if (!mso_check_allow('MY_edit')) {
		return $args;
	}

	$this_plugin_url = 'MY'; // url и hook

	// добавляем свой пункт в меню админки
	// первый параметр - группа в меню
	// второй - это действие/адрес в url - http://сайт/admin/demo
	//			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	// Третий - название ссылки	

	mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин MY'));

	// прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	// связанную функцию именно она будет вызываться, когда 
	// будет идти обращение по адресу http://сайт/admin/MY
	mso_admin_url_hook($this_plugin_url, 'MY_admin_page');

	return $args;
}

// функция вызываемая при хуке, указанном в mso_admin_url_hook
function MY_admin_page($args = [])
{
	// выносим админские функции отдельно в файл

	if (!mso_check_allow('MY_edit')) {
		echo t('Доступ запрещен');

		return $args;
	}

	mso_hook_add_dinamic('mso_admin_header', ' return $args . "' . t('MY') . '"; ');
	mso_hook_add_dinamic('admin_title', ' return "' . t('MY') . ' - " . $args; ');

	require(getinfo('plugins_dir') . 'MY/admin.php');
}

// функции плагина
function MY_custom($arg = [], $num = 1)
{ 
	
}

# end of file
