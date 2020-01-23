<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// MY - замените на имя плагина

// функция автоподключения плагина
function MY_autoload()
{
	mso_hook_add('admin_init', 'MY_admin_init'); // хук на админку
	mso_register_widget('MY_widget', t('MY')); // регистрируем виджет
}

// функция выполняется при активации (вкл) плагина
function MY_activate()
{
	mso_create_allow('MY_edit', t('Админ-доступ к настройкам') . ' ' . t('MY'));
}

// функция выполняется при деинсталяции плагина
function MY_uninstall()
{
	mso_delete_option_mask('MY_widget_', 'plugins'); // удалим созданные опции
	mso_remove_allow('MY_edit'); // удалим созданные разрешения
}

// функция выполняется при указаном хуке admin_init
function MY_admin_init($args = [])
{
	if (mso_check_allow('MY_edit')) {
		$this_plugin_url = 'MY'; // url и hook

		// добавляем свой пункт в меню админки
		// первый параметр - группа в меню
		// второй - это действие/адрес в url - http://сайт/admin/demo
		//			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		// Третий - название ссылки	

		mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин MY'));

		// прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		// связанную функцию именно она будет вызываться, когда 
		// будет идти обращение по адресу http://сайт/admin/MY
		mso_admin_url_hook($this_plugin_url, 'MY_admin_page');
	}

	return $args;
}

// функция вызываемая при хуке, указанном в mso_admin_url_hook
function MY_admin_page($args = [])
{
	if (!mso_check_allow('MY_edit')) {
		echo t('Доступ запрещен');
		
		return $args;
	}

	// выносим админские функции отдельно в файл
	mso_hook_add_dinamic('mso_admin_header', ' return $args . "' . t('MY') . '"; ');
	mso_hook_add_dinamic('admin_title', ' return "' . t('MY') . ' - " . $args; ');

	require(getinfo('plugins_dir') . 'MY/admin.php');
}


// функция, которая берет настройки из опций виджетов
function MY_widget($num = 1)
{
	$widget = 'MY_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', []); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header']) {
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	} else {
		$options['header'] = '';
	}

	return MY_widget_custom($options, $num);
}

// форма настройки виджета 
// имя функции = виджет_form
function MY_widget_form($num = 1)
{
	$widget = 'MY_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции 
	$options = mso_get_option($widget, 'plugins', []);

	if (!isset($options['header'])) $options['header'] = '';

	// вывод самой формы
	$CI = &get_instance();
	$CI->load->helper('form');

	$form = mso_widget_create_form(t('Заголовок'), form_input(
		[
			'name' => $widget . 'header',
			'value' => $options['header']
		],
		t('Подсказка')
	));

	// $form .= mso_widget_create_form(t(''), , t(''));

	return $form;
}

// сюда приходят POST из формы настройки виджета
// имя функции = виджет_update
function MY_widget_update($num = 1)
{
	$widget = 'MY_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', []);

	// обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');

	if ($options != $newoptions)
		mso_add_option($widget, $newoptions, 'plugins');
}

// функции плагина
function MY_widget_custom($options = [], $num = 1)
{
	// кэш 
	$cache_key = 'MY_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	
	if ($k) return $k; // да есть в кэше

	$out = '';

	if (!isset($options['header'])) $options['header'] = '';

	// ... код виджета ...

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}

# end of file
