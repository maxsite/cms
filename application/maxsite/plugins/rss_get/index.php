<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function rss_get_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('rss_get_widget', 'Get RSS'); 
}

# функция выполняется при деинсталяции плагина
function rss_get_uninstall($args = array())
{	
	mso_delete_option_mask('rss_get_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

function rss_get_widget($num = 1)
{
	$widget = 'rss_get_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
		else $options['header'] = '';

	return rss_get_widget_custom($options, $num);
}


function rss_get_widget_form($num = 1) 
{
	$widget = 'rss_get_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('RSS', 'plugins');
	if ( !isset($options['url']) ) $options['url'] = 'http://www.google.com/search?q="MaxSite%20CMS"&hl=ru&client=news&tbm=blg&output=rss';
	if ( !isset($options['count']) ) $options['count'] = 5;
	if ( !isset($options['time_cache']) ) $options['time_cache'] = 180;
	if ( !isset($options['max_word_description']) ) $options['max_word_description'] = '40';
	
	if ( !isset($options['format']) ) $options['format'] = '<p><a rel="nofollow" target="_blank" href="[link]">[link-host]</a><br><em>[title]</em><br>[dc:date]</p>';
	
	if ( !isset($options['format_date']) ) $options['format_date'] = 'd/m/Y H:i';
	
	if ( !isset($options['footer']) ) $options['footer'] = '';
	
	if ( !isset($options['fields']) ) $options['fields'] = 'title link description dc:date';
	
	if ( !isset($options['fields_items']) ) $options['fields_items'] = 'items';
	
	if ( !isset($options['charset']) ) $options['charset'] = 'UTF-8';
	
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Адрес'), form_input( array( 'name'=>$widget . 'url', 'value'=>$options['url'])  ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Поля RSS'), form_input( array( 'name'=>$widget . 'fields', 'value'=>$options['fields'] ) ), 'Поля, по которым будет строится формат вывода в виде [поле] или для вложенных [поле:субполе]');
	
	$form .= mso_widget_create_form(t('Поле с записями'), form_input( array( 'name'=>$widget . 'fields_items', 'value'=>$options['fields_items'] ) ), 'Обычно items');

	$form .= mso_widget_create_form(t('Формат вывода'), form_textarea( array( 'name'=>$widget . 'format', 'value'=>$options['format'])  ), '');
	
	$form .= mso_widget_create_form(t('Формат даты'), form_input( array( 'name'=>$widget . 'format_date', 'value'=>$options['format_date'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество слов'), form_input( array( 'name'=>$widget . 'max_word_description', 'value'=>$options['max_word_description'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст в конце блока'), form_textarea( array( 'name'=>$widget . 'footer', 'value'=>$options['footer']) ), '');

	$form .= mso_widget_create_form(t('Время кэша (минуты)'), form_input( array( 'name'=>$widget . 'time_cache', 'value'=>$options['time_cache'] ) ), '');
	
	$form .= mso_widget_create_form(t('Кодировка RSS'), form_input( array( 'name'=>$widget . 'charset', 'value'=>$options['charset'] ) ), '');
		
	return $form;
}


function rss_get_widget_update($num = 1) 
{
	$widget = 'rss_get_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['url'] = mso_widget_get_post($widget . 'url');
	
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	if ($newoptions['count'] < 1) $newoptions['count'] = 5;
	
	$newoptions['max_word_description'] = (int) mso_widget_get_post($widget . 'max_word_description');
	if ($newoptions['max_word_description'] < 1) $newoptions['max_word_description'] = 40;	
	
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['format_date'] = mso_widget_get_post($widget . 'format_date');
	$newoptions['footer'] = mso_widget_get_post($widget . 'footer');
	
	$newoptions['time_cache'] = (int) mso_widget_get_post($widget . 'time_cache');
	
	$newoptions['fields'] = mso_widget_get_post($widget . 'fields');
	$newoptions['fields_items'] = mso_widget_get_post($widget . 'fields_items');
	$newoptions['charset'] = mso_widget_get_post($widget . 'charset');
	
	if ( $options != $newoptions ) mso_add_option($widget, $newoptions, 'plugins');
}


#
function rss_get_widget_custom($arg, $num)
{
	# параметры ленты
	if ( !isset($arg['url']) ) $arg['url'] = 'http://www.google.com/search?q="MaxSite%20CMS"&hl=ru&client=news&tbm=blg&output=rss';
	if ( !isset($arg['count']) ) $arg['count'] = 5;
	if ( !isset($arg['format']) ) $arg['format'] = '<p><a rel="nofollow" target="_blank" href="[link]">[link-host]</a><br><em>[title]</em><br>[dc:date]</p>';
	if ( !isset($arg['format_date']) ) $arg['format_date'] = 'd/m/Y H:i';
	if ( !isset($arg['max_word_description']) ) $arg['max_word_description'] = 40;

	# оформление виджета
	if ( !isset($arg['header']) ) $arg['header'] = '';
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="rss_get">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';
	
	if ( !isset($arg['footer']) ) $arg['footer'] = '';
	
	if ( !isset($arg['time_cache']) ) $arg['time_cache'] = 180;
	
	if ( !isset($arg['fields']) ) $arg['fields'] = 'title link description summary dc:date dc:publisher dc:creator';
	
	if ( !isset($arg['field_items']) ) $arg['fields_items'] = 'items';
	if ( !isset($arg['charset']) ) $arg['charset'] = 'UTF-8';
	
	$rss = rss_get_go(array(
		'url' => $arg['url'], 
		'count' => $arg['count'], 
		'format' => $arg['format'], 
		'format_date' => $arg['format_date'], 
		'max_word_description' => $arg['max_word_description'], 
		'time_cache' => $arg['time_cache'],
		'fields' => $arg['fields'],
		'fields_items' => $arg['fields_items'],
		'charset' => $arg['charset'],
	));
	
	if ($rss) 
	{	
		return $arg['header'] . $arg['block_start'] . $rss . $arg['footer'] . $arg['block_end'];
	}
}


function rss_get_go($arg)
{	
	// здесь нет проверок на корректность $arg, потому что мы её уже выполнили в rss_get_widget_custom
	
	if (!$arg['url']) return false;
	
	# проверим кеш, может уже есть в нем все данные
	$cache_key = 'rss/' . 'rss_get_' . md5(serialize($arg));

	$k = mso_get_cache($cache_key, true);
	
	if ($k)
	{
		return $k; // да есть в кэше
	}
	else
	{
		require_once(getinfo('plugins_dir') . 'rss_get/lastrss.php');
		
		$rss_pars = new lastRSS();
		
		$rss_pars->convert_cp = $arg['charset'];
		
		$rss_pars->itemtags = mso_explode($arg['fields'], false);

		$rss = $rss_pars->Get($arg['url']);
	}
	
	if (!$rss) return '';
	
	if (isset($rss[$arg['fields_items']])) 
	{
		$rss = $rss[$arg['fields_items']];
		$rss = array_slice($rss, 0, $arg['count']); // колво записей
	}
	else
	{
		return ''; // нет items
	}
	
	// меняем ключи с values и заполняем нулями - это шаблон для полей
	$fields = array_fill_keys(mso_explode($arg['fields'], false), false);

	$out = '';
	
	foreach ($rss as $item) 
	{ 
		// заполним массив шаблона полей значениями из итема
		$fields_out = $fields;
		
		foreach ($fields as $field => $tmp) 
		{
			if (isset($item[$field])) // одиночный ключ поле
			{
				$fields_out[$field] = $item[$field];
				continue;
			}
		}
		
		$out1 = $arg['format'];

		foreach ($fields_out as $field => $value) 
		{
			// обратное преобразование в html
			$value = str_replace('&lt;', '<', $value);
			$value = str_replace('&gt;', '>', $value);
			$value = str_replace('&amp;', '&', $value);
			$value = str_replace('<![CDATA[', '', $value);
			$value = str_replace(']]>', '', $value);
			
			// если стоит максимальное колво слов, то обрежем лишнее
			if ($arg['max_word_description'] and $field != 'link')
				$value = mso_str_word($value, $arg['max_word_description']);
			
			// если поле содержит date, то пробуем его преобразовать в нужный нам формат даты
			if ( (strpos($field, 'dc:date') !== false) or (strpos($field, 'date') !== false) or (strpos($field, 'pubDate') !== false) or $field == 'published' or $field == 'updated' )
			{
				if ( ($d = strtotime($value)) !== -1) // успешное преобразование
					$value = date($arg['format_date'], $d);
			}
			
			if ($field == 'link') 
			{
				$link_host = parse_url($value);
				$link_host = $link_host['host'];
				$out1 = str_replace('[link-host]', $link_host, $out1);
			}
			
			$out1 = str_replace('[' . $field . ']', $value, $out1);
		}
		
		$out .= $out1;
	}
	
	if ($out and $arg['time_cache'])
	{
		mso_add_cache($cache_key, $out, $arg['time_cache'] * 60, true);
	}
		
	return $out;
}

# end file