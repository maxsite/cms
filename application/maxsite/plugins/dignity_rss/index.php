<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Александр Шиллинг
 * (c) http://maxsite.thedignity.biz
 */

# функция автоподключения плагина
function dignity_rss_autoload($args = array())
{
	mso_register_widget('dignity_rss_widget', t('RSS подписка')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_rss_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_rss_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_rss_widget($num = 1) 
{
	$widget = 'dignity_rss_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	

	if (isset($options['textdo']) ) $options['textdo'] = '<p>' . $options['textdo'] . '</p>';
	else $options['textdo'] = '';
	
	if (isset($options['feed_url']) ) $options['feed_url'] = $options['feed_url'];
	else $options['feed_url'] = getinfo('rss_url');

	if (isset($options['google_text']) ) $options['google_text'] = $options['google_text'];
	else $options['google_text'] = t('Читать RSS через Google');

	if (isset($options['yandex_text']) ) $options['yandex_text'] = $options['yandex_text'];
	else $options['yandex_text'] = t('Читать RSS через Яндекс');

	if (isset($options['rss_text']) ) $options['rss_text'] = $options['rss_text'];
	else $options['rss_text'] = t('RSS лента');

	if (isset($options['rss_to_email']) ) $options['rss_to_email'] = $options['rss_to_email'];
	else $options['rss_to_email'] = t('RSS-лента на E-Mail');

	if (isset($options['textposle']) ) $options['textposle'] = '<p>' . $options['textposle'] . '</p>';
	else $options['textposle'] = '';
	
	return dignity_rss_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_rss_widget_form($num = 1) 
{

	$widget = 'dignity_rss_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Подписка на новости');
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['feed_url']) ) $options['feed_url'] = getinfo('rss_url');
	if ( !isset($options['google_text']) ) $options['google_text'] = t('Читать блог через Google');
	if ( !isset($options['yandex_text']) ) $options['yandex_text'] = t('Читать блог через Яндекс');
	if ( !isset($options['rss_text']) ) $options['rss_text'] = t('RSS лента');
	if ( !isset($options['rss_to_email']) ) $options['rss_to_email'] = t('Получать RSS-ленту на почту');
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст вначале'), form_textarea( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ), '');
	
	$form .= mso_widget_create_form(t('Адрес RSS-Feed'), form_input( array( 'name'=>$widget . 'feed_url', 'value'=>$options['feed_url'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст для Google'), form_input( array( 'name'=>$widget . 'google_text', 'value'=>$options['google_text'] )) , '');
	
	$form .= mso_widget_create_form(t('Текст для Яндекс'), form_input( array( 'name'=>$widget . 'yandex_text', 'value'=>$options['yandex_text'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст RSS ленты'), form_input( array( 'name'=>$widget . 'rss_text', 'value'=>$options['rss_text'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст RSS-лента на почту'), form_input( array( 'name'=>$widget . 'rss_to_email', 'value'=>$options['rss_to_email'] ) ), '');
	
	$form .= mso_widget_create_form(t('Текст в конце'), form_textarea( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ), '');
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_rss_widget_update($num = 1) 
{
	$widget = 'dignity_rss_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['feed_url'] = mso_widget_get_post($widget . 'feed_url');
	$newoptions['google_text'] = mso_widget_get_post($widget . 'google_text');
	$newoptions['yandex_text'] = mso_widget_get_post($widget . 'yandex_text');
	$newoptions['rss_text'] = mso_widget_get_post($widget . 'rss_text');
	$newoptions['rss_to_email'] = mso_widget_get_post($widget . 'rss_to_email');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# функции плагина

function dignity_rss_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$textdo = $options['textdo'];
	$textposle = $options['textposle'];
	$feed_url = $options['feed_url'];
	$google_text = $options['google_text'];
	$yandex_text = $options['yandex_text'];
	$rss_text = $options['rss_text'];
	$rss_to_email = $options['rss_to_email'];
	$path = getinfo('plugins_url') . 'dignity_rss/img/'; # путь к картинкам
	$rss_google = 'http://fusion.google.com/add?feedurl=' . $feed_url;
	$rss_yandex = 'http://lenta.yandex.ru/settings.xml?name=feed&amp;url=' . $feed_url;
	$rss_google_read = '<p><a href="' .$rss_google  . '" rel="nofollow"><img src="' . $path . 'google.png"></a> <a href="' . $rss_google . '" rel="nofollow">' . $google_text . '</a></p>';
	$rss_yandex_read = '<p><a href="' .$rss_yandex  . '" rel="nofollow"><img src="' . $path . 'yandex.png"></a> <a href="' . $rss_yandex . '" rel="nofollow">' . $yandex_text . '</a></p>';
	$rss_mail = '<p><a href="http://www.rss2email.ru?rss=' . $feed_url . '" title="' . $rss_to_email . '" rel="nofollow"><img src="' . $path . 'email.png"></a> <a href="http://www.rss2email.ru?rss=' . $feed_url . '" title="' . $rss_to_email . '" rel="nofollow">' . $rss_to_email . '</a></p>';
	$rss_f = '<p><a href="' . $feed_url . '"><img src="' . $path . 'rss.png"></a>' . ' <a href="' . $feed_url . '">' . $rss_text . '</a></p>';
	
	return $header . $textdo . $rss_f . $rss_google_read . $rss_yandex_read . $rss_mail . $textposle;
}

# end file