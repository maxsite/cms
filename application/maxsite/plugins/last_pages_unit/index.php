<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function last_pages_unit_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('last_pages_unit_widget', t('Последние записи'));
}

# функция выполняется при деинсталяции плагина
function last_pages_unit_uninstall($args = array())
{
	mso_delete_option_mask('last_pages_unit_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function last_pages_unit_widget($num = 1)
{
	$widget = 'last_pages_unit_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else 
		$options['header'] = '';

	return last_pages_unit_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function last_pages_unit_widget_form($num = 1)
{
	$widget = 'last_pages_unit_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) )	$options['header'] = t('Последние записи');
	if ( !isset($options['cache_time']) )	$options['cache_time'] = 0;
	if ( !isset($options['prefs']) ) 	$options['prefs'] = '
cat_id = 1
limit = 3
thumb = 0
content = 0
placehold = 0
line1 = [title]
line2 = [thumb]
line3 = 
line4 = 
line5 = 
page_start = <li>
page_end = </li>
title_start = 
title_end = 
block_start= <div class="layout-center pad20 pad10-b"><ul class="pad0">
block_end = </ul></div>
';

	// вывод самой формы
	$CI = & get_instance();
	
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
		
	$form .= mso_widget_create_form(t('Время кеширования'), form_input( array( 'name'=>$widget . 'cache_time', 'value'=>$options['cache_time'] ) ), '');
	
	$form .= mso_widget_create_form(t('Параметры отображения'), form_textarea( array( 'name'=>$widget . 'prefs', 'value'=>$options['prefs'], 'rows' => '10')), 'Доступны параметры PHP-класса <a href="http://maxsite.org/page/vyvod-blokov-zapisej-v-shablone">Block_pages</a>');
	
	return $form;
}

# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_pages_unit_widget_update($num = 1)
{
	$widget = 'last_pages_unit_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['cache_time'] = (int) mso_widget_get_post($widget . 'cache_time');
	$newoptions['prefs'] = mso_widget_get_post($widget . 'prefs');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


function last_pages_unit_widget_custom($arg = array(), $num = 1)
{
	if ( !isset($arg['header']) )		$arg['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . t('Последние записи') . mso_get_val('widget_header_end', '</span></div>');
	if ( !isset($arg['cache_time']) )	$arg['cache_time'] = 0;
	if ( !isset($arg['prefs']) )		$arg['prefs'] = '';

	$out = '';
	
	$cache_key = 'last_pages_unit_widget-' . serialize($arg) . '-' . $num;
	
	if ($arg['cache_time'] > 0 and $out = mso_get_cache($cache_key) )
	{
		return $out; # да есть в кэше
	}

	$units = mso_section_to_array('[unit]'.$arg['prefs'].'[/unit]', '!\[unit\](.*?)\[\/unit\]!is');

	ob_start();

	if ($units and isset($units[0]) and $units[0]) 
	{
		$UNIT = $units[0];
		require(dirname(realpath(__FILE__)) . '/last-pages.php');
	}

	$out = $arg['header'] . ob_get_clean(); 
	
	if ($arg['cache_time'] > 0) mso_add_cache($cache_key, $out, $arg['cache_time'] * 60);
	
	return $out;
}

# end of file