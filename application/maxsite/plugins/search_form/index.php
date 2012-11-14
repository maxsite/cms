<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function search_form_autoload($args = array())
{
	mso_register_widget('search_form_widget', t('Форма поиска')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function search_form_uninstall($args = array())
{	
	mso_delete_option_mask('search_form_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function search_form_widget($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return search_form_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function search_form_widget_form($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text']) ) $options['text'] = t('Что искать?');
	if ( !isset($options['submit']) ) $options['submit'] = t('Поиск');
	if ( !isset($options['style_text']) ) $options['style_text'] = '';
	if ( !isset($options['style_submit']) ) $options['style_submit'] = '';
	if ( !isset($options['text_posle']) ) $options['text_posle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])));
	
	$form .= mso_widget_create_form(t('Текст подсказки'), form_input( array( 'name'=>$widget . 'text', 'value'=>$options['text'])));
	
	$form .= mso_widget_create_form(t('Текст на кнопке'), form_input( array( 'name'=>$widget . 'submit', 'value'=>$options['submit'])));
	
	$form .= mso_widget_create_form(t('CSS-стиль текста'), form_input( array( 'name'=>$widget . 'style_text', 'value'=>$options['style_text'])));
	
	$form .= mso_widget_create_form(t('CSS-стиль кнопки'), form_input( array( 'name'=>$widget . 'style_submit', 'value'=>$options['style_submit'])));
	
	$form .= mso_widget_create_form(t('Текст внизу'), form_textarea( array( 'name'=>$widget . 'text_posle', 'value'=>$options['text_posle'], 'rows' => '3')));
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function search_form_widget_update($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['submit'] = mso_widget_get_post($widget . 'submit');
	$newoptions['style_text'] = mso_widget_get_post($widget . 'style_text');
	$newoptions['style_submit'] = mso_widget_get_post($widget . 'style_submit');
	$newoptions['text_posle'] = mso_widget_get_post($widget . 'text_posle');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function search_form_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text']) ) $options['text'] = t('Что искать?');
	if ( !isset($options['submit']) ) $options['submit'] = t('Поиск');
	if ( !isset($options['style_text']) ) $options['style_text'] = '';
	if ( !isset($options['style_submit']) ) $options['style_submit'] = '';
	if ( !isset($options['text_posle']) ) $options['text_posle'] = '';
	
	if ($options['text_posle'])
	{
		// d тексте можно указать [SITEURL], который заменится на адрес сайта
		$options['text_posle'] = str_replace('[SITEURL]', getinfo('site_url'), $options['text_posle']);
	}
	
	if ($options['style_text']) $options['style_text'] = ' style ="' . $options['style_text'] . '"';
	
	$out .= '
	<form class="search_form_widget fform" name="f_search" method="get" onsubmit="location.href=\'' . getinfo('siteurl') . 'search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;">
	<p>
	<span>
	<input type="text" name="s"' . $options['style_text'] . ' class="search_text" onfocus="if (this.value == \'' . $options['text'] . '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'' . $options['text'] . '\';}" value="' . $options['text'] . '">
	</span>
	<span class="fempty"></span>
	<span>
	<button type="submit" name="Submit" style="' . $options['style_submit'] . '" class="search_submit">' . $options['submit'] . '</button>
	</span>
	</p>'
	. $options['text_posle']
	.'</form>';
	
	if ($options['header']) $out = $options['header'] . $out;
	
	return $out;	
}

# end file