<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) MaxSite CMS
 * http://max-3000.com/
 *
 * Функции для админ-панели
 *
 */

# возвращает файлы для компонент
function default_components()
{
	// запоминаем результат, чтобы несколько раз не вызывать функцию mso_get_path_files
	static $all = false; 
	
	if ($all === false)
		$all = mso_get_dirs(getinfo('template_dir') . 'components/', array(), true);
	
	return '0||' . tf('Отсутствует') . '#' . implode($all, '#');
}

# возвращает файлы для css-профиля
function default_profiles()
{
	$all = mso_get_path_files(getinfo('template_dir') . 'assets/css/profiles/', getinfo('template_url') . 'assets/css/profiles/', false, array('css'));
	
	if ($all)	$all = ' ||Нет #' . implode($all, '#');
		else $all = ' ||Нет';
	
	return $all;
}


# end of file