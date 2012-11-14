<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function redirect_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'redirect_admin_init'); # хук на админку
	mso_hook_add( 'init', 'redirect_init'); # хук на init
	mso_hook_add( 'custom_page_404', 'redirect_custom_page_404', 5); # хук на custom_page_404 с низким приоритетом
}

# функция выполняется при активации (вкл) плагина
function redirect_activate($args = array())
{	
	mso_create_allow('redirect_edit', t('Админ-доступ к плагину редиректов'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function redirect_uninstall($args = array())
{
	mso_remove_allow('redirect_edit'); // удалим созданные разрешения
	mso_delete_option('redirect', 'plugins' ); // удалим созданные опции
	return $args;
}

# цепляемся к хуку init
function redirect_init($args = array())
{
	// получаем опции
	// в опциях all - строки с редиректами
	// загоняем их в массив
	// смотрим текущий url
	// если он есть в редиректах, то редиректимся

	$options = mso_get_option('redirect', 'plugins', array());
	if ( !isset($options['all']) ) return $args; // нет опций

	$all = explode("\n", $options['all']); // разобъем по строкам

	if (!$all) return $args; // пустой массив

	// текущий адрес
	$current_url = mso_current_url(true);

	foreach ($all as $row) // перебираем каждую строчку
	{
		$urls = explode('|', $row); //  адрес | редирект | 301, 302
		$urls = array_map('trim', $urls);
		if ( isset($urls[0]) && isset($urls[1]) && $urls[0] && $urls[1]) // если есть урлы
		{
			//проверяем, используются ли шаблоны в $urls[0]
			if ( preg_match("/\(.*\)+/",$urls[0]) )
			{
				$patern = preg_replace( "![\-\?]+!", '\\\$0', $urls[0] );
				if ( preg_match("!" . $patern . "!i", $current_url, $match) )
				{
					$urls[0] = $match[0];
					$cn = count($match);
					for($i=1; $i < $cn; $i++)
						$urls[1] = str_replace('$' . $i, $match[$i], $urls[1]);
				}
			}
			//
			if($current_url != $urls[0]) continue; // адреса разные
			// совпали, делаем редирект
			if ( isset($urls[2]) ) mso_redirect($urls[1], true, $urls[2]);
			else mso_redirect($urls[1], true);
		}
	}
	return $args;
}


function redirect_custom_page_404($args = false)
{
	// это почти аналог redirect_init с той разницей, что 
	// хук срабатывает только при page_404

	// получаем опции
	// в опциях all - строки с редиректами
	// загоняем их в массив
	// смотрим текущий url
	// если он есть в редиректах, то редиректимся
	$options = mso_get_option('redirect', 'plugins', array());
	if ( !isset($options['all404']) ) return $args; // нет опций

	$all = explode("\n", $options['all404']); // разобъем по строкам

	if (!$all) return $args; // пустой массив

	// текущий адрес
	$current_url = mso_current_url(true);
	

	foreach ($all as $row) // перебираем каждую строчку
	{
		$urls = explode('|', $row); //  адрес | редирект | 301, 302
		$urls = array_map('trim', $urls);
		if ( isset($urls[0]) && isset($urls[1]) && $urls[0] && $urls[1]) // если есть урлы
		{
			//проверяем, используются ли шаблоны в $urls[0]
			if ( preg_match("/\(.*\)+/",$urls[0]) )
			{
				$patern = preg_replace( "![\-\?]+!", '\\\$0', $urls[0] );
				if ( preg_match("!" . $patern . "!i", $current_url, $match) )
				{
					$urls[0] = $match[0];
					$cn = count($match);
					for($i=1; $i < $cn; $i++)
						$urls[1] = str_replace('$' . $i, $match[$i], $urls[1]);
				}
			}
			//
			if($current_url != $urls[0]) continue; // адреса разные
			
			// совпали, делаем редирект
			if ( isset($urls[2]) ) mso_redirect($urls[1], true, $urls[2]);
				else mso_redirect($urls[1], true);
			
		}
	}
	return $args;
}


# функция выполняется при хуке admin_init
function redirect_admin_init($args = array())
{
	if ( !mso_check_allow('redirect_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'redirect'; // url и hook

	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки

	mso_admin_menu_add('plugins', $this_plugin_url, t('Редиректы'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
	# связанную функцию именно она будет вызываться, когда
	# будет идти обращение по адресу http://сайт/admin/redirect
	mso_admin_url_hook ($this_plugin_url, 'redirect_admin_page');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function redirect_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('redirect_edit') )
	{
		echo t('Доступ запрещен');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редиректы') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редиректы') . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'redirect/admin.php');
}

# end file
