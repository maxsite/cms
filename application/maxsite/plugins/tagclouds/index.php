<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function tagclouds_autoload($args = array())
{
	mso_register_widget('tagclouds_widget', t('Облако тэгов/меток')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function tagclouds_uninstall($args = array())
{	
	mso_delete_option_mask('tagclouds_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function tagclouds_widget($num = 1) 
{
	$widget = 'tagclouds_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return tagclouds_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function tagclouds_widget_form($num = 1) 
{
	$widget = 'tagclouds_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $options['min_size'] = 90;
		else $options['min_size'] = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $options['max_size'] = 230;
		else $options['max_size'] = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $options['max_num'] = 50;
		else $options['max_num'] = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $options['min_count'] = 0;
		else $options['min_count'] = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат'), form_textarea( array( 'name'=>$widget . 'format', 'value'=>$options['format'], 'rows' => 3) ), '%SIZE% %URL% %TAG% %COUNT%');

	$form .= mso_widget_create_form(t('Мин. размер (%)'), form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ), '');

	$form .= mso_widget_create_form(t('Макс. размер (%)'), form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ), '');

	$form .= mso_widget_create_form(t('Макс. меток'), form_input( array( 'name'=>$widget . 'max_num', 'value'=>$options['max_num'] ) ), '');

	$form .= mso_widget_create_form(t('Миним. меток'), form_input( array( 'name'=>$widget . 'min_count', 'value'=>$options['min_count'] ) ), t('Отображать только метки, которых более указанного количества. (0 - без ограничений)'));
	
	$form .= mso_widget_create_form(t('Начало блока'), form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ), '');
	
	$form .= mso_widget_create_form(t('Конец блока'), form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ), '');
	
	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown($widget . 'sort', 
								array( '0'=>t('По количеству записей (обратно)'), 
										'1'=>t('По количеству записей'), 
										'2'=>t('По алфавиту'), 
										'3'=>t('По алфавиту (обратно)')), 
								$options['sort'] ), '');
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function tagclouds_widget_update($num = 1) 
{
	$widget = 'tagclouds_widget_' . $num; // имя для опций = виджет + номер
	
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
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина
function tagclouds_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'tagclouds_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $min_size = 90;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 230;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $max_num = 50;
		else $max_num = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $min_count = 0;
		else $min_count = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
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
    
    $url = getinfo('siteurl') . 'tag/';
    $out = '';
    $i = 0;
    foreach ($tagcloud as $tag => $count) 
    {
		if ($min_count) 
			if ($count < $min_count) continue;

		$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
		$af = str_replace(array('%SIZE%', '%URL%', '%TAG%', '%COUNT%'), 
							array($font_size, $url . urlencode($tag), $tag, $count), $options['format']);
		
		// альтернативный синтаксис с []
		$af = str_replace(array('[SIZE]', '[URL]', '[TAG]', '[COUNT]'), 
							array($font_size, $url . urlencode($tag), $tag, $count), $af);

		$out .= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

# end file