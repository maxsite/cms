<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// для users может быть несколько разных действий в зависимости от сегментов
// формируем по ним правильный путь к файлу
 
if (mso_segment(3)=='edit')	// редактирование комюзера
{
	if ($fn = mso_find_ts_file('type/users/units/users-form.php')) 
	{
		require($fn);
		return;
	}
}
elseif (mso_segment(3)=='lost') 	// восстановление пароля комюзера
{
	if ($fn = mso_find_ts_file('type/users/units/users-form-lost.php')) 
	{
		require($fn);
		return;
	}
}
elseif (mso_segment(2)=='') // список всех комюзеров
{
	if ($fn = mso_find_ts_file('type/users/units/users-all.php')) 
	{
		require($fn);
		return;
	}
}
else // данные юзера - указан второй сегмент
{
	if ($fn = mso_find_ts_file('type/users/units/users.php')) 
	{
		require($fn);
		return;
	}
}
