<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function run_php_autoload($args = array())
{
	mso_hook_add('content_content', 'run_php_custom'); # хук на вывод контента
}

# callback-функция 
function run_php_callback($matches)
{
	$arr1 = array('<p>', '</p>', '<br />', '<br>', '&nbsp;', '&amp;', '&lt;', '&gt;', '&quot;');
	$arr2 = array('',    '',     "\n",     "\n",   ' ',      '&',     '<',    '>',    '"');
	$text = trim( str_replace($arr1, $arr2, $matches[1]) );
	ob_start();
	eval(stripslashes($text));
	$text = ob_get_contents();
	ob_end_clean();
	return $text;
}


# функции плагина
function run_php_custom($text = '')
{
	if (strpos($text, '[php]') !== false) // есть вхождения [php]
	{
		$pattern = '~\[php\](.*?)\[/php\]~si';
		$text = preg_replace_callback($pattern, 'run_php_callback', $text);
	}
	return $text;
}

?>