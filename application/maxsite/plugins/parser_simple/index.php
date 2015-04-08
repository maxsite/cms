<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * http://lpf.maxsite.com.ua/autotag-single
 */

function parser_simple_autoload()
{
	mso_hook_add('parser_register', 'parser_simple_register');  // парсер Simple
}

function parser_simple_register($parsers = array())
{
	$parsers['Simple'] = array( // имя парсера
		'content' => 'parser_simple_content', // функция обработчик на хуке content
		'content_post_edit' => 'parser_simple_post_edit', // функция перед отправкой в БД
	);
	
	return $parsers;
}


function parser_simple_content($text = '')
{
	$text = autotag_simple($text);
	
	return $text;
}


function parser_simple_post_edit($text = '')
{
	// глюк FireFox исправлем замену абсолютного пути src на абсолютный
	$text = str_replace('src="../../', 'src="' . getinfo('site_url'), $text);
	$text = str_replace('src="../', 'src="' . getinfo('site_url'), $text);

	return $text;
}


function autotag_simple($text)
{
	$text = str_replace(array("\r\n", "\r"), "\n", $text); // win-dos
	
	$text = "\n" . $text . "\n";
	
	$text = preg_replace('!\n\s*_\s(.*?)\n!', "\n\n<p>$1</p>\n", $text);
	$text = preg_replace('!\n\s*_\s(.*?)\n!', "\n\n<p>$1</p>\n", $text);
	
	$text = preg_replace('!\n\s*_\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n\n<p class=\"$1\" style=\"$2\">$3</p>\n", $text);
	$text = preg_replace('!\n\s*_\((.*?)\)\s(.*?)\n!', "\n\n<p class=\"$1\">$2</p>\n", $text);
	
	$text = preg_replace('!\n\s*h1\s(.*?)\n!', "\n<h1>$1</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\s(.*?)\n!', "\n<h2>$1</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\s(.*?)\n!', "\n<h3>$1</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\s(.*?)\n!', "\n<h4>$1</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\s(.*?)\n!', "\n<h5>$1</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\s(.*?)\n!', "\n<h6>$1</h6>\n", $text);
	
	$text = preg_replace('!\n\s*h1\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h1 class=\"$1\" style=\"$2\">$3</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h2 class=\"$1\" style=\"$2\">$3</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h3 class=\"$1\" style=\"$2\">$3</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h4 class=\"$1\" style=\"$2\">$3</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h5 class=\"$1\" style=\"$2\">$3</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\((.*?)\)\((.*?)\)\s(.*?)\n!', "\n<h6 class=\"$1\" style=\"$2\">$3</h6>\n", $text);
	
	$text = preg_replace('!\n\s*h1\((.*?)\)\s(.*?)\n!', "\n<h1 class=\"$1\">$2</h1>\n", $text);
	$text = preg_replace('!\n\s*h2\((.*?)\)\s(.*?)\n!', "\n<h2 class=\"$1\">$2</h2>\n", $text);
	$text = preg_replace('!\n\s*h3\((.*?)\)\s(.*?)\n!', "\n<h3 class=\"$1\">$2</h3>\n", $text);
	$text = preg_replace('!\n\s*h4\((.*?)\)\s(.*?)\n!', "\n<h4 class=\"$1\">$2</h4>\n", $text);
	$text = preg_replace('!\n\s*h5\((.*?)\)\s(.*?)\n!', "\n<h5 class=\"$1\">$2</h5>\n", $text);
	$text = preg_replace('!\n\s*h6\((.*?)\)\s(.*?)\n!', "\n<h6 class=\"$1\">$2</h6>\n", $text);
	
	$text = preg_replace('!\n\s*hr\s*\n!', "\n<hr>\n", $text);
	$text = preg_replace('!\n\s*hr\((.*?)\)\s*\n!', "\n<hr class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*div\n!', "\n<div>\n", $text);
	$text = preg_replace('!\n\s*\/div\n!', "\n</div>\n", $text);
	
	$text = preg_replace('!\n\s*div\((.*?)\)\((.*?)\)\n!', "\n<div class=\"$1\" style=\"$2\">\n", $text);
	$text = preg_replace('!\n\s*div\((.*?)\)\n!', "\n<div class=\"$1\">\n", $text);	
	
	$text = preg_replace('!\n\s*ul\n!', "\n<ul>\n", $text);
	$text = preg_replace('!\n\s*\/ul\n!', "\n</ul>\n", $text);
	$text = preg_replace('!\n\s*ul\((.*?)\)\n!', "\n<ul class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*ol\n!', "\n<ol>\n", $text);
	$text = preg_replace('!\n\s*\/ol\n!', "\n</ol>\n", $text);	
	$text = preg_replace('!\n\s*ol\((.*?)\)\n!', "\n<ol class=\"$1\">\n", $text);
	
	$text = preg_replace('!\n\s*\*\s(.*?)\n!', "\n<li>$1</li>\n", $text);
	$text = preg_replace('!\n\s*\*\s(.*?)\n!', "\n<li>$1</li>\n", $text);
	
	$text = preg_replace('!\n\s*bq\n!', "\n<blockquote>\n", $text);
	$text = preg_replace('!\n\s*\/bq\n!', "\n</blockquote>\n", $text);
	
	$text = preg_replace('!\n\s*bq\((.*?)\)\n!', "\n<blockquote class=\"$1\">\n", $text);
	
	$text = preg_replace('! __(.*?)__!', " <i>$1</i>", $text);
	$text = preg_replace('! _(.*?)_!', " <em>$1</em>", $text);
	
	$text = preg_replace('! \*\*(.*?)\*\*!', " <b>$1</b>", $text);
	$text = preg_replace('! \*(.*?)\*!', " <strong>$1</strong>", $text);
	
	$text = preg_replace('! \@(.*?)\@!', " <code>$1</code>", $text);

	
	return trim($text);
}

# end file