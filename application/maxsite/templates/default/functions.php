<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * файл functions.php подключается при инициализации сайта
 *
 */

# данные для админки
if (is_type('admin'))
{
	# регистрируем сайдбар
	mso_register_sidebar('1', tf('Первый сайдбар'));
	
	# набор из mso_set_val
	if ($fn = mso_fe('custom/set_val_admin.php')) require_once($fn);
	
	# функции для админки
	if ($fn = mso_fe('custom/template-admin.php')) require_once($fn);
}
else
{
	# набор из mso_set_val
	if ($fn = mso_fe('custom/set_val.php')) require_once($fn);

	# дополнительный файл template.php
	if ($fn = mso_fe('custom/template.php')) require_once($fn);
}

if ($fn = mso_fe('custom/my-template.php')) require_once($fn);

# end of file