<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
global $MSO;

$dir_admin = $MSO->config['admin_dir'];


if (!is_login())
{
	// require($dir_admin . 'template/loginform.php');
	
	require($dir_admin . 'template/' . mso_get_option('admin_template', 'general', 'default') .  '/loginform.php');
	
}
else
{
	require($dir_admin . 'common.php'); # админские функции
	require($dir_admin . 'default.php'); # дефолтные хуки и значения
	
	mso_admin_init(); # инициализация
	
	# подключаем шаблон админки
	
	$fn = $dir_admin . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/template.php';
	
	if (file_exists($fn)) require($fn);
		else require($dir_admin . 'template/default/template.php');
	
	
}

# end file