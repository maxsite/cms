<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) MaxSite CMS
 * http://max-3000.com/
 *
 * Функции для работы с шаблоном
 * версия 2017-08-25
 */

# типовой вывод секции HEAD
# можно использовать в header.php
function my_default_head_section()
{
	global $page;
	
	// атирибуты <HTML>
	$html_attr = mso_get_val('head_section_html_add');
	$html_attr = mso_hook('html_attr', $html_attr);
	$html_attr = $html_attr ? ' ' . $html_attr : '';
	
	echo 
'<!DOCTYPE HTML>
<html' . $html_attr . '><head>' . mso_hook('head_start') . '
<meta charset="UTF-8">
<title>' . mso_head_meta('title') . '</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="generator" content="MaxSite CMS">
<meta name="description" content="' . mso_head_meta('description') . '">
<meta name="keywords" content="' . mso_head_meta('keywords') . '">
<link rel="shortcut icon" href="' . getinfo('uploads_url') . 'favicons/' . mso_get_option('default_favicon', 'templates', 'favicon1.png') . '" type="image/x-icon">
';
	
	if (mso_get_option('default_canonical', 'templates', 0)) echo mso_link_rel('canonical');

	echo mso_rss();
	
	if ($fn = mso_fe('custom/head-start.php')) require($fn);

	// autoload файлов
	if ($autoload_css = mso_get_path_files(getinfo('template_dir') . 'assets/css/', getinfo('template_url') . 'assets/css/', true, array('css')))
	{
		foreach($autoload_css as $fn_css)
		{
			// pr($fn_css);
			echo mso_load_style($fn_css);
		}
	}
	
	my_out_component_css();
	mso_hook('head_css');
	my_default_out_profiles();
	
	// своя версия jQuery, если нужно
	if ($j = mso_get_val('jquery_url', false))
	{
		echo mso_load_script($j);
	}
	else
	{
		if (mso_fe('assets/js/jquery.min.js')) 
			mso_add_file('assets/js/jquery.min.js');
		else 
			echo mso_load_jquery();
	}
	
	mso_hook('head');

	// autoload js-файлов
	if ($autoload_js = mso_get_path_files(getinfo('template_dir') . 'assets/js/autoload/', getinfo('template_url') . 'assets/js/autoload/', true, array('js')))
	{
		foreach($autoload_js as $fn_js)
		{
			echo '<script src="' . $fn_js . '"></script>' . NR;
		}
	}
	
	my_out_component_js();
	
	if ($fn = mso_fe('custom/head.php')) require($fn);
	if ($fn = mso_page_foreach('head')) require($fn);
	if (function_exists('ushka')) echo ushka('head');
	
	if (mso_fe('assets/js/my.js')) 
		mso_add_file('assets/js/my.js');
	
	if ($my_style = mso_get_option('my_style', getinfo('template'), '')) 
		echo NR . '<!-- custom css-my_style -->' . NR . '<style>' . NR . $my_style . '</style>';
	
	mso_hook('head_end');

	if (function_exists('ushka')) echo ushka('google_analytics_top');
	
	echo NR . '</head>';
} 
 
 
# вывод подключенных css-профилей
function my_default_out_profiles($path = 'assets/css/profiles/')
{
	global $page;
	
	if ($default_profiles = mso_get_option('default_profiles', getinfo('template'), array())) // есть какие-то профили оформления
	{
		$css_out = '';
		
		// theme-профили подключаются как link rel="stylesheet
		foreach($default_profiles as $css_file)
		{
			$fn = $path . $css_file;
			
			$link = strpos($css_file, 'theme-'); // это theme- ?
			
			if ($link !== false and $link === 0)
				mso_add_file($fn); // подключаем внешими стилями
			else
				// получение и обработка CSS из файла
				$css_out .= my_out_css_file($fn, false, false); 
		}
		
		if ($css_out) 
			echo '<style>' . $css_out . '</style>' . NR;
	}
	
	// здесь же выводим css-профиль записи
	// он задан через метаполе
	
	if (is_type('page') and isset($page) and $page)
	{
		if ($page_css_profiles = mso_page_meta_value('page_css_profiles', $page['page_meta']))
		{
			$fn = $path . $page_css_profiles;
		
			$link = strpos($page_css_profiles, 'theme-'); // это theme- ?
			
			if ($link !== false and $link === 0)
			{
				mso_add_file($fn); // подключаем внешими стилями
			}
			else
			{
				// получение и обработка CSS из файла
				if ($css_out = my_out_css_file($fn, false, false)) 
					echo NR . '<style>' . $css_out . '</style>' . NR;
			}
		}
	}
}


# функция возвращает полный путь к файлу компонента для указанной опции
# $option - опция
# $def_component - компонент по умолчанию
# пример использования
# if ($fn = my_get_component_fn('header_component2', 'menu')) require($fn);
function my_get_component_fn($option = '', $def_component = '')
{
	if ($dir = mso_get_option($option, getinfo('template'), $def_component)) // получение опции
	{
		$fn = getinfo('template_dir') . 'components/' . $dir . '/' . $dir . '.php';
		
		// проверяем если файл в наличии
		if (file_exists($fn)) return $fn;
	}
	
	return false; // ничего нет
}


