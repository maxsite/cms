<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function links_autoload()
{
	mso_register_widget('links_widget', t('Ссылки')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function links_uninstall($args = array())
{	
	mso_delete_option_mask('links_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function links_widget($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return links_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function links_widget_form($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['links']) ) $options['links'] = '';
	if ( !isset($options['screenshot']) ) $options['screenshot'] = 0;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Ссылки'), form_textarea( array( 'name'=>$widget . 'links', 'value'=>$options['links'] ) ), t('Указывайте по одной ссылке в каждом абзаце в формате:<br><strong>http://links/ | название | описание | noindex | _blank</strong><br><strong>noindex</strong> - обрамить ссылку в nofollow, если не нужно - указать пробел<br><strong>_blank</strong> - открыть ссылку в новом окне, если не нужно - указать пробел'));
	
	$form .= mso_widget_create_form(t('Отображать'), form_dropdown( $widget . 'screenshot', array( 
		'0'=>t('Обычным списком'), 
		'1'=>t('Использовать скриншот сайта 120x83px (бэби)'), 
		'2'=>t('Использовать скриншот сайта 202x139px (маленький)'), 
		'3'=>t('Использовать скриншот сайта 305x210px (средний)'), 
		'4'=>t('Использовать скриншот сайта 400x275px (большой)')), 
		$options['screenshot']), t('Скриншоты создаются с помощью <a href="http://www.webmorda.kz/" target="_blank">Мордашка твоего сайта</a>'));
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function links_widget_update($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['links'] = mso_widget_get_post($widget . 'links');
	$newoptions['screenshot'] = mso_widget_get_post($widget . 'screenshot');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function links_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'links_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['screenshot']) ) $options['screenshot'] = 0;
	
	if ( isset($options['links']) ) 
	{
		$links = explode("\n", $options['links']); // разбиваем по строкам
		
		foreach ($links as $row)
		{
			$ar_link = explode('|', $row); // разбиваем по |
			
			// всего должно быть 5 элементов
			if ( isset($ar_link[0]) and trim($ar_link[0]) ) // если есть первый элемент
			{
				$href = trim($ar_link[0]); // адрес
				
				if ( $href and isset($ar_link[1]) and trim($ar_link[1]) ) // если есть второй элемент - название
				{
					$title = trim($ar_link[1]); // название
					
					if ( isset($ar_link[2]) and trim($ar_link[2]) )// если есть описание 
					{	
						$descr = '<div>' . trim($ar_link[2]) . '</div>';
					}	
					else 
					{
						$descr = '';
					}
					
					if ( isset($ar_link[3]) and trim($ar_link[3]) )// если есть noindex 
					{	
						//$noindex1 = '<noindex>'; 
						//$noindex2 = '</noindex>';
						
						$noindex1 = ''; 
						$noindex2 = '';
						
						$nofollow = ' rel="nofollow"';
					}	
					else 
					{
						$noindex1 = $noindex2 = $nofollow = '';
					}
					
					if ( isset($ar_link[4]) and trim($ar_link[4]) ) $blank = ' target="_blank"'; // если есть _blank
						else $blank = '';
					
					if (!$options['screenshot'])
					{
						// обычный вывод списком
						$out .= NR . '<li>' . $noindex1 . '<a href="' . $href . '" title="' . htmlspecialchars($title) . '"' . $nofollow . $blank . '>' 
							. $title . '</a>' . $descr . $noindex2 . '</li>';
					}
					else
					{
						// скриншоты
						$href_w = str_replace('http://', '', $href);
						
						if ($options['screenshot'] == 1)
						{
							$width = '120';
							$height = '83';
							$s = 'm';
						}
						elseif ($options['screenshot'] == 2)
						{
							$width = '202';
							$height = '139';						
							$s = 's';
						}
						elseif ($options['screenshot'] == 3)
						{
							$width = '305';
							$height = '210';						
							$s = 'n';
						}						
						else
						{
							$width = '400';
							$height = '275';						
							$s = 'b';
						}			
						
						
						$out .= NR . '<p>' . $noindex1 . '<a href="' . $href . '" title="' . htmlspecialchars($title) . '"' . $nofollow . $blank . '>' 
							. '<img src="http://webmorda.kz/site2img/?s=' . $s . '&u=' . $href_w . '" alt="' 
							. htmlspecialchars($title) . '" title="' . $title 
							. '" width="' . $width . '" height="' . $height . '"></a>' . $descr . '' . $noindex2 . '</p>';
							
						/*
						http://www.webmorda.kz/api.html
						http://webmorda.kz/site2img/?u={1}&s={2}&q={3}&r={4}
						*/
					}
				}
			}
		}
	}
	
	if ($out) 
	{
		if (!$options['screenshot'])
		{
			// обычным списком
			$out = $options['header'] . NR . '<ul class="is_link links">' . $out . NR . '</ul>' .NR ;
		}
		else
		{
			// скриншоты
			$out = $options['header'] . NR . '<div class="links">' . $out . '</div><div class="break"></div>' . NR;
		}
	}
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

# end file