<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function tagclouds3d_autoload($args = array())
{
	mso_register_widget('tagclouds3d_widget', 'Облако тэгов 3D'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function tagclouds3d_uninstall($args = array())
{	
	mso_delete_option_mask('tagclouds3d_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function tagclouds3d_widget($num = 1) 
{
	$widget = 'tagclouds3d_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
		else $options['header'] = '';
	
	return tagclouds3d_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function tagclouds3d_widget_form($num = 1) 
{
	$widget = 'tagclouds3d_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $options['min_size'] = 25;
		else $options['min_size'] = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $options['max_size'] = 30;
		else $options['max_size'] = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $options['max_num'] = 50;
		else $options['max_num'] = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $options['min_count'] = 0;
		else $options['min_count'] = (int) $options['min_count'];
	
	if ( !isset($options['width']) ) $options['width'] = 150;
		else $options['width'] = (int) $options['width'];
		
	if ( !isset($options['height']) ) $options['height'] = 150;
		else $options['height'] = (int) $options['height'];
			
	if ( !isset($options['speed']) ) $options['speed'] = 220;
		else $options['speed'] = (int) $options['speed'];
		
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
	/* =========== */	
	if ( !isset($options['bgcolor']) ) $options['bgcolor'] = 'FFFFFF';
		else $options['bgcolor'] = $options['bgcolor'];
	
	if ( !isset($options['text_color']) ) $options['text_color'] = '000000';
		else $options['text_color'] = $options['text_color'];
		
	if ( !isset($options['text_color2']) ) $options['text_color2'] = 'CCCCCC';
		else $options['text_color2'] = $options['text_color2'];
	
	if ( !isset($options['hover_color']) ) $options['hover_color'] = '999999';
		else $options['hover_color'] = $options['hover_color'];		
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Скорость вращения'), form_input( array( 'name'=>$widget . 'speed', 'value'=>$options['speed'] ) ), '');
	
	$form .= mso_widget_create_form(t('Ширина (px)'), form_input( array( 'name'=>$widget . 'width', 'value'=>$options['width'] ) ), '');
	
	$form .= mso_widget_create_form(t('Высота (px)'), form_input( array( 'name'=>$widget . 'height', 'value'=>$options['height'] ) ), '');
	
	$form .= mso_widget_create_form(t('Background Color #'), form_input( array( 'name'=>$widget . 'bgcolor', 'value'=>$options['bgcolor'] ) ), t('Все цвета указывайте без символа #'));
	
	$form .= mso_widget_create_form(t('Цвет текста #'), form_input( array( 'name'=>$widget . 'text_color', 'value'=>$options['text_color'] ) ), '');
	
	$form .= mso_widget_create_form(t('Цвет текста 2 #'), form_input( array( 'name'=>$widget . 'text_color2', 'value'=>$options['text_color2'] ) ), '');
	
	$form .= mso_widget_create_form(t('Цвет «hover» #'), form_input( array( 'name'=>$widget . 'hover_color', 'value'=>$options['hover_color'] ) ), '');
	
	$form .= mso_widget_create_form(t('Мин. размер (%)'), form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ), '');
	
	$form .= mso_widget_create_form(t('Макс. размер (%)'), form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ), '');
	
	$form .= mso_widget_create_form(t('Макс. меток'),form_input( array( 'name'=>$widget . 'max_num', 'value'=>$options['max_num'] ) ) , '');
	
	$form .= mso_widget_create_form(t('Миним. меток'), form_input( array( 'name'=>$widget . 'min_count', 'value'=>$options['min_count'] ) ), t('Отображать только метки, которых более указанного количества (0 - без ограничений)'));
	
	$form .= mso_widget_create_form(t('Начало блока'), form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ), '');
	
	$form .= mso_widget_create_form(t('Конец блока'), form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ), '');
	
	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown($widget . 'sort', 
								array( '0'=>t('По количеству записей (обратно)'), '1'=>t('По количеству записей'), 
									   '2'=>t('По алфавиту'), '3'=>t('По алфавиту (обратно)')), $options['sort'] ), '');

	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function tagclouds3d_widget_update($num = 1) 
{
	$widget = 'tagclouds3d_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['min_size'] = mso_widget_get_post($widget . 'min_size');
	$newoptions['max_size'] = mso_widget_get_post($widget . 'max_size');
	$newoptions['max_num'] = mso_widget_get_post($widget . 'max_num');
	$newoptions['min_count'] = mso_widget_get_post($widget . 'min_count');	
	$newoptions['width'] = mso_widget_get_post($widget . 'width');
	$newoptions['height'] = mso_widget_get_post($widget . 'height');
	$newoptions['speed'] = mso_widget_get_post($widget . 'speed');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	/* === */
	$newoptions['bgcolor'] = mso_widget_get_post($widget . 'bgcolor');
	$newoptions['text_color'] = mso_widget_get_post($widget . 'text_color');
	$newoptions['text_color2'] = mso_widget_get_post($widget . 'text_color2');
	$newoptions['hover_color'] = mso_widget_get_post($widget . 'hover_color');
	/* === */
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function tagclouds3d_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'tagclouds3d_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds3d">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $min_size = 90;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 230;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $max_num = 50;
		else $max_num = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $min_count = 0;
		else $min_count = (int) $options['min_count'];
	
	if ( !isset($options['width']) ) $width = 150;
		else $width = (int) $options['width'];
		
	if ( !isset($options['height']) ) $height = 150;
		else $height = (int) $options['height'];
		
	if ( !isset($options['speed']) ) $speed = 220;
		else $speed = (int) $options['speed'];
		
	/* === */
	if ( !isset($options['bgcolor']) ) $bgcolor = 'FFFFFF';
		else $bgcolor = $options['bgcolor'];
	
	if ( !isset($options['text_color']) ) $text_color = '000000';
		else $text_color = $options['text_color'];
		
	if ( !isset($options['text_color2']) ) $text_color2 = 'CCCCCC';
		else $text_color2 = $options['text_color2'];
		
	if ( !isset($options['hover_color']) ) $hover_color = '999999';
		else $hover_color = $options['hover_color'];
					
	/* === */	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
		
	require_once( getinfo('common_dir') . 'meta.php' ); // функции мета
	$tagcloud = mso_get_all_tags_page();
	
	asort($tagcloud);
	$min = reset($tagcloud);
    $max = end($tagcloud);
    
    if ($max == $min) $max++;
    
    // сортировка перед выводом
    if ($sort == 0) arsort($tagcloud); // по количеству обратно
    elseif ($sort == 1) asort($tagcloud); // по количеству 
    elseif ($sort == 2) ksort($tagcloud); // по алфавиту
    elseif ($sort == 3) krsort($tagcloud); // обратно по алфавиту
    else arsort($tagcloud); // по умолчанию
    
    $url 			= getinfo('siteurl') . 'tag/';
    $out 			= '';
	$links  		= '';
	$links_noscript = '';
    $i 				= 0;
    $format 		= '<a href=\'%URL%\' style=\'font-size:%SIZE%px;\'>%TAG%<\/a>';
	$no_script  	= '<a href="%URL%" style="font-size:%SIZE%px;">%TAG%</a>';
    /* ============ */
    
	foreach ($tagcloud as $tag => $count) 
    {
		if ($min_count) 
			if ($count < $min_count) continue;

		$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
		
        $tag_url = urlencode( $tag );
        	
		$f = str_replace(array('%SIZE%', '%URL%', '%TAG%', '%COUNT%'), 
							array($font_size, $url . $tag_url, $tag, $count), $format );
		
		$af = str_replace(  array( '%SIZE%', '%URL%', '%TAG%' ), 
							array( $font_size, $url . $tag_url, $tag ), $no_script );
		
		$links.= $f . ' ';
		$links_noscript.= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
    $out .= '<script src="' . getinfo('plugins_url') . 'tagclouds3d/swfobject.js"></script>
	
			<div id="tag3dcontent">';
	
		$out .= $links_noscript;
	
	$out .= '<script>
				//<![CDATA[
						var rnumber = Math.floor(Math.random()*9999999);
						var widget_so = new SWFObject("' . getinfo('plugins_url') . 'tagclouds3d/tagcloud.swf?r="+rnumber, "tagcloudflash", "'.$width.'", "'.$height.'", "9", "#'.$bgcolor.'");
						widget_so.addParam("wmode", "transparent")
						widget_so.addParam("allowScriptAccess", "always");
						widget_so.addVariable("tcolor", "0x'.$text_color.'");
						widget_so.addVariable("tcolor2", "0x'.$text_color2.'");
						widget_so.addVariable("hicolor", "0x'.$hover_color.'");
						widget_so.addVariable("tspeed", "'.$speed.'");
						widget_so.addVariable("distr", "true");
						widget_so.addVariable("mode", "tags");
						widget_so.addVariable("tagcloud", "<span>';
	$out.=$links . '<\/span>");
				widget_so.write("tag3dcontent");
				//]]>	
				</script>
			</div>';
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

# end file