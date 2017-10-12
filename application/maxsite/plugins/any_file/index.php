<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function any_file_autoload()
{
	// регистрируем виджет
	mso_register_widget('any_file_widget', t('any_file')); 
}

# функция выполняется при деинсталяции плагина
function any_file_uninstall($args = array())
{	
	// удалим созданные опции
	mso_delete_option_mask('any_file_widget_', 'plugins'); 
	return $args;
}

# функция, которая берет настройки из опций виджетов
function any_file_widget($num = 1) 
{
	$widget = 'any_file_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array()); // получаем опции
	
	// заменим заголовок, чтобы был в .mso-widget-header
	if (isset($options['header']) and $options['header']) 
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else 
		$options['header'] = '';
	
	return any_file_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function any_file_widget_form($num = 1) 
{
	$widget = 'any_file_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if (!isset($options['header'])) $options['header'] = '';
	if (!isset($options['file'])) $options['file'] = '';
	
	$class = '';
	
	if ($options['file'])
	{
		$file = str_replace('TEMPLATE/', getinfo('template_dir'), $options['file']);
		$file = str_replace('PLUGINS/',  getinfo('plugins_dir'), $file);
		$file = str_replace('UPLOADS/',  getinfo('uploads_dir'), $file);
		$file = str_replace('FCPATH/',   getinfo('FCPATH'), $file);
		$file = str_replace('SHARED/',   getinfo('shared_dir'), $file);
		
		$class = file_exists($file) ? 't-green' : 't-red';
	}

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), t('Заголовок виджета'));

	$form .= mso_widget_create_form(t('Подключаемый файл'), form_input( array( 'name'=>$widget . 'file', 'value'=>$options['file'], 'class'=>$class ) ), t('Путь следует указывать полный. Можно использовать замены: <code>TEMPLATE/</code> — каталог текущего шаблона; <code>PLUGINS/</code> — каталог плагинов; <code>UPLOADS/</code> — каталог uploads; <code>FCPATH/</code> — корень сайта; <code>SHARED/</code> — каталог shared;'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function any_file_widget_update($num = 1) 
{
	$widget = 'any_file_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	// обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['file'] = mso_widget_get_post($widget . 'file');
	
	if ($options != $newoptions) mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function any_file_widget_custom($options = array(), $num = 1)
{
	if (!isset($options['file']) or !$options['file']) return;
	
	if (!isset($options['header'])) $options['header'] = '';
	
	$file = str_replace('TEMPLATE/', getinfo('template_dir'), $options['file']);
	$file = str_replace('PLUGINS/', getinfo('plugins_dir'), $file);
	$file = str_replace('UPLOADS/', getinfo('uploads_dir'), $file);
	$file = str_replace('FCPATH/', getinfo('FCPATH'), $file);
	$file = str_replace('SHARED/', getinfo('shared_dir'), $file);
	
	if (file_exists($file)) 
	{	
		ob_start();
		require($file);
		$text = ob_get_contents();
		ob_end_clean();
		
		return $options['header'] . $text;
	}
}

# end of file