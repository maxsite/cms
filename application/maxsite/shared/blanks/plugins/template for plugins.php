<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// MY - замените на имя плагина

//  функция автоподключения плагина
function MY_autoload()
{ }

// функция выполняется при активации (вкл) плагина
function MY_activate()
{
	mso_create_allow('MY_edit', t('Админ-доступ к настройкам MY'));
}

// функция выполняется при деактивации (выкл) плагина
function MY_deactivate()
{
	// mso_delete_option('plugin_MY', 'plugins' ); // удалим созданные опции
}

// функция выполняется при деинсталяции плагина
function MY_uninstall()
{
	mso_delete_option('plugin_MY', 'plugins'); // удалим созданные опции
	mso_remove_allow('MY_edit'); // удалим созданные разрешения
}

// функция отрабатывающая миниопции плагина (function плагин_mso_options)
// если не нужна, удалите целиком
function MY_mso_options()
{
	if (!mso_check_allow('MY_edit')) {
		echo t('Доступ запрещен');
		return;
	}

	// ключ, тип, ключи массива
	mso_admin_plugin_options(
		'plugin_MY',
		'plugins',
		[
			'option1' => [
				'type' => 'text',
				'name' => t('Название'),
				'description' => t('Описание'),
				'default' => ''
			],
		],
		t('Настройки плагина MY'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

// функции плагина
function MY_custom($arg = [])
{ 
	
}

# end of file
