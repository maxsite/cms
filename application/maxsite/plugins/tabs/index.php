<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function tabs_autoload($args = array())
{
	mso_hook_add('head', 'tabs_head');
	mso_hook_add('content', 'tabs_content');
	
	mso_register_widget('tabs_widget', t('Табы (закладки)')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function tabs_uninstall($args = array())
{	
	mso_delete_option_mask('tabs_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}


# функция, которая берет настройки из опций виджетов
function tabs_widget($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return tabs_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function tabs_widget_form($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['tabs']) ) $options['tabs'] = '';
	if ( !isset($options['type_func']) ) $options['type_func'] = 'widget';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Табы'), form_textarea( array( 'name'=>$widget . 'tabs', 'value'=>$options['tabs'] ) ), t('Указывайте по одному табу в каждом абзаце в формате: <strong>заголовок | виджет номер</strong><br>Например: <strong>Цитаты | randomtext_widget 1</strong><br>Для ушки: <strong>Цитаты | ушка_цитаты</strong>'));
	
	$form .= mso_widget_create_form(t('Использовать с'), form_dropdown( $widget . 'type_func', array( 'widget'=>t('виджетами'), 'ushka'=>t('ушками')), $options['type_func']), '');


	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function tabs_widget_update($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['tabs'] = mso_widget_get_post($widget . 'tabs');
	$newoptions['type_func'] = mso_widget_get_post($widget . 'type_func');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}


# подключаем в заголовок стили и js
function tabs_head($args = array()) 
{
	/*
		Идея и основа кода (c) Dimox, http://dimox.name/universal-jquery-tabs-script/
		Переделка, адаптация (с) MAX (http://maxsite.org/), Cuprum (http://cuprum.name/)
	*/

	echo mso_load_jquery() 
		. mso_load_jquery('jquery.cookie.js')
		. mso_load_script(getinfo('plugins_url'). 'tabs/tabs.js');

	return $args;
}

# функции плагина
function tabs_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['tabs']) ) $options['tabs'] = '';
	if ( !isset($options['type_func']) ) $options['type_func'] = 'widget';
	
	$ar = explode("\n", trim($options['tabs'])); // все табы в массив
	
	$tabs = array(); // наши закладки
	if ($ar)
	{
		foreach($ar as $key=>$val)
		{
			$t = explode('|', $val);
			if (isset($t[0]) and isset($t[1])) // есть и название и ушка
			{
				$tabs[$key]['title'] = trim($t[0]);
				$tabs[$key]['ushka'] = trim($t[1]);
			}
		}
	}
	
	if ($tabs) // есть закладки, можно выводить
	{
		$out .= NR . '<div class="tabs"><ul class="tabs-nav">' . NR;
		
		$current = ' tabs-current';
		
		foreach($tabs as $key => $tab)
		{
			$out .= '<li class="elem' . $current . '"><span>' . $tab['title'] . '</span></li>' . NR;
			$current = '';
		}
		$out .= '</ul><div class="clearfix"></div>' . NR;
		
		$visible = ' tabs-visible';
		foreach($tabs as $key => $tab)
		{
			if ($options['type_func'] == 'widget') // выводим с помощью функции виджета ($tab['ushka'])
			{
				$func = $tab['ushka']; // category_widget 20
				$nm = 0;
				
				// разделим и определим номер виджета
				$arr_w = explode(' ', $func); // в массив
				
				if ( sizeof($arr_w) > 1 ) // два или больше элементов
				{
					$func = trim( $arr_w[0] ); // первый - функция
					$nm = trim( $arr_w[1] ); // второй - номер виджета
				}
				
				// замены номера виджета из mso_show_sidebar()
				$nm = mso_slug($nm);
				$nm = str_replace('--', '-', $nm);
					
				if ( function_exists($func) ) 
				{
					$func = $func($nm);
				}
				else $func = 'no-func';
			}
			else 
			{
				if (function_exists('ushka')) $func = ushka($tab['ushka']);
				else $func = '';
			}

			$out .= NR . '<div class="tabs-box' . $visible  . '">' . $func . '</div>' . NR;
			$visible = '';
		}
			
		$out .= '</div><!-- div class="tabs -->' . NR;
	}
	
	if ($out and $options['header']) 
		$out = $options['header'] . '<div class="widget-content">' . $out . '</div><!-- div class=widget-content -->';
	else
		$out = '<div class="widget-content">' . $out . '</div><!-- div class=widget-content -->';
	
	return $out;
}

# создание табов в тексте записи
/*
[tabs]

[tab=Один]
текст первый
[/tab]

[tab=Два]
текст второй
[/tab]

[/tabs]

*/
function tabs_content($text = '')
{
	if (strpos($text, '[tabs]') !== false)
	{
		$text = preg_replace_callback('!\[tabs\](.*?)\[/tabs\]!is', 'tabs_content_callback', $text );
	}
	
	return $text;
}

# колбак функция обработки внутри [tabs] [/tabs]
function tabs_content_callback($matches) 
{
	global $mso_page_current;
	
	$text = $matches[1];
	$text = str_replace("\r", "", $text);
	$text = str_replace("\n", "", $text);
	
	$text = str_replace("<br>", "", $text); // удалим br

	$text = trim($text);

	$out = '';
	
	$r = preg_match_all('!\[tab=(.*?)\](.*?)\[\/tab\]!is', $text, $all);
	
	if ($r) // есть вхождение tab
	{
		// сделаем под них массивы
		$names = $all[1]; // названия табов
		$text = $all[2]; // содеримое
		
		$out_names = ''; // html-блок названий
		$out_text = ''; // html-блок содержимого
		
		// индексы $names и $text должны совпадать
		foreach ($names as $key => $val)
		{
			if ($key === 0) 
			{
				$current = ' tabs-current';
				$visible = ' tabs-visible';
			}
			else 
			{
				$current = '';
				$visible = '';
			}
			
			$out_names .= '<li class="elem' . $current . '"><span>' . $val . '</span></li>';
			
			$out_text .= '<div class="tabs-box' . $visible . '">' . $text[$key] . '</div>';
		}
		
		if ($out_names and $out_text)
		{
			$page_id = (isset($mso_page_current['page_id'])) ? $mso_page_current['page_id'] : 0;
			
			$out = '[html]<div class="tabs_widget tabs_widget_' . $page_id . '"><div class="tabs"><ul class="tabs-nav">'
				. trim($out_names)
				. '</ul><div class="clearfix"></div>'
				. trim($out_text)
				. '</div></div>[/html]';
		}
	}
	
	return $out;
}


# end file