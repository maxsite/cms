<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * https://github.com/maxsite/cms/issues/171
 * http://ruogp.me/
 * https://habrahabr.ru/post/278459/
 *
 */

# функция автоподключения плагина
function open_graph_autoload()
{
	mso_hook_add('html_attr', 'open_graph_html_attr'); # хук на атрибуты <HTML>
	mso_hook_add('head', 'open_graph_head'); # хук на <HEAD>
}

# функция выполняется при активации (вкл) плагина
function open_graph_activate($args = array())
{	
	mso_create_allow('open_graph_edit', t('Админ-доступ к настройкам open_graph'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function open_graph_uninstall($args = array())
{	
	mso_delete_option('plugin_open_graph', 'plugins' ); // удалим созданные опции
	mso_remove_allow('open_graph_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function open_graph_mso_options() 
{
	if ( !mso_check_allow('open_graph_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	// mso_admin_plugin_options('plugin_open_graph', 'plugins', 
	// 	array(
	// 		'option1' => array(
	// 						'type' => 'text', 
	// 						'name' => t('Название'), 
	// 						'description' => t('Описание'), 
	// 						'default' => ''
	// 					),
	// 		),
	// 	t('Настройки плагина Open Graph'), // титул
	// 	t('Укажите необходимые опции.')   // инфо
	// );
	
}

# атрибуты HTML
# отдавать результат по return
function open_graph_html_attr($arg = '')
{
	// если уже есть подключение og, то ничего не делаем
	if (strpos($arg, 'prefix="og: http://ogp.me/ns#"') === false)
		$arg .= ' prefix="og: http://ogp.me/ns#"';
	
	return $arg;
}

# meta-данные
# выводятся через echo
function open_graph_head($arg = '')
{
	global $page;
	
	if (is_type('home'))
		echo '<meta property="og:type" content="website">';
	else
		echo '<meta property="og:type" content="article">';
		
	echo '<meta property="og:title" content="' . mso_head_meta('title') . '">';
	echo '<meta property="og:description" content="' . mso_head_meta('description') . '">';
	echo '<meta property="og:url" content="' . mso_link_rel('canonical', '', true) . '">';
	
	if (is_type('page') and isset($page['page_meta']['image_for_page'][0]))
	{
		echo '<meta property="og:image" content="' . $page['page_meta']['image_for_page'][0] . '">';
	}
	
	return $arg;
}


# end of file