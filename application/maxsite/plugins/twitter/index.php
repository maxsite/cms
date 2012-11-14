<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function twitter_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('twitter_widget', t('Мой Twitter')); 
}

# функция выполняется при деинсталяции плагина
function twitter_uninstall($args = array())
{	
	mso_delete_option_mask('twitter_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

function twitter_widget($num = 1)
{
	$widget = 'twitter_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	return twitter_widget_custom($options, $num);
}


function twitter_widget_form($num = 1) 
{
	$widget = 'twitter_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Мой Twitter');
	if ( !isset($options['url']) ) $options['url'] = 'http://twitter.com/statuses/user_timeline/14057433.rss';
	if ( !isset($options['count']) ) $options['count'] = '7';
	if ( !isset($options['max_word_description']) ) $options['max_word_description'] = '0';
	if ( !isset($options['format']) ) $options['format'] = '<p><a href="%LINK%">%DATE%</a><br>%TITLE%</p>';
	if ( !isset($options['format_date']) ) $options['format_date'] = 'd/m/Y H:i:s';
	if ( !isset($options['footer']) ) $options['footer'] = '';
	
	// http://d51x.ru/page/modifikacija-plagina-twitter
    if ( !isset($options['show_nick']))  $options['show_nick'] = true;

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . '_header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Адрес RSS'), form_input( array( 'name'=>$widget . '_url', 'value'=>$options['url'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество записей'), form_input( array( 'name'=>$widget . '_count', 'value'=>$options['count'] ) ), '');
	
	$form .= mso_widget_create_form(t('Формат вывода'), form_input( array( 'name'=>$widget . '_format', 'value'=>$options['format'] ) ), '%TITLE% %DATE% %LINK%');
	
	$form .= mso_widget_create_form(t('Формат даты'), form_input( array( 'name'=>$widget . '_format_date', 'value'=>$options['format_date'] ) ), '');
	
	$form .= mso_widget_create_form(t('Количество слов'), form_input( array( 'name'=>$widget . '_max_word_description', 'value'=>$options['max_word_description'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст в конце блока'), form_input( array( 'name'=>$widget . '_footer', 'value'=>$options['footer'] ) ), '');
	
	$form .= mso_widget_create_form('', form_checkbox( array( 'name'=>$widget . '_show_nick', 'value'=> 'show_nick', 'checked' =>  $options['show_nick'])) . ' ' . t('Отображать ник'));
	
	
	return $form;
}


function twitter_widget_update($num = 1) 
{
	$widget = 'twitter_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . '_header');
	$newoptions['url'] = mso_widget_get_post($widget . '_url');
	
	$newoptions['count'] = (int) mso_widget_get_post($widget . '_count');
	if ($newoptions['count'] < 1) $newoptions['count'] = 5;
	
	$newoptions['max_word_description'] = (int) mso_widget_get_post($widget . '_max_word_description');
	if ($newoptions['max_word_description'] < 1) $newoptions['max_word_description'] = 0;	
	
	$newoptions['format'] = mso_widget_get_post($widget . '_format');
	$newoptions['format_date'] = mso_widget_get_post($widget . '_format_date');
	$newoptions['footer'] = mso_widget_get_post($widget . '_footer');
	
	
	$newoptions['show_nick'] =  mso_widget_get_post($widget . '_show_nick');
		
	if ( $options != $newoptions ) mso_add_option($widget, $newoptions, 'plugins' );
}


#
function twitter_widget_custom($arg, $num)
{
	# параметры ленты
	if ( !isset($arg['url']) ) $arg['url'] = false;
	if ( !isset($arg['count']) ) $arg['count'] = 5;
	if ( !isset($arg['format']) ) $arg['format'] = '<p><strong>%DATE%</strong><br>%TITLE% <a href="%LINK%">&gt;&gt;&gt;</a></p>';
	if ( !isset($arg['format_date']) ) $arg['format_date'] = 'd/m/Y H:i:s';
	if ( !isset($arg['max_word_description']) ) $arg['max_word_description'] = false;

	# оформление виджета
	if ( !isset($arg['header']) ) $arg['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . 'Мой Twitter' . mso_get_val('widget_header_end', '</span></h2>');
	
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="twitter">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';
	
	if ( !isset($arg['footer']) ) $arg['footer'] = '';
	if ( !isset($arg['show_nick']) ) $arg['show_nick'] = true;

	$rss = @twitter_go($arg['url'], $arg['count'], $arg['format'], $arg['format_date'], $arg['max_word_description'], $arg['show_nick']);
	if ($rss) 
	{	
		//$rss = str_replace('maxsite:', '<strong>MaxSite:</strong>', $rss);
		return $arg['header'] . $arg['block_start'] . $rss . $arg['footer'] . $arg['block_end'];
	}
}


function twitter_go($url = false, $count = 5, $format = '<p><strong>%DATE%</strong><br>%TITLE% <a href="%LINK%">&gt;&gt;&gt;</a></p>', $format_date = 'd/m/Y H:i:s', $max_word_description = false, $show_nick = true)
{	
	if (!$url) return false;
	
	# проверим кеш, может уже есть в нем все данные
	$cache_key = 'rss/' . 'twitter_go' . $url . $count . $format . $format_date . (int) $max_word_description;
	$k = mso_get_cache($cache_key, true);
	if ($k) return $k; // да есть в кэше
	
	if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', 600); // время кэширования MAGPIE
	require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');

	$rss = fetch_rss($url);
	$rss = array_slice($rss->items, 0, $count);

	$out = '';
	foreach ( $rss as $item ) 
	{ 
		$out .= $format;
		
		if ( $show_nick )
			$item['title'] = preg_replace('|(\S+): (.*)|si', '<strong>\\1:</strong> \\2', $item['title']); // выделим ник: 
		else
			$item['title'] = preg_replace('|(\S+): (.*)|si', '\\2', $item['title']);
		
		// подсветим ссылки
		$item['title'] = preg_replace('|(http:\/\/)(\S+)|si', '<a rel="nofollow" href="http://\\2" target="_blank">\\2</a>', $item['title']);
		
		$out = str_replace('%TITLE%', $item['title'], $out); // [title] = [description] = [summary]
		
		if ($max_word_description)
		{
			$item['description'] = mso_str_word($item['description'], $max_word_description) . '...';
		}
		
		$item['description'] = preg_replace('|(\S+): (.*)|si', '<strong>\\1:</strong> \\2', $item['description']);
		$item['description'] = preg_replace('|(http:\/\/)(\S+)|si', '<a rel="nofollow" href="http://\\2" target="_blank">\\2</a>', $item['description']);
		
		$out = str_replace('%DESCRIPTION%', $item['description'], $out); // [title] = [description] = [summary]
		$out = str_replace('%DATE%', date($format_date, (int) $item['date_timestamp']), $out); // [pubdate]
		$out = str_replace('%LINK%', $item['link'], $out); // [link] = [guid]
	}
	
	mso_add_cache($cache_key, $out, 600, true);

	return $out;
}

# end file