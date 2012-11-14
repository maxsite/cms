<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function faq_autoload($args = array())
{
	mso_hook_add( 'content', 'faq_custom'); # хук на вывод контента
}

# callback-функция вычленения заголовков
function faq_custom_headers_callback($matches)
{
	return '<li><a href="#' . mso_slug($matches[1]) . '">' . $matches[1] . '</a></li>';
}

# callback-функция вычленения текстов
function faq_custom_text_callback($matches)
{
	return '<a id="' . mso_slug($matches[1]) . '"></a><h3>' . trim($matches[1]) . '</h3>'
			. '<div class="faq-text">' . trim($matches[2]) . '<a href="#faq-top" class="to-faq-top">' . t('К списку') . '</a></div>';
}

function faq_custom_faqs_callback($matches)
{
	$text = $matches[1]; // все faq
	
	$pattern = '~\[faq=(.*?)\](.*?)\[/faq\]~si';
	$text1 = '<ul>' . preg_replace_callback($pattern, 'faq_custom_headers_callback', $text) . '</ul>';
	
	$text2 = preg_replace_callback($pattern, 'faq_custom_text_callback', $text);
	
	$text = '<div class="faq-list">' . $text1 . '</div><div class="faq-out">' . $text2 . '</div>';
	
	return '<div class="faq"><a id="faq-top"></a>' . $text . '</div><!--div class="faq"-->';
}

# функции плагина
function faq_custom($text = '')
{
	if (strpos($text, '[faqs]') !== false) // есть вхождения [faqs]
	{
		$text = preg_replace_callback('~\[faqs\](.*?)\[/faqs\]~si', 'faq_custom_faqs_callback', $text);
	}
	return $text;
}

# end file