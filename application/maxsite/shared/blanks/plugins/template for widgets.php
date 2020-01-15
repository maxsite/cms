<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MaxSite CMS
 * (c) https://max-3000.com/
 */

// MY - замените на имя плагина

// функция автоподключения плагина
function MY_autoload()
{
	// регистрируем виджет
	mso_register_widget('MY_widget', t('MY'));
}

// функция выполняется при деинсталяции плагина
function MY_uninstall()
{
	// удалим созданные опции
	mso_delete_option_mask('MY_widget_', 'plugins');
}

// функция, которая берет настройки из опций виджетов
function MY_widget($num = 1)
{
	$widget = 'MY_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', []); // получаем опции

	// заменим заголовок, чтобы был в .mso-widget-header
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
