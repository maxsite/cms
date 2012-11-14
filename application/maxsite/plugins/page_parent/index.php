<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function page_parent_autoload($args = array())
{
	mso_register_widget('page_parent_widget', t('Родительские/дочерние страницы')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function page_parent_uninstall($args = array())
{	
	mso_delete_option_mask('page_parent_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function page_parent_widget($num = 1) 
{
	$widget = 'page_parent_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return page_parent_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function page_parent_widget_form($num = 1) 
{
	$widget = 'page_parent_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['page_id']) ) $options['page_id'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Номер страницы'), form_input( array( 'name'=>$widget . 'page_id', 'value'=>$options['page_id'] ) ), '');
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function page_parent_widget_update($num = 1) 
{
	$widget = 'page_parent_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['page_id'] = mso_widget_get_post($widget . 'page_id');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function page_parent_widget_custom($options = array(), $num = 1)
{
	// кэш не нужен, потому что mso_page_map сама всё кэширует

	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['page_id']) ) $options['page_id'] = 0;
	
	if (!$options['page_id']) return '';
	
	$r = mso_page_map($options['page_id']); // построение карты страниц
	
	// создание ul-списка со своими опциями
	$out = mso_create_list($r, array('format_current'=>'[LINK][TITLE][/LINK]',
									'class_ul'=>'is_link page_parent', 
									'class_child'=>'is_link page_parent_child', 'current_id'=>false ) ); 
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	return $out;	
}

# end file