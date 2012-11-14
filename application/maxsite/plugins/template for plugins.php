<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

%%% - замените на имя плагина


# функция автоподключения плагина
function %%%_autoload()
{

}

# функция выполняется при активации (вкл) плагина
function %%%_activate($args = array())
{	
	mso_create_allow('%%%_edit', t('Админ-доступ к настройкам %%%'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function %%%_deactivate($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function %%%_uninstall($args = array())
{	
	mso_delete_option('plugin_%%%', 'plugins' ); // удалим созданные опции
	mso_remove_allow('%%%_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function %%%_mso_options() 
{
	if ( !mso_check_allow('%%%_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_%%%', 'plugins', 
		array(
			'option1' => array(
							'type' => 'text', 
							'name' => t('Название'), 
							'description' => t('Описание'), 
							'default' => ''
						),
			),
		t('Настройки плагина %%%'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

# функции плагина
function %%%_custom($arg = array())
{

	
}

# end file