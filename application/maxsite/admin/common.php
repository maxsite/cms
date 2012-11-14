<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
# инициализация админки
function mso_admin_init($args = array())
{
	global $admin_menu;
	
	$admin_menu = array();
	
	$menu = mso_hook('admin_menu');
	if (!$menu) $menu = mso_hook('admin_menu_default');

	mso_admin_plugins_default(); # дефолтные плагины
	
	mso_hook('admin_init');
}

# добавляет пункт меню админки
function mso_admin_menu_add($group = '', $url = '', $name = '', $sort = 10)
{
	global $admin_menu;
	
	# группа - адрес - название ссылка - порядок в своей группе
	if ( ($url > '') and ($sort == 0) ) $sort = 10;

	$admin_menu[$group][$url] = $sort . '_' . $name;
	natsort($admin_menu[$group]);

	return $admin_menu;
}

# служебная функция
# удяляем из меню начальные цифры 10_меню -> меню
function _mso_del_menu_pod($text)
{
	$pos = strstr($text, '_');
	if ($pos !== false) $text = substr($pos, 1);
	return $text;
}

# вывод меню в шаблоне админки
function mso_admin_menu()
{
	$out = mso_hook('admin_menu', false);
	if (!$out) $out = mso_hook('admin_menu_default');
	return $out;
}

# вывод хидера в шаблоне админки
function mso_admin_header()
{
	$out = mso_hook('admin_header', false);
	if (!$out) $out = mso_hook('admin_header_default');
	return $out;
}

# вывод подвала в шаблоне админки
function mso_admin_footer()
{
	$out = mso_hook('admin_footer', false);	
	if (!$out) $out = mso_hook('admin_footer_default');
	return $out;
}

# вывод контента в шаблоне админки
function mso_admin_content()
{
	global $MSO;
	
	$out = mso_hook('admin_content_do');
	
	if ( count($MSO->data['uri_segment']) > 1 )
	{
		$url = $MSO->data['uri_segment'][2];
		
		if ( mso_hook_present('admin_url_' . $url)) $out = mso_hook('admin_url_' . $url, $out);
			else $out = mso_hook('admin_content_default', $out);
	}
	else
	{
			$out = mso_hook('admin_content_default', $out);
	}

	$out = mso_hook('admin_content', $out);
	
	return $out;
}

# построение ссылки с учетом указанного 3-го сегмента 
# admin / options / tri
# можно стоить меню в плагинах админки. Можно указать класс выбранной ссылки
function mso_admin_link_segment_build($plu_url, $segment, $title, $class_link)
{
	global $MSO;
	
	// определяем текущий сегмент
	if ( count($MSO->data['uri_segment']) > 2 )
		$seg = $MSO->data['uri_segment'][3];
	else $seg = false;
	
	// если сегменты совпадают, то ссылку выделяем классом
	if ($seg == $segment) $class_link = ' class="' . $class_link . '"';
		else $class_link = '';
	
	$out = '<a href="' . $plu_url .'/' . $segment . '"' . $class_link . '>' . $title . '</a>';
	
	return $out;
}


# деактивация плагина
function mso_plugin_deactivate($f_name)
{
	global $MSO;
	
	// если плагин уже активен, то его удаляем из активных
	if (in_array($f_name, $MSO->active_plugins))
	{ // в массиве, значит активен - отключаем его
		
		// выполним деактивацию в самом плагине, если есть
		$f = $f_name . '_deactivate';
		if (function_exists($f)) $f();
		
		// удалим из глобального
		$MSO->active_plugins = array_flip($MSO->active_plugins);
		unset($MSO->active_plugins[$f_name]);
		$MSO->active_plugins = array_flip($MSO->active_plugins);
		
		// обновляем настройки
		mso_add_option ('active_plugins', $MSO->active_plugins, 'general');
		// return '<div class="update">Плагин <strong>' . $f_name . '</strong> выключен!</div>';
		return true;
	}
	else return false;
}


# активация плагина
function mso_plugin_activate($f_name)
{
	global $MSO;
	
	// если плагин уже активен, то его удаляем из активных
	if (!in_array($f_name, $MSO->active_plugins))
	{  // неактивный плагин
		mso_plugin_load($f_name);
		
		$f = $f_name . '_activate';
		if (function_exists($f)) $f();
		
		mso_add_option ('active_plugins', $MSO->active_plugins, 'general');
		return true;
	}
	else return false;
}


# uninstall плагина если он активный
function mso_plugin_uninstall($f_name)
{
	global $MSO;
	
	if (in_array($f_name, $MSO->active_plugins))
	{ // в массиве, значит активен - отключаем его
		
		// выполним деактивацию в самом плагине, если есть
		$f = $f_name . '_deactivate';
		if (function_exists($f)) $f();
		
		$f = $f_name . '_uninstall';
		if (function_exists($f)) $f();
		
		// удалим из глобального
		$MSO->active_plugins = array_flip($MSO->active_plugins);
		unset($MSO->active_plugins[$f_name]);
		$MSO->active_plugins = array_flip($MSO->active_plugins);
		
		// обновляем настройки
		mso_add_option ('active_plugins', $MSO->active_plugins, 'general');
		// echo '<div class="update">Плагин <strong>' . $f_name . '</strong> деинсталирован!</div>';
		return true;
	}
	else return false;
}

?>