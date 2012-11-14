<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function picasa_autoload($args = array())
{
	mso_register_widget('picasa_widget', t('Веб-альбомы Picasa')); 
}

# функция выполняется при деинстяляции плагина
function picasa_uninstall($args = array())
{	
	mso_delete_option_mask('picasa_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

function picasa_widget($num = 1)
{
	$widget = 'picasa_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';

	return picasa_widget_custom($options, $num);
}

# форма настройки виджета
function picasa_widget_form($num = 1) 
{
	$widget = 'picasa_widget_' . $num;
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Мой веб-альбом Picasa');
	if ( !isset($options['url']) ) $options['url'] = 'Логин пользователя';
	if ( !isset($options['show_type']) ) $options['show_type'] = 1;
	if ( !isset($options['albums_count']) ) $options['albums_count'] = 10;
		else $options['albums_count'] = (int) $options['albums_count'];
	if ( !isset($options['album_name']) ) $options['album_name'] = 'название_альбома';
	if ( !isset($options['img_size']) ) $options['img_size'] = 32;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	
	$form .= mso_widget_create_form(t('Логин пользователя'), form_input( array( 'name'=>$widget . 'url', 'value'=>$options['url'] ) ), 'http://picasaweb.google.com/<b>' . t('логин') . '</b>');
	
	$form .= mso_widget_create_form(t('Показывать'), form_dropdown($widget . 'show_type', 
								array( '1'=>t('Только названия альбомов'),
										'2'=>t('Обложки альбомов'),
										'3'=>t('Фотографии из альбома')), 
								$options['show_type'] ), '');
	
	$form .= mso_widget_create_form(t('Количество'), form_input( array( 'name'=>$widget . 'albums_count', 'value'=>$options['albums_count'] ) ), t('Количество выводимых названий альбомов/обложек/фотографий'));
	
	$form .= mso_widget_create_form(t('Название альбома'), form_input( array( 'name'=>$widget . 'album_name', 'value'=>$options['album_name'] ) ), t('Для «Фотографии из альбома» http://picasaweb.google.com/логин/<b>название_альбома</b>'));
	
	$form .= mso_widget_create_form(t('Размер изображений'), form_dropdown($widget . 'img_size', 
								array( '32'=>t('32px'), 
										'48'=>t('48px'), 
										'64'=>t('64px'), 
										'72'=>t('72px'),
										'144'=>t('144px'),
										'160'=>t('160px'),
										'200'=>t('200px — только для фотографий'),
										'288'=>t('288px — только для фотографий'),
										'320'=>t('320px — только для фотографий'),
										'400'=>t('400px — только для фотографий'),
										'512'=>t('512px — только для фотографий')), 
								$options['img_size'] ), '');
	
	return $form;
}

# получаем/обновляем опции
function picasa_widget_update($num = 1) 
{
	$widget = 'picasa_widget_' . $num;
	
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['url'] = mso_widget_get_post($widget . 'url');
	$newoptions['show_type'] = mso_widget_get_post($widget . 'show_type');
	
	$newoptions['albums_count'] = (int) mso_widget_get_post($widget . 'albums_count');
	if ($newoptions['albums_count'] < 1) $newoptions['albums_count'] = 0;
	
	$newoptions['album_name'] = mso_widget_get_post($widget . 'album_name');
	$newoptions['img_size'] = mso_widget_get_post($widget . 'img_size');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

function picasa_widget_custom($arg, $num)
{
	# параметры ленты
	if ( !isset($arg['url']) ) $arg['url'] = false;
	if ( !isset($arg['show_type']) ) $arg['show_type'] = 1;
	if ( !isset($arg['albums_count']) ) (int) $arg['albums_count'] = 10;
	if ( !isset($arg['album_name']) ) $arg['album_name'] = 32;
	if ( !isset($arg['img_size']) ) $arg['img_size'] = 32;

	# оформление виджета
	if ( !isset($arg['header']) ) 
		$arg['header'] = '<h2 class="box"><span>' . t('Мой веб-альбом Picasa') . '</span></h2>';
		
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="picasa">';
	
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';

	$rss = @picasa_go($arg['url'], $arg['show_type'], $arg['albums_count'], $arg['album_name'], $arg['img_size']);
	if ($rss) 
	{	
		return $arg['header'] . $arg['block_start'] . $rss . $arg['block_end'];
	}
}

function picasa_go($url = false, $show_type = 1, $albums_count = 10, $album_name = false, $img_size = 32)
{	
	if (!$url) return false;
	
	# проверим кеш, может уже есть в нем все данные
	$cache_key = 'rss/' . 'picasa_' . $url . $show_type . (int) $albums_count . $album_name . $img_size;
	
	$k = mso_get_cache($cache_key, true);
	
	if ($k) return $k; // да есть в кэше
	
	if (!defined('MAGPIE_CACHE_AGE'))	define('MAGPIE_CACHE_AGE', 3600); // время кэширования MAGPIE
	
	require_once(getinfo('common_dir') . 'magpierss/rss_fetch.inc');
	
	if ($show_type == 1) 
	{
	
		$rss = fetch_rss("http://picasaweb.google.com/data/feed/base/user/".$url."?alt=rss&kind=album&hl=ru&access=public");
		
		$rss = array_slice($rss->items, 0, $albums_count);
		
		$result = '';
		
		foreach ( $rss as $item ) 
		{ 	
			$title = $item['title'];
			$title = str_replace("'","",$title);
			$title = str_replace('"',"",$title);
			$result .= "<p><a href='".$item['link']."' target='_blank' rel='nofollow'>".$title."</a></p>";	
		}
	};
	
	if ($show_type == 2) 
	{
		
		$rss = fetch_rss("http://picasaweb.google.com/data/feed/base/user/".$url."?alt=rss&kind=album&hl=ru&access=public");
		
		$rss = array_slice($rss->items, 0, $albums_count);
		
		$result = '';
		
		foreach ( $rss as $item ) 
		{ 	
			$title = $item['title'];
			$title = str_replace("'","",$title);
			$title = str_replace('"',"",$title);
			preg_match('/.*src="(.*?)".*/',$item['description'],$img_src);
			$path = $img_src[1];
			$path = str_replace("s160-","s".$img_size."-",$path);
			$result .= "<a href='".$item['link']."' target='_blank' rel='nofollow'><img src='".$path."' class='picasa-photo' alt='' title='".$title."' width='".$img_size."' height='".$img_size."'></a>";	
		}
	};
	
	if ($show_type == 3) 
	{
		
		$rss = fetch_rss("http://picasaweb.google.com/data/feed/base/user/" . $url . "/album/" . $album_name ."?alt=rss&kind=photo&hl=ru&access=public");
		
		$rss = array_slice($rss->items, 0, $albums_count);
		
		$result = '';
		
		foreach ( $rss as $item ) 
		{ 	
			$title = $item['title'];
			$title = str_replace("'", "", $title);
			$title = str_replace('"', "", $title);
			preg_match('/.*src="(.*?)".*/', $item['description'], $img_src);
			$path = $img_src[1];
			$path = str_replace("s288", "s" . $img_size,$path);
			$result .= "<a href='" . $item['link'] . "' target='_blank' rel='nofollow'><img src='" . $path . "' class='picasa-photo' alt='' title='" . $title . "'></a>";	
		}
	};
	
	mso_add_cache($cache_key, $result, 300, true);
	
	return $result;
}


# end file