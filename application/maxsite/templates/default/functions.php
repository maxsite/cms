<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/ 
 * файл functions.php подключается при инициализации сайта
 */

// только для админки
if (is_type('admin')) {
	mso_register_sidebar('1', tf('Первый сайдбар')); // регистрируем сайдбар
	
	if ($fn = mso_fe('custom/set_val_admin.php')) require_once $fn; // набор из mso_set_val
	if ($fn = mso_fe('custom/template-admin.php')) require_once $fn; // функции только для админки
}

if ($fn = mso_fe('custom/set_val.php')) require_once $fn; // набор из mso_set_val
if ($fn = mso_fe('custom/template.php')) require_once $fn; // функции
if ($fn = mso_fe('custom/my-template.php')) require_once $fn; // выполнение

# end of file
