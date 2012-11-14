<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function cron_autoload()
{
	mso_hook_add('init', 'cron_custom', 0); # должен сработать последним, поэтому приоритет менее 10
}

# срабатывание крона при инициализации
function cron_custom($args = array())
{
	$options = mso_get_option('plugin_cron', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'cron'; 
	
	if ( mso_segment(1) == $options['slug'] ) 
	{
		mso_hook('cron'); # это крон, выполняем его хук
		die('Cron done'); # после крона всегда останавливаем выполнение
	}
	else return $args;
}


# функция выполняется при деинстяляции плагина
function cron_uninstall($args = array())
{	
	mso_delete_option('plugin_cron', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function cron_mso_options() 
{
	$options = mso_get_option('plugin_cron', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'cron';
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_cron', 'plugins', 
		array(
			'slug' => array(
							'type' => 'text', 
							'name' => t('Адрес крона'), 
							'description' => t('Укажите адрес по которому будет вызываться крон. Например: «cron» - адрес') . ' ' 
											. getinfo('siteurl') 
											. '<strong>cron</strong>', 
							'default' => 'cron'
						),
			),
		t('Настройки плагина cron'), // титул
		'Данный плагин предназначен для выполнения периодических задач. Вначале задайте адрес, по которому будет вызываться крон. После этого на сервере задайте период для своих задач и укажите в качестве программы:
		<pre>
GET ' . getinfo('siteurl') . $options['slug'] . '
		</pre>
		
		<p class="info">После этого те функции, которые определены для хука «cron» (плагины), будут выполняться в момент срабатывания крона на сервере.
		' // инфо
	);
}

# end file