# функция подключает файлы css-style установленных компонентов и выводит их содержимое в едином блоке <style>
# использовать в head 
# $component_options - названия опций, которыми определяются компоненты в шаблоне
# css-файл компонента находится в общем css-каталоге шаблона с именем компонента, например menu.php и menu.css
function my_out_component_css($component_options = array('header_component1', 'header_component2', 'header_component3', 'header_component4', 'header_component5', 'footer_component1', 'footer_component2', 'footer_component3', 'footer_component4', 'footer_component5'))
{
	// проходимся по всем заданным опциям
	foreach($component_options as $option)
	{
		// и если они определены
		if ($dir = mso_get_option($option, getinfo('template'), false))
		{
			$fn = 'components/' . $dir . '/style.css';
			mso_add_file($fn); // подключаем внешими стилями
		}
	}
}

# аналогично выводятся js-файлы компонент.js
function my_out_component_js($component_options = array('header_component1', 'header_component2', 'header_component3', 'header_component4', 'header_component5', 'footer_component1', 'footer_component2', 'footer_component3', 'footer_component4', 'footer_component5'))
{
	// проходимся по всем заданным опциям
	foreach($component_options as $option)
	{
		// и если они определены
		if ($dir = mso_get_option($option, getinfo('template'), false))
		{
			$fn = 'components/' . $dir . '/' . $dir . '.js';
			mso_add_file($fn);
		}
	}
}

# получает css из указанного файла
# в css-файле можно использовать php
# осуществляется сжатие css
# автозамена [TEMPLATE_URL] на url-шаблона
# функция возвращает только стили, без обрамляющего <style>
# Если <style> нужны, то $tag_style = true
# Если нужен сразу вывод в браузер, то $echo = true
function my_out_css_file($fn, $tag_style = true, $echo = true)
{
	
	$fn = getinfo('template_dir') . $fn;
	
	$out = '';
	if (file_exists($fn)) // проверяем если ли файл в наличии
	{
		if ($r = @file_get_contents($fn)) $out .= $r . NR; // получаем содержимое
	
		if ($out) 
		{
			ob_start();
			eval( '?>' . stripslashes($out) . '<?php ');
			$out = ob_get_contents();
			ob_end_clean();
			
			$out = str_replace('[TEMPLATE_URL]', getinfo('template_url'), $out);
			$out = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $out);
			$out = str_replace(array('; ', ' {', ': ', ', '), array(';', '{', ':', ','), $out);
		}
	
		if ($tag_style) $out = NR . '<style>' . $out . '</style>' . NR;
		if ($echo) echo $out;
	}
	
	return $out;
}


// функции из LPF
// http://lpf.maxsite.com.ua/

/**
*  сжатие HTML-текста путём удаления лишних пробелов
*  
*  @param $text 
*  
*  @return string
*/
function my_compress_text($text)
{
	# защищенный текст
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'my_clean_pre_do', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', 'my_clean_pre_do', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', 'my_clean_html_script', $text);
	
	$text = str_replace(array("\r\n", "\r"), "\n", $text);
	$text = str_replace("\t", ' ', $text);
	$text = str_replace("\n   ", "\n", $text);
	$text = str_replace("\n  ", "\n", $text);
	$text = str_replace("\n ", "\n", $text);
	$text = str_replace("\n", '', $text);
	$text = str_replace('   ', ' ', $text);
	$text = str_replace('  ', ' ', $text);
	
	// специфичные замены
	$text = str_replace('<!---->', '', $text);
	$text = str_replace('>    <', '><', $text);
	$text = str_replace('>   <', '><', $text);
	$text = str_replace('>  <', '><', $text);
	
	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'my_clean_html_posle', $text);
	
	return $text;
}

/**
*  pre, которое загоняется в [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function my_clean_pre_do($matches)
{
	$text = trim($matches[2]);

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br>", "\n", $text);
	$text = str_replace("<br />", "<br>", $text);
	$text = str_replace("<br/>", "<br>", $text);
	$text = str_replace("<br>", "\n", $text);
	
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	$text = str_replace('&lt;pre', '<pre', $text);
	$text = str_replace('&lt;/pre', '</pre', $text);
	$text = str_replace('pre&gt;', 'pre>', $text);

	$text = $matches[1] . "\n" . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];

	return $text;
}

/**
*  script, который загоняется в [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function my_clean_html_script($matches)
{
	$text = trim($matches[2]);
	$text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]'. $matches[3];
	
	return $text;
}

/**
*  декодирование из [html_base64]
*  
*  @param $matches 
*  
*  @return string
*/
function my_clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}


/**
*  заменяет в тексте все вхождения http:// и https:// на // 
*  
*  @param $text текст
*  
*  @return string
*/
function my_remove_protocol($text)
{
	# защищенный текст
	$text = preg_replace_callback('!(<pre.*?>)(.*?)(</pre>)!is', 'my_clean_pre_do', $text);
	$text = preg_replace_callback('!(<code.*?>)(.*?)(</code>)!is', 'my_clean_pre_do', $text);
	$text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', 'my_clean_html_script', $text);
	$text = preg_replace_callback('!(<textarea.*?>)(.*?)(</textarea>)!is', 'my_clean_html_script', $text);
	
	$text = str_replace('https://', '//', $text);
	$text = str_replace('http://', '//', $text);
	
	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'my_clean_html_posle', $text);
		
	return $text;
}

# end of file