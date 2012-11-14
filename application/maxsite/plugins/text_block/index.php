<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function text_block_autoload($args = array())
{
	mso_register_widget('text_block_widget', t('Текстовый блок')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function text_block_uninstall($args = array())
{	
	mso_delete_option_mask('text_block_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function text_block_widget($num = 1) 
{
	$widget = 'text_block_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
		
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['type']) ) $options['type'] = 'html';
	
	return text_block_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function text_block_widget_form($num = 1) 
{

	$widget = 'text_block_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['type']) ) $options['type'] = 'html';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст'), form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ), '');
	
	$form .= mso_widget_create_form(t('Тип'), form_dropdown( $widget . 'type', array( 'html'=>t('HTML или текст'), 'php'=>'PHP'), $options['type']), t('Можно использовать HTML-тэги. Если тип PHP, то код должен выполняться без ошибок!'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function text_block_widget_update($num = 1) 
{
	$widget = 'text_block_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['type'] = mso_widget_get_post($widget . 'type');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function text_block_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];

	if ($options['type'] =='php')
	{
		ob_start();
		eval( '?>' . stripslashes( $text ) . '<?php ');
		$text = ob_get_contents();
		ob_end_clean();
	}
		
	return $header . $text;
}

# end file