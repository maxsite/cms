<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function template_options_autoload($args = array())
{
	mso_create_allow('template_options_admin', t('Доступ к настройкам шаблона'));
	if (is_type('admin'))mso_hook_add('admin_init', 'template_options_admin_init'); # хук на админку
}

# функция выполняется при указаном хуке admin_init
function template_options_admin_init($args = array()) 
{
	if ( !mso_check_allow('template_options_admin') ) return $args;
	
	$this_plugin_url = 'template_options'; // url и hook
	mso_admin_menu_add('options', $this_plugin_url, t('Шаблон'), 2);
	mso_admin_url_hook ($this_plugin_url, 'template_options_admin_page');
	
	return $args;
}

# функция вызываемая при выборе пункта меню 'Настройка шаблона'
function template_options_admin_page($args = array()) 
{
	global $MSO;
	
	if ( !mso_check_allow('template_options_admin') ) return $args;
	
	# options/options.php
	$fn1 = $MSO->config['templates_dir'] . $MSO->config['template'] . '/options/options.php';
	
	# options.php
	$fn2 = $MSO->config['templates_dir'] . $MSO->config['template'] . '/options.php';
	
	# если файла нет, то подключаем дефолтный из админки
	# template_options/options.php
	$fn3 = $MSO->config['admin_plugins_dir'] . 'template_options/options.php';

	if (file_exists($fn1)) require_once($fn1);
	elseif (file_exists($fn2)) require_once($fn2);
	else require_once($fn3);

}


# end